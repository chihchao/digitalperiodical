function accordiontree_clickAction(e) {
	accordiontree_openAction($(e.target));
}
function accordiontree_openAction(elem) {
	//set css
	$(elem).parent('ul').children('li').css('background','url(js/css/accordiontree/li_right.gif) no-repeat 0 10px');
	$(elem).css('background','url(js/css/accordiontree/li_down.gif) no-repeat 0 10px');
	$(elem).children('ul').children('li').css('background','url(js/css/accordiontree/li_right.gif) no-repeat 0 10px');
	//accordion
	$(elem).children('ul').children('li').children('ul').hide();
	$(elem).parent('ul').children('li').children('ul').hide('slow');
	$(elem).children('ul').show('slow');
	//bind other lis click action
	$(elem).parent('ul').children('li').unbind();
	$(elem).parent('ul').children('li').click(accordiontree_clickAction);
	//unbind this click action
	$(elem).unbind();
	//bind children click action
	$(elem).children('ul').children('li').unbind();
	$(elem).children('ul').children('li').click(accordiontree_clickAction);
}
function accordiontree_openDefault(elem) {
	//count router length
	var tmp_elem = elem;
	var counter = 1;
	while ($(tmp_elem).parent('ul').parent().attr('class') != 'Accordiontree') {
		counter ++;
		tmp_elem = $(tmp_elem).parent('ul').parent();
	}
	//set router path
	var path = new Array(counter);
	var i;
	tmp_elem = elem;
	for (i = counter - 1; i >= 0; i --) {
		path[i] = tmp_elem;
		tmp_elem = $(tmp_elem).parent('ul').parent();
	}
	//open the path
	for (i = 0; i < counter; i++) accordiontree_openAction(path[i]);
}
function ra_accordiontree() {
	$('.Accordiontree').children('ul').children('li').css('background','url(js/css/accordiontree/li_right.gif) no-repeat 0 10px');
	$('.Accordiontree').children('ul').children('li').children('ul').hide();
	$('.Accordiontree').children('ul').children('li').click(accordiontree_clickAction);
	//accordiontree_openAction($('.Accordiontree').children('ul').children('li'));
	$(".AccordiontreeNow").each(function(index,domelem) {
		accordiontree_openDefault(domelem);
	});
}