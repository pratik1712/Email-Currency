
rcmail.addEventListener('init', function(evt)
{
	document.getElementById('rcmcurrency').lastChild.data = 'Currency';
	var curr_avail = $('<A>').attr('id', 'rcmcurravailable').html(rcmail.gettext('Currency Available : ...'));
	rcmail.add_element(curr_avail, 'toolbar');
}
);

rcmail.addEventListener('listupdate', function(evt)
{
	document.getElementById('rcmcurravailable').lastChild.data = rcmail.gettext('Currency Available : ') + rcmail.env.user_curr_avail;
}
);

$(document).ready(function(){
	var li = '<li><input type="checkbox" name="list_col[]" value="currency" id="cols_currency" /><label for="cols_currency">'+rcmail.get_label('Currency')+'</label></li>';
	$("#listmenu fieldset ul input#cols_threads").parent().after(li);


	
});



