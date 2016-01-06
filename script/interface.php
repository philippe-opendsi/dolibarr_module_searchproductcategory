<?php


	require '../config.php';
	dol_include_once('/categories/class/categorie.class.php');
	dol_include_once('/product/class/product.class.php');
	dol_include_once('/comm/propal/class/propal.class.php');
	
	$get=GETPOST('get');
	$put=GETPOST('put');

	switch ($get) {
		case 'categories':
			$fk_parent = (int)GETPOST('fk_parent');
			$Tab =array(
				'TCategory'=>_categories($fk_parent)
				,'TProduct'=>_products($fk_parent)
			);
			
			__out($Tab,'json');
					
			break;
	}
	
	switch ($put) {
		case 'addline':
			
			$object_type=GETPOST('object_type');
			$object_id=(int)GETPOST('object_id');
			$qty=(float)GETPOST('qty');
			$TProduct=GETPOST('TProduct');
			$txtva=(float)GETPOST('txtva');
			
			if(!empty($TProduct)) {
				//$o=new $object_type($db);
				$o=new Propal($db);
				$o->fetch($object_id);
				
				foreach($TProduct as $fk_product) {
					$p=new Product($db);
					$p->fetch($fk_product);
					
					$o->addline($p->description, $p->price, $qty, $txtva,0,0,$fk_product);
				}
				
				
			}
			
			echo 1;
			
			break;
		default:
			
			break;
	}

function _products($fk_parent=0) {
	global $db;

	if(empty($fk_parent)) return array();
	
	$parent = new Categorie($db);
	$parent->fetch($fk_parent);
	
	$TProd = $parent->getObjectsInCateg('product');
	
	return $TProd;
}

function _categories($fk_parent=0) {
	global $db;
	
	$parent = new Categorie($db);
	if(empty($fk_parent)) {
		$TFille = $parent->get_all_categories(0,true);	
	}
	else {
		$parent->fetch($fk_parent);
		$TFille = $parent->get_filles();
	}
	
	
	return $TFille;
}