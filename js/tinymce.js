function ra_tinymce(adv) {
	if (adv) {
		tinyMCE.init({
			mode : "specific_textareas",
			editor_selector : "DGPDFormTinymceContent",
			theme : "advanced",
			language : "zh-tw",
			remove_linebreaks : false,
			apply_source_formatting : true,
			content_css : "tinymce/css/content.css",
			plugins : "searchreplace,",
			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,sub,sup,|,justifyleft,justifycenter,justifyright,justifyfull,|,fontsizeselect,forecolor,|,undo,redo",
			theme_advanced_buttons2 : "cut,copy,pastetext,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,|,cleanup,removeformat,code,",
			theme_advanced_buttons3 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_resizing : true,
		});
	} else {
		tinyMCE.init({
			mode : "specific_textareas",
			editor_selector : "DGPDFormTinymceContent",
			theme : "advanced",
			language : "zh-tw",
			remove_linebreaks : false,
			apply_source_formatting : true,
			content_css : "tinymce/css/content.css",
			plugins : "searchreplace,",
			theme_advanced_buttons1 : "",
			theme_advanced_buttons2 : "",
			theme_advanced_buttons3 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_resizing : true,
		});
	}
}