function ra_datepicker() {
	var dp_value = new Array($( ".DGPDDatepicker" ).length);
	$(".DGPDDatepicker").each(function(index) {
		dp_value[index] = $(this).val();
	});
	$(".DGPDDatepicker").datepicker();
	$(".DGPDDatepicker").datepicker( "option", "dateFormat", "yy-mm-dd" );
	$(".DGPDDatepicker").datepicker($.datepicker.regional["zh-TW"]);
	$(".DGPDDatepicker").each(function(index) {
		$(this).datepicker( "setDate", dp_value[index]);
	});
}