<?php
	require '../config.php';

?>$(document).ready(function() {
	
	
	$search = $('<span><a href="javascript:;" onclick="openSearchProductByCategory(this)"><?php echo img_picto($langs->trans('SearchByCategory'), 'object_searchproductcategory.png@searchproductcategory') ?></a></span>');
	
	if($('input#search_idprod').length>0) {
		
		$search.find('a').attr('related-label','input#search_idprod');
		$search.find('a').attr('related','input#idprod');
		
		$('input#search_idprod').after($search);
		
	}
	
});

function openSearchProductByCategory(a) {
	
	if($('div#popSearchProductByCategory').length == 0) {
		
		$('body').append('<div id="popSearchProductByCategory" class="arboContainer"><div class="arbo"></div></div>');
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
	$pop.attr('related', $(a).attr('related'));
	$pop.attr('related-label', $(a).attr('related-label'));
	
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
		
		if(data.TCategory.length == 0 && data.TProduct.length ==0) {
			$ul.append('<li class="none"><?php echo $langs->trans('NothingHere'); ?></li>');
		}
		else {
			$.each(data.TCategory,function(i,item) {
				$ul.append('<li class="category" catid="'+item.id+'"><a href="javascript:getArboSPC('+item.id+', $(\'li[catid='+item.id+']\') )">'+item.label+'</a></li>');
			});
			
			$.each(data.TProduct,function(i,item) {
				$ul.append('<li class="product" productid="'+item.id+'"><a href="javascript:;" onclick="addProductSPC('+item.id+',\''+item.label.replace(/\'/g, "&quot;")+'\')">'+item.label+'</a></li>');
			});
		}
		
		container.append($ul);
	});
}

function addProductSPC(fk_product,label) {
	
	var related = $('div.arboContainer').attr('related');
	$(related).val(fk_product);
	
	if(label) {
		var relatedLabel = $('div.arboContainer').attr('related-label');
		$(relatedLabel).val(label);
	}
	
	$pop = $( "div#popSearchProductByCategory" );
	$pop.dialog('close');
}

function initSearchProductByCategory() {
	
	$arbo = $( "div#popSearchProductByCategory div.arbo" );
	$arbo.html('<ul class="tree"><?php echo img_picto('', 'working.gif') ?></ul>');
	
	getArboSPC(0, $arbo);
}
