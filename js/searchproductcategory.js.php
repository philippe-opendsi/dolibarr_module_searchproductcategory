<?php
	require '../config.php';

	if(empty($user->rights->searchproductcategory->user->search)) exit;

	$langs->load('searchproductcategory@searchproductcategory');

?>
var spc_line_class = 'even';
$(document).ready(function() {
	$search = $('<span class="searchbycateg_icone"><a href="javascript:;" onclick="openSearchProductByCategory(this)"><?php echo img_picto($langs->trans('SearchByCategory'), 'object_searchproductcategory.png@searchproductcategory') ?></a></span>');
	
	if($('input#search_idprod').length>0 && $('input#search_idprod').next().attr('class') != 'searchbycateg_icone') {
		
		$search.find('a').attr('related-label','input#search_idprod');
		$search.find('a').attr('related','input#idprod');
		
		$('input#search_idprod').after($search);
		
	}
	else if($('select#idprod').length>0 && $('select#idprod').next().attr('class') != 'searchbycateg_icone') {
		
		$search.find('a').attr('related','select#idprod');
		$('select#idprod').after($search);
	
	}
	else if ($('#nomenclature_bt_add_product').length > 0 || $('#nomenclature_bt_clone_nomenclature').length > 0)
	{
		if ($('#nomenclature_bt_add_product').length > 0)
		{
			$search.find('a').attr('related-label','input[id*=search_fk_new_product_]');
			$search.find('a').attr('related','input[id*=fk_new_product][type=hidden]');

			$('#nomenclature_bt_add_product').before($search.clone());
		}
		
		if ($('#nomenclature_bt_clone_nomenclature').length > 0)
		{
			$search.find('a').attr('related-label','input[id*=search_fk_clone_from_product]');
			$search.find('a').attr('related','input[id*=fk_clone_from_product][type=hidden]');

			$('#nomenclature_bt_clone_nomenclature').before($search.clone());
		}
	}
	else {
		return false;
	}
	
	initSearchProductByCategory("div#arboresenceCategoryProduct");
	
	$('#addline_spc').click(function() {
		$(this).after('<span class="loading"><?php echo img_picto('', 'working.gif') ?></span>');
		$(this).hide();
		var TProduct={};
		var TProductPrice={};
		
		$('input.checkSPC:checked').each(function(i,item){
			var fk_product = $(item).attr('fk_product');
			TProduct[fk_product] = fk_product;
		});
		
		<?php if (!empty($conf->global->PRODUIT_MULTIPRICES)) { ?>
		$('input.radioSPC:checked').each(function(i,item){
			var priceToUse = $(item).val();
			TProductPrice[$(item).data('fk-product')] = priceToUse;
		});
		<?php } ?>
		
		$.ajax({
			url:"<?php echo dol_buildpath('/searchproductcategory/script/interface.php',1); ?>"
			,data:{
				put:"addline"
				,TProduct:TProduct
				,TProductPrice:TProductPrice
				,object_type:spc_object_type
				,object_id:spc_object_id
				,qty:$('#qty_spc').val()
				<?php if (!empty($conf->global->SUBTOTAL_ALLOW_ADD_LINE_UNDER_TITLE)) { ?>,under_title:$(this).closest('td').children('select.under_title').val()<?php } ?>
			}
			,method:'post'
			,dataType:'json'	
		}).done(function(data) {
			
			var url = window.location.href;
			
			url = url.replace(window.location.hash, "");
			window.location.href=url;
			
			return;
		});
		
	});
});

function openSearchProductByCategory(a) {
	
	if($('div#popSearchProductByCategory').length == 0) {
		
		$('body').append('<div id="popSearchProductByCategory" class="arboContainer" spc-role="arbo"><div class="arbo"></div></div>');
		$( "div#popSearchProductByCategory" ).dialog({
	      modal: true,
	      autoOpen: false,
	      title:"<?php echo $langs->transnoentities('SearchByCategory'); ?>",
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
function searchCategorySPC(a) {
	
	var keyword = $(a).prev('input[name=spc_keyword]').val();
	getArboSPC(0, $("div#arboresenceCategoryProduct,div#popSearchProductByCategory div.arbo"), keyword) ;
	
}
function getArboSPC(fk_parent, container,keyword) {
	
	container.find('ul.tree').remove();
	container.append('<span class="loading"><?php echo img_picto('', 'working.gif') ?></span>');
	
	$.ajax({
		url:"<?php echo dol_buildpath('/searchproductcategory/script/interface.php',1) ?>"
		,data:{
			get:"categories"
			,fk_parent:fk_parent
			,keyword:keyword
			,fk_soc:spc_fk_soc
		}
		,dataType:'json'	
	}).done(function(data) {
		
		$ul = $('<ul class="tree" fk_parent="'+fk_parent+'"></ul>');
		
		if(data.TCategory.length == 0 && data.TProduct.length ==0) {
			$ul.append('<li class="none '+spc_line_class+'"><?php 
				if(!empty($conf->global->SPC_DO_NOT_LOAD_PARENT_CAT)) {
					echo $langs->trans('DoASearch');						
				}
				else {
					echo $langs->trans('NothingHere');	
				}
			?></li>');
		}
		else {
			$.each(data.TCategory,function(i,item) {
				spc_line_class = (spc_line_class == 'even') ? 'odd' : 'even';
				$ul.append('<li class="category '+spc_line_class+'" catid="'+item.id+'"><a href="javascript:getArboSPC('+item.id+', $(\'li[catid='+item.id+']\') )">'+item.label+'</a></li>');
			});
			
			$.each(data.TProduct,function(i,item) {
				spc_line_class = (spc_line_class == 'even') ? 'odd' : 'even';
				
				var TRadioboxMultiPrice = '';
				<?php if (!empty($conf->global->PRODUIT_MULTIPRICES)) { ?>
					for (var p in item.multiprices) {
						if (item.multiprices_base_type[p] == 'TTC') var priceToUse = parseFloat(item.multiprices_ttc[p]);
						else var priceToUse = parseFloat(item.multiprices[p]);
						
						if (isNaN(priceToUse)) priceToUse = 0;
						
						var checked = false;
						if (data.default_price_level == p) checked = true;
						TRadioboxMultiPrice += '<span class="multiprice"><input '+(checked ? "checked" : "")+' class="radioSPC" type="radio" name="TProductSPCPriceToAdd['+item.id+']" value="'+priceToUse+'" data-fk-product="'+item.id+'" style="vertical-align:bottom;" /> ' + priceToUse.toFixed(2) + '</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					}
				<?php } ?>
				
				$li = $('<li class="product '+spc_line_class+'" productid="'+item.id+'"><input type="checkbox" value="1" name="TProductSPCtoAdd['+item.id+']" fk_product="'+item.id+'" class="checkSPC" /> <a class="checkIt" href="javascript:;" onclick="checkProductSPC('+item.id+')" >'+item.label+'</a> <a class="addToForm" href="javascript:;" onclick="addProductSPC('+item.id+',\''+item.label.replace(/\'/g, "&quot;")+'\', \''+item.ref+'\')"><?php echo img_right($langs->trans('SelectThisProduct')) ?></a> '+TRadioboxMultiPrice+' </li>');
				
				<?php if (!empty($conf->global->SPC_DISPLAY_DESC_OF_PRODUCT)) { ?>
					var desc = item.description.replace(/'/g, "\\'");
				
				<?php 	if(!empty($conf->global->PRODUCT_USE_UNITS)){ ?>
						desc = desc + "\n Unit : "+item.unit;
				<?php } ?>
					var bubble = $("<?php echo addslashes(img_help()); ?>");
					bubble.attr('title', desc);
					
					$li.append(bubble);
				<?php } else if (!empty($conf->global->PRODUCT_USE_UNITS)) { ?>
					var unit = "Unit : "+item.unit;
					var bubble = $("<?php echo addslashes(img_help()); ?>");
					bubble.attr('title', unit);
					$li.append(bubble);
				<?php } ?>
				
				$ul.append($li);
			});
		}
		
		container.find('span.loading').remove();
		container.append($ul);
		
		$('#arboresenceCategoryProduct').find('a.addToForm').remove();
		$("div#popSearchProductByCategory").find('input[type=checkbox], span.multiprice').remove();
		
		var TCheckIt = $("div#popSearchProductByCategory").find('a.checkIt');
		for (var j=0; j < TCheckIt.length; j++)
		{
			$(TCheckIt[j]).attr('onclick', $(TCheckIt[j]).next('a.addToForm').attr('onclick'));
		}
	});
}

function checkProductSPC(fk_product) {
	if( $('input[name="TProductSPCtoAdd['+fk_product+']"]').is(':checked') ) {
		$('input[name="TProductSPCtoAdd['+fk_product+']"]').prop('checked',false);
	}
	else {
		$('input[name="TProductSPCtoAdd['+fk_product+']"]').prop('checked',true);	
	}
	
}

function addProductSPC(fk_product,label,ref) {
	
	var related = $('div.arboContainer').attr('related');
	$(related).val(fk_product);
	$('#prod_entry_mode_predef').prop('checked',true);
	$('#prod_entry_mode_predef').click();	

	if(label) {
		var relatedLabel = $('div.arboContainer').attr('related-label');
		if (typeof ref != 'undefined') $(relatedLabel).val(ref);
		else $(relatedLabel).val(label);
		
		$('#idprod').trigger('change');
	}
	
	$pop = $( "div#popSearchProductByCategory" );
	$pop.dialog('close');
}

function initSearchProductByCategory(selector) {
	
	$arbo = $( selector );
	$arbo.html();
	$arbo.append('<div><input type="text" value="" name="spc_keyword" size="10" /> <a href="javascript:;" onclick="searchCategorySPC(this)"><?php echo img_picto('','search'); ?></a></div>');
	$arbo.append('<ul class="tree"><?php echo img_picto('', 'working.gif') ?></ul>');
	
	getArboSPC(0, $arbo);
}
