<?php
	require '../config.php';

	if(empty($user->rights->searchproductcategory->user->search)) exit;

?>
var spc_line_class = 'even';
$(document).ready(function() {
	
	$search = $('<span><a href="javascript:;" onclick="openSearchProductByCategory(this)"><?php echo img_picto($langs->trans('SearchByCategory'), 'object_searchproductcategory.png@searchproductcategory') ?></a></span>');
	
	if($('input#search_idprod').length>0) {
		
		$search.find('a').attr('related-label','input#search_idprod');
		$search.find('a').attr('related','input#idprod');
		
		$('input#search_idprod').after($search);
		
	}
	
	initSearchProductByCategory("div#arboresenceCategoryProduct");
	
	$('#addline_spc').click(function() {
		$(this).after('<span class="loading"><?php echo img_picto('', 'working.gif') ?></span>');
		$(this).hide();
		
		TProduct=[];
		
		$.ajax({
			url:"<?php echo dol_buildpath('/searchproductcategory/script/interface.php',1) ?>"
			,data:{
				put:"addline"
				,TProduct:TProduct
			}
			,dataType:'json'	
		}).done(function(data) {
			
			document.location.href=document.location.href;
			
		});
	
	
});

function openSearchProductByCategory(a) {
	
	if($('div#popSearchProductByCategory').length == 0) {
		
		$('body').append('<div id="popSearchProductByCategory" class="arboContainer" spc-role="arbo"><div class="arbo"></div></div>');
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
	    
	    initSearchProductByCategory("div#popSearchProductByCategory div.arbo");
	}
	
	$pop = $( "div#popSearchProductByCategory" );
	$pop.attr('related', $(a).attr('related'));
	$pop.attr('related-label', $(a).attr('related-label'));
	
	$pop.dialog('open');
	
}
function getArboSPC(fk_parent, container) {
	
	container.find('ul.tree').remove();
	container.append('<span class="loading"><?php echo img_picto('', 'working.gif') ?></span>');
	
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
			$ul.append('<li class="none '+spc_line_class+'"><?php echo $langs->trans('NothingHere'); ?></li>');
		}
		else {
			$.each(data.TCategory,function(i,item) {
				spc_line_class = (spc_line_class == 'even') ? 'odd' : 'even';
				$ul.append('<li class="category '+spc_line_class+'" catid="'+item.id+'"><a href="javascript:getArboSPC('+item.id+', $(\'li[catid='+item.id+']\') )">'+item.label+'</a></li>');
			});
			
			$.each(data.TProduct,function(i,item) {
				spc_line_class = (spc_line_class == 'even') ? 'odd' : 'even';
				$li = $('<li class="product '+spc_line_class+'" productid="'+item.id+'"><input type="checkbox" value="1" name="TProductSPCtoAdd['+item.id+']" /> <a href="javascript:checkProductSPC('+item.id+')" >'+item.label+'</a> <a class="addToForm" href="javascript:;" onclick="addProductSPC('+item.id+',\''+item.label.replace(/\'/g, "&quot;")+'\')"><?php echo img_right($langs->trans('SelectThisProduct')) ?></a></li>');
				$ul.append($li);
			});
		}
		
		container.find('span.loading').remove();
		container.append($ul);
		
		$('#arboresenceCategoryProduct').find('a.addToForm').remove();
	});
}

function checkProductSPC(fk_product) {
	$('input[name="TProductSPCtoAdd['+fk_product+']"]').prop('checked',true);
}

function addProductSPC(fk_product,label) {
	
	var related = $('div.arboContainer').attr('related');
	$(related).val(fk_product);
	$('#prod_entry_mode_predef').attr('checked','checked');
	
	if(label) {
		var relatedLabel = $('div.arboContainer').attr('related-label');
		$(relatedLabel).val(label);
	}
	
	$pop = $( "div#popSearchProductByCategory" );
	$pop.dialog('close');
}

function initSearchProductByCategory(selector) {
	
	$arbo = $( selector );
	$arbo.html('<ul class="tree"><?php echo img_picto('', 'working.gif') ?></ul>');
	
	getArboSPC(0, $arbo);
}
