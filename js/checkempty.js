var checkempty_inputid, checkempty_errms;
function setCheckemptyVar(var1, var2) {
	checkempty_inputid = var1;
	checkempty_errms = var2;
}
function checkEmpty(e)
{
	if ($(checkempty_inputid).val() == "")
	{
		e.preventDefault();
		alert(checkempty_errms);
	}
}