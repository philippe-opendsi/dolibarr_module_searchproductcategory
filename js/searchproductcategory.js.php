<?php
	require '../config.php';

?>$(document).ready(function() {
	
	
	$search = $('<span> <a href="javascript:openSearchProductByCategory()"><?php echo img_picto($langs->trans('SearchByCategory'), 'object_searchproductcategory.png@searchproductcategory') ?></a></span>');
	
	if($('input#search_idprod').length>0) {
		
		$('input#search_idprod').after($search);
		
	}
	
});

function openSearchProductByCategory() {
	
	if($('div#popSearchProductByCategory').length == 0) {
		
		$('body').append('<div id="popSearchProductByCategory"><div class="arbo"></div></div>');
		$( "div#popSearchProductByCategory" ).dialog({
	      modal: true,
	      autoOpen: false,
	      title:"<?php echo $langs->trans('SearchByCategory'); ?>",
	      width:"80%",
	      buttons: {
	        "<?php echo $langs->trans('Cancel'); ?>": function() {
	          $( this ).dialog( "close" );
	        }
	      }
	    });
	    
	    initSearchProductByCategory();
	}
	
	$pop = $( "div#popSearchProductByCategory" );
	
	$pop.dialog('open');
	
}
function getArboSPC(fk_parent, container) {
	
	container.find('ul.tree').remove();
	
	$.ajax({
		url:"<?php echo dol_buildpath('/searchproductcategory/script/interface.php',1) ?>"
		,data:{
			get:"categories"
			,fk_parent:fk_parent
		}
		,dataType:'json'	
	}).done(function(data) {
		
		$ul = $('<ul class="tree" fk_parent="'+fk_parent+'"></ul>');
		
		$.each(data,function(i,item) {
			
			$ul.append('<li catid="'+item.id+'"><a href="javascript:getArboSPC('+item.id+', $(\'li[catid='+item.id+']\') )">'+item.label+'</a></li>');
		});
		
		container.append($ul);
	});
}
function initSearchProductByCategory() {
	
	$arbo = $( "div#popSearchProductByCategory div.arbo" );
	$arbo.html('<ul class="tree"><?php echo img_picto('', 'working.gif') ?></ul>');
	
	getArboSPC(0, $arbo);
}
