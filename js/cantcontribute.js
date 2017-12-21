var cantcontribute_errms;
function setCantcontributeVar(var1) {
	cantcontribute_errms = var1;
}
function checkCategory(e) {
	$(".DGPDFormArticlecidCant").each(function(index) {
		if ($("#DGPDFormArticlecid").val() == $(this).val())
		{
			e.preventDefault();
			alert(cantcontribute_errms);
		}
	});
}