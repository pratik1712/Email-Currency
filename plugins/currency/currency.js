
rcmail.addEventListener('init', function(evt)
{
	document.getElementById('rcmcurrency').lastChild.data = 'Currency';
}
);

rcmail.addEventListener('listupdate', function(evt)
{
	var curr_avail = $('<A>').attr('id', 'rcmcurravailable').html('Currency Available : ' + rcmail.gettext(rcmail.env.user_curr_avail));
	rcmail.add_element(curr_avail, 'toolbar');
	

}
);

$(document).ready(function(){
	var li = '<li><input type="checkbox" name="list_col[]" value="currency" id="cols_currency" /><label for="cols_currency">'+rcmail.get_label('Currency')+'</label></li>';
	$("#listmenu fieldset ul input#cols_threads").parent().after(li);


	
});



