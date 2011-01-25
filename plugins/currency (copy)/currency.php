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
  }

  function message_list($args){
    
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


}
