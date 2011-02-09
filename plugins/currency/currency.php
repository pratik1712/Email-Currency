<?php
class currency extends rcube_plugin {
	public $task = 'mail';
	public $newval = null;
	public $new_user_default = 100;
	/**
	 *@purpose : to attach hooks, stylesheet and scripts 
	 *@arguments : NA
	 *@return : NA
	 */
	function init() {
		$rcmail = rcmail::get_instance();
		
		// Adding functionalities for compose window
		if($rcmail->action == 'compose') {      
     		$this->composemail_init();      
    	}

		// Adding stylesheet and scripts
		if (($rcmail->task == 'mail') && ($rcmail->action == '')) {
			$this->include_script('currency.js');
		}
		$this->include_stylesheet("currency.css");

		/**
		 * Adding hooks
		 */

		// Adding the Currency Selection in the HTML
		$this->add_hook('template_object_composesubject',array($this, 'compose_header_currency'));		
		

		//$this->add_hook('user_create',array($this, 'user_create_currency'));
		// Fetching the values form the header @ list view
		$this->add_hook('messages_list', array($this, 'message_list'));
		// Adding additional headers to be fetched
		$this->add_hook('imap_init', array($this, 'fetch_header'));
		// Adding additional headers before sending the mail
		$this->add_hook('message_outgoing_headers', array($this, 'message_headers'));
	}
	

	function composemail_init(){
		$rcmail = rcmail::get_instance();
	
		// Current user id
		$myid = $rcmail->user->ID;

		// Formulating query and fetching currency available
		$query = "SELECT curr_available FROM users WHERE user_id=?";
		$rcmail->db->query($query,$myid);
		$ret = $rcmail->db->fetch_assoc();

		// Currency available with the user
		$mycurrency = $ret['curr_available'];
		
		$rcmail->output->set_env('user_curr_avail', $mycurrency);
		
		// Environment Variable for storing the currency selected for the given mail
		$rcmail->output->set_env('user_curr_fixed', 0);		
		
		// Adding Script for compose window
		$this->include_script('currency_compose.js');
		
		// Adding Localization
		$this->add_texts('localization', true);

		// Displaying the value to the user
		/*
		$this->add_button(array(
		'command' => 'plugin.currencyleft',
		'label' => $this->gettext('currency.plugin_currencyleft') . ": " . $mycurrency,
		'title' => 'currency.plugin_currencyleft',
		'id' => 'rcmbtn_compose_currency'), 'toolbar');
		*/
		
	}
	
	function compose_header_currency($args){
		$new_div = '
			<tr id="compose-currency">
			<td class="title">Currency</td>
			<td class="editfield">
			<input type="radio" name="_currency" id="compose-currency-0" tabindex="9" value="0" checked = true> 0 </input>
			<input type="radio" name="_currency" id="compose-currency-1" tabindex="9" value="1"> 1 </input>
			<input type="radio" name="_currency" id="compose-currency-2" tabindex="9" value="2"> 2 </input>
			<input type="radio" name="_currency" id="compose-currency-3" tabindex="9" value="3"> 3 </input>
			<input type="radio" name="_currency" id="compose-currency-4" tabindex="9" value="4"> 4 </input>
			<input type="radio" name="_currency" id="compose-currency-5" tabindex="9" value="5"> 5 </input>
			</td>
			</tr>
			';		

		$div = $args['content'];
		$args['content'] = $div . $new_div ;
		
		return $args;
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
		$rcmail->output->set_env('user_curr_avail', $mycurrency);
		
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
				$rcmail = rcmail::get_instance();
				
				$myid = $rcmail->user->ID;
				// Formulating query and fetching currency available
				$query = "SELECT curr_available FROM users WHERE user_id=?";
				$rcmail->db->query($query,$myid);
				$ret = $rcmail->db->fetch_assoc();

				// Currency available with the user
				$mycurrency = $ret['curr_available'];
				
				$mycurrval = get_input_value('_currency',RCUBE_INPUT_POST);
				if($mycurrval == null)
					$mycurrval = 0;
				$recipient_list = rtrim(rtrim($args['headers']['To'], " "), ",");			

				$num_recipient = count(split("," , $recipient_list));			
					
				$newval = $mycurrency - ($mycurrval * $num_recipient);
				
				if($newval>=0){
					// Current user id
					$myid = $rcmail->user->ID;
					// Formulating query and fetching currency available
					$query = "UPDATE users SET curr_available = ? WHERE user_id=?";
					$rcmail->db->query($query,$newval,$myid);

						

					$args['headers'][$value] =  '' .$mycurrval ;
												//$rcmail->env->get['user_curr_fixed'];
												//$rcmail->env->user_curr_fixed;
				}
				else{
					$args['abort'] = true;
					$args['message']= 'You dont have enough currency to attach.. Reduce the attached amount';	
				}
			}
			else {
			unset($args['headers'][$header]);
			} 
		}
/*
		
*/
	return $args;
	}
	
	function fetch_header($p){
		$add_headers = array('Currency-Attached');
		$p['fetch_headers'] = trim($p['fetch_headers'].' ' . strtoupper(join(' ', $add_headers)));
		return $p;
	}  
}
