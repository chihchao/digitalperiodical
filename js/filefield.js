var filefield_select, filefield_description;
function setFilefieldVar(var1, var2) {
	filefield_select = var1;
	filefield_description = var2;
}
function addFileField(e) { $('#DGPDFormFilefield').append('<li>' + filefield_select + '<input type="file" name="file[]" size="10" /><br />' + filefield_description + '<input type="text" name="description[]" value="" /></li>'); }