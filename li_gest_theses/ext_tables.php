<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TYPO3_CONF_VARS['SC_OPTIONS']['tce']['formevals']['tx_ligesttheses_dateValide'] = 'EXT:li_gest_theses/class.tx_ligesttheses_dateValide.php';
$TYPO3_CONF_VARS['SC_OPTIONS']['tce']['formevals']['tx_ligesttheses_dateObligatoire'] = 'EXT:li_gest_theses/class.tx_ligesttheses_dateObligatoire.php';



$tempColumns = Array (
	"afficher_theses_hdr" => Array (		
		"exclude" => 1,
		"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligestmembrelabo_MembreDuLabo.afficher_theses_hdr",		
		"config" => Array (
			"type" => "select",
			"foreign_table" => "tx_ligesttheses_TheseHDR",	
			//"foreign_table_where" => "AND tx_ligesttheses_TheseHDR.idMembreLabo=###THIS_UID### AND tx_ligesttheses_TheseHDR.sys_language_uid=0 ORDER BY tx_ligesttheses_TheseHDR.DateDebut DESC",
			"foreign_table_where" => "AND tx_ligesttheses_TheseHDR.idMembreLabo=###THIS_UID### ORDER BY tx_ligesttheses_TheseHDR.DateDebut DESC",
			"size" => 1,
			"minitems" => 0,
			"maxitems" => 1,
			"wizards" => Array(
				"_PADDING" => 2,
				"_VERTICAL" => 1,
				"add" => Array(
					"type" => "popup",
					"title" => "Create new record",
					"icon" => "add.gif",
					"params" => Array(
						"table"=>"tx_ligesttheses_TheseHDR",
						'pid' => '###CURRENT_PID###',
						"setValue" => "prepend"
					),
					"script" => "wizard_add.php",
					"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
				),
				"edit" => Array(
					"type" => "popup",
					"title" => "Edit",
					"script" => "wizard_edit.php",
					"popup_onlyOpenIfSelected" => 1,
					"icon" => "edit2.gif",
					"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
				),
			),
		),
	),
	"afficher_theses_dirigees_hdr" => Array (		
		"exclude" => 1,
		"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligestmembrelabo_MembreDuLabo.afficher_theses_dirigees_hdr",		
		"config" => Array (
			"type" => "select",
			"foreign_table" => "tx_ligesttheses_Dirige",	
			"foreign_table_where" => "AND tx_ligesttheses_Dirige.idMembreLabo=###THIS_UID### ORDER BY tx_ligesttheses_Dirige.DateDebut DESC",
			"size" => 1,
			"minitems" => 0,
			"maxitems" => 1,
			"wizards" => Array(
				"_PADDING" => 2,
				"_VERTICAL" => 1,
				"add" => Array(
					"type" => "popup",
					"title" => "Create new record",
					"icon" => "add.gif",
					"params" => Array(
						"table"=>"tx_ligesttheses_Dirige",
						'pid' => '###CURRENT_PID###',
						"setValue" => "prepend"
					),
					"script" => "wizard_add.php",
					"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
				),
				"edit" => Array(
					"type" => "popup",
					"title" => "Edit",
					"script" => "wizard_edit.php",
					"popup_onlyOpenIfSelected" => 1,
					"icon" => "edit2.gif",
					"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
				),
			),
		),
	),	
);







t3lib_div::loadTCA("tx_ligestmembrelabo_MembreDuLabo");
t3lib_extMgm::addTCAcolumns("tx_ligestmembrelabo_MembreDuLabo",$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes("tx_ligestmembrelabo_MembreDuLabo","afficher_theses_hdr,afficher_theses_dirigees_hdr;;;;1-1-1");

$TCA["tx_ligesttheses_TheseHDR"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_TheseHDR',		
		'label'     => 'uid',
		'label_alt' => 'Titre, idMembreLabo',
		'label_alt_force' => '1',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => "ORDER BY Titre",
		
		'copyAfterDuplFields' => 'sys_language_uid',
		'useColumnsForDefaultValues' => 'sys_language_uid',
		'transOrigPointerField' => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		'languageField' => 'sys_language_uid',
		'shadowColumnsForNewPlaceholders' => 'sys_language_uid,l18n_parent',
		
		'delete' => 'deleted',	
		'enablecolumns' => array (
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_ligesttheses_TheseHDR.gif',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "hidden, Titre, Description, DateDebut, DateFin, Jury, Resultat, Type, idMembreLabo",
	)
);

$TCA["tx_ligesttheses_Dirige"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_Dirige',		
		'label'     => 'uid',
		'label_alt' => 'idMembreLabo, idTheseHDR',
		'label_alt_force' => '1',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => "ORDER BY idMembreLabo, idTheseHDR",	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_ligesttheses_Dirige.gif',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "hidden, idMembreLabo, idTheseHDR, CoefficientDEncadrement, EstDirecteur, DateDebut, DateFin",
	)
);


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';


$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform'; //Ajouté



t3lib_extMgm::addPlugin(array('LLL:EXT:li_gest_theses/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');


t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","Managing Thesis/HDR");

t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:li_gest_theses/flexform_ds_pi1.xml'); //Ajouté



if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_ligesttheses_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_ligesttheses_pi1_wizicon.php';
?>