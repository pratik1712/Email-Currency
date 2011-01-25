<?php


class currency extends rcube_plugin {
  public $task = 'mail';

  function init() {
    $rcmail = rcmail::get_instance();
    if (($rcmail->task == 'mail') && ($rcmail->action == '')) {
      $this->include_script('currency.js');
    }
    $this->add_hook('messages_list', array($this, 'message_list'));
    $this->include_stylesheet("currency.css");
	
	$this->add_hook('imap_init', array($this, 'fetch_header'));	

	//Headers
	 $this->add_hook('message_outgoing_headers', array($this, 'message_headers'));
  }

  function message_list($args){


	if(is_array($args['messages'])){
      foreach($args['messages'] as $message){  
        if($message->others['currency-attached']){
          $message->list_flags['extra_flags']['curre'] = 1;
        }
      }
    }
    
		$count = count($args['messages']);

// Getting the Remaining Currency from the Server
		$rcmail = rcmail::get_instance();
		$myid = $rcmail->user->ID;
		$query = "SELECT curr_available FROM users WHERE user_id=?";
		$rcmail->db->query($query,$myid);
		$ret = $rcmail->db->fetch_assoc();
		$mycurrency = $ret['curr_available'];
		

    for ($i=0;$i<$count;$i++) {
      $uid = $args['messages'][$i]->uid;
      if(!empty($uid))
        $args['messages'][$i]->list_cols['currency'] = '<input type="button" value="'.$mycurrency.'" disabled="disabled" name="rcmcurrency'.$uid.'" id="rcmcurrency'.$uid.'" />';
    }
    return $args;
  }

// Setting the Header
	function message_headers($args)
    {
	$this->load_config();

        // additional email headers
        $additional_headers = rcmail::get_instance()->config->get('list_headers',array());
        foreach($additional_headers as $header=>$value){
			if('Currency-Attached' === $value){
				$args['headers'][$value] = 'Currency Value';
			}
            else {
                unset($args['headers'][$header]);
            } 
        }

        return $args;
    }

  function fetch_header($p){
    $add_headers = array('Currency-Attached');
    $p['fetch_headers'] = trim($p['fetch_headers'].' ' . strtoupper(join(' ', $add_headers)));
    return $p;
  }  
}
