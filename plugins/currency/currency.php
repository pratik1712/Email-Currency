<?php

class currency extends rcube_plugin {
	public $task = 'mail';
	public $newval = null;
	/**
	 *@purpose : to attach hooks, stylesheet and scripts 
	 *@arguments : NA
	 *@return : NA
	 */
	function init() {
		$rcmail = rcmail::get_instance();

		// Adding stylesheet and scripts

		if (($rcmail->task == 'mail') && ($rcmail->action == '')) {
			$this->include_script('currency.js');
		}
		$this->include_stylesheet("currency.css");

		// Adding hooks
		
		// Fetching the values form the header @ list view
		$this->add_hook('messages_list', array($this, 'message_list'));
		// Adding additional headers to be fetched
		$this->add_hook('imap_init', array($this, 'fetch_header'));
		// Adding additional headers before sending the mail
		$this->add_hook('message_outgoing_headers', array($this, 'message_headers'));
	}


	function message_list($args){
		// Number of messages
		$count = count($args['messages']);
		$rcmail = rcmail::get_instance();

		// Current user id
		$myid = $rcmail->user->ID;
		// Formulating query and fetching currency available
		$query = "SELECT curr_available FROM users WHERE user_id=?";
		$rcmail->db->query($query,$myid);
		$ret = $rcmail->db->fetch_assoc();

		// Currency available with the user
		$mycurrency = $ret['curr_available'];
		
		for ($i=0;$i<$count;$i++) {
			$newval = null;
			// Getting uid for all the messages
			$uid = $args['messages'][$i]->uid;
			if(!empty($uid))
				// Displaying the value in the list of messages
				if($args['messages'][$i]->others['currency-attached'])
					$newval = $args['messages'][$i]->others['currency-attached'];

				$args['messages'][$i]->list_cols['currency'] = '<input type="button" value="'.$newval.'" disabled="disabled" name="rcmcurrency'.$uid.'" id="rcmcurrency'.$uid.'" />';
		}
		return $args;
	}



	function message_headers($args){
		$this->load_config();
		// Getting the list of additional headers to be fetched from config		
		$additional_headers = rcmail::get_instance()->config->get('list_headers',array());
		foreach($additional_headers as $header=>$value){
			if('Currency-Attached' === $value){
			$args['headers'][$value] = 'My Currency';
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
