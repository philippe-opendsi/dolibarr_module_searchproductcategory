<?php


	require '../config.php';
	
	$get=GETPOST('get');
	$put=GETPOST('put');

	switch ($get) {
		case 'categories':
			$fk_parent = (int)GETPOST('fk_parent');
			$Tab = _categories($fk_parent);
			
			__out($Tab,'json');
					
			break;
	}

function _categories($fk_parent=0) {
	global $db;
	
	dol_include_once('/categories/class/categorie.class.php');
	
	$cat = new Categorie($db);
	if(empty($fk_parent)) {
		$cat->id=0;
		$TFille = $cat->get_all_categories(0,true);	
	}
	else {
		$cat->fetch($fk_parent);
		$TFille = $cat->get_filles();
	}
	
	
	return $TFille;
}
