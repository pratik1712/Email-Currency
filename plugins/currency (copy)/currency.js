/*
 * Check box plugin
 * @version 0.2.1
 * 
 */



rcmail.addEventListener('init', function(evt)
{
  document.getElementById('rcmcurrency').lastChild.data = 'Currency';
}
);

$(document).ready(function(){
  var li = '<li><input type="checkbox" name="list_col[]" value="currency" id="cols_currency" /><label for="cols_currency">'+rcmail.get_label('Currency')+'</label></li>';

  $("#listmenu fieldset ul input#cols_threads").parent().after(li);
});



