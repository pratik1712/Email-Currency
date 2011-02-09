$(document).ready(function(){
	var curr_avail = $('<A>').attr('id', 'rcmcurravail').html(rcmail.gettext('Currency Available : ') + rcmail.env.user_curr_avail );
	$("#composemenulink").after(curr_avail);
/*
	$("#rcmbtn110").click(function(){
		var ab = $('input:radio[name=_currency]:checked').val();
		rcmail.env.user_curr_fixed = ab;
		alert(rcmail.env.user_curr_fixed);
	}); 
*/
});
