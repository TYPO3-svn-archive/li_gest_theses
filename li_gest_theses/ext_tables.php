<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

// Ajout de champs dans le formulaire de la table tx_ligestmembrelabo_MembreDuLabo

$tempColumns = Array (
	"Afficher_theses_hdr" => Array (		
		"exclude" => 1,
		"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligestmembrelabo_MembreDuLabo.afficher_theses_hdr",
		"config" => Array (
			"type" => "select",
			"foreign_table" => "tx_ligesttheses_TheseHDR",	
			"foreign_table_where" => "AND tx_ligesttheses_TheseHDR.idMembreLabo=###THIS_UID### AND tx_ligesttheses_TheseHDR.sys_language_uid=0 ORDER BY tx_ligesttheses_TheseHDR.DateDebut DESC",
			"size" => 6,
			"minitems" => 0,
			"maxitems" => 1,
			"wizards" => Array(
				"_PADDING" => 2,
				"_VERTICAL" => 1,
				"add" => Array(
					"type" => "popup",
					"title" => "Create new record",
					"notNewRecords" => 1,
					"script" => t3lib_extMgm::extRelPath("li_gest_membre_labo")."wizard/add.php",
					"icon" => "add.gif",
					"params" => Array(
						"table"			=> "tx_ligesttheses_TheseHDR",
						"champ"			=> "idMembreLabo"
					),
					"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
				),
				"edit" => Array(
					"type" => "popup",
					"title" => "Edit",
					"script" => "wizard_edit.php",
					"notNewRecords" => 1,
					"popup_onlyOpenIfSelected" => 1,
					"icon" => "edit2.gif",
					"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
				),
				"del" => Array(
					"title" => "Delete record",
					"type" => "popup",
					"notNewRecords" => 1,
					"icon" => "clearout.gif",
					"popup_onlyOpenIfSelected" => 1,
					'params' => Array(
						'table'=>'tx_ligesttheses_TheseHDR',
					),
					"script" => t3lib_extMgm::extRelPath("li_gest_membre_labo")."wizard/delete.php",
					"JSopenParams" => "height=1,width=1,status=0,menubar=0,scrollbars=1",
				),
				"reload" => Array(
					"title" => "Refresh",
					"type" => "popup",
					"notNewRecords" => 1,
					"icon" => "refresh_n.gif",
					"script" => t3lib_extMgm::extRelPath("li_gest_membre_labo")."wizard/reload.php",
					"JSopenParams" => "height=1,width=1,status=0,menubar=0,scrollbars=1",
				),
			),
		),
	),
	"Afficher_theses_hdr_dirigees" => Array (		
		"exclude" => 1,
		"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligestmembrelabo_MembreDuLabo.afficher_theses_dirigees_hdr",		
		"config" => Array (
			"type" => "select",
			"foreign_table" => "tx_ligesttheses_Dirige",	
			"foreign_table_where" => "AND tx_ligesttheses_Dirige.idMembreLabo=###THIS_UID### ORDER BY tx_ligesttheses_Dirige.DateDebut DESC",
			"size" => 6,
			"minitems" => 0,
			"maxitems" => 1,
			"wizards" => Array(
				"_PADDING" => 2,
				"_VERTICAL" => 1,
				"add" => Array(
					"type" => "popup",
					"title" => "Create new record",
					"notNewRecords" => 1,
					"script" => t3lib_extMgm::extRelPath("li_gest_membre_labo")."wizard/add.php",
					"icon" => "add.gif",
					"params" => Array(
						"table"			=> "tx_ligesttheses_Dirige",
						"champ"			=> "idMembreLabo",
						"lien"			=> Array('tx_ligesttheses_TheseHDR')
					),
					"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
				),
				"edit" => Array(
					"type" => "popup",
					"title" => "Edit",
					"notNewRecords" => 1,
					"script" => "wizard_edit.php",
					"popup_onlyOpenIfSelected" => 1,
					"icon" => "edit2.gif",
					"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
				),
				"del" => Array(
					"title" => "Delete record",
					"type" => "popup",
					"notNewRecords" => 1,
					"icon" => "clearout.gif",
					"popup_onlyOpenIfSelected" => 1,
					'params' => Array(
						'table'=>'tx_ligesttheses_Dirige',
					),
					"script" => t3lib_extMgm::extRelPath("li_gest_membre_labo")."wizard/delete.php",
					"JSopenParams" => "height=1,width=1,status=0,menubar=0,scrollbars=1",
				),
				"reload" => Array(
					"title" => "Refresh",
					"type" => "popup",
					"notNewRecords" => 1,
					"icon" => "refresh_n.gif",
					"script" => t3lib_extMgm::extRelPath("li_gest_membre_labo")."wizard/reload.php",
					"JSopenParams" => "height=1,width=1,status=0,menubar=0,scrollbars=1",
				),
			),
		),
	),
);


t3lib_div::loadTCA("tx_ligestmembrelabo_MembreDuLabo");
t3lib_extMgm::addTCAcolumns("tx_ligestmembrelabo_MembreDuLabo",$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes("tx_ligestmembrelabo_MembreDuLabo","Afficher_theses_hdr,Afficher_theses_hdr_dirigees;;;;1-1-1");






// Paramétrage de l'affichage de listes d'enregistrement de la table tx_ligesttheses_TheseHDR dans le backend.

// allow TheseHDR records on normal pages
t3lib_extMgm::allowTableOnStandardPages('tx_ligesttheses_TheseHDR');
// add the TheseHDR record to the insert records content element
t3lib_extMgm::addToInsertRecords('tx_ligesttheses_TheseHDR');

$TCA["tx_ligesttheses_TheseHDR"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_TheseHDR',	
		'label'     => 'Titre, idMembreLabo',
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
		"fe_admin_fieldList" => "hidden, Titre, Resume, DateDebut, DateSoutenance, Jury, Resultat, Type, idMembreLabo, Afficher_Dirige, Afficher_Cotutelle",
	)
);

// Paramétrage de l'affichage de listes d'enregistrement de la table tx_ligesttheses_Dirige dans le backend.

// allow Dirige records on normal pages
//t3lib_extMgm::allowTableOnStandardPages('tx_ligesttheses_Dirige');
// add the Dirige record to the insert records content element
//t3lib_extMgm::addToInsertRecords('tx_ligesttheses_Dirige');

$TCA["tx_ligesttheses_Dirige"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_Dirige',
		'label'     => 'idTheseHDR, idMembreLabo',
		'label_alt' => 'idTheseHDR, idMembreLabo',
		'label_alt_force' => '1',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => "ORDER BY idTheseHDR, idMembreLabo",	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_ligesttheses_Dirige.gif',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "hidden, idTheseHDR, idMembreLabo, CoefficientDEncadrement, EstDirecteur, DateDebut, DateFin",
	)
);


// Paramétrage de l'affichage de listes d'enregistrement de la table tx_ligesttheses_Cotutelle dans le backend.

// allow TheseHDR records on normal pages
//t3lib_extMgm::allowTableOnStandardPages('tx_ligesttheses_Cotutelle');
// add the TheseHDR record to the insert records content element
//t3lib_extMgm::addToInsertRecords('tx_ligesttheses_Cotutelle');

$TCA["tx_ligesttheses_Cotutelle"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_Cotutelle',
		'label'     => 'idTheseHDR, NomCotutelle, PrenomCotutelle',
		'label_alt' => 'idTheseHDR, NomCotutelle, PrenomCotutelle',	
		'label_alt_force' => '1',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => "ORDER BY idTheseHDR, NomCotutelle, PrenomCotutelle",	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_ligesttheses_Cotutelle.gif',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "hidden, idTheseHDR, NomCotutelle, PrenomCotutelle, AdresseCotutelle",
	)
);





// load tt_content to $TCA array
t3lib_div::loadTCA('tt_content');

// remove some fields from the tt_content content element
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';

// add FlexForm field to tt_content
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';


// add li_gest_theses to the "insert plugin" content element
t3lib_extMgm::addPlugin(array('LLL:EXT:li_gest_theses/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');

// initialize static extension templates
t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","Managing Thesis/HDR");



// initalize "context sensitive help" (csh)
t3lib_extMgm::addLLrefForTCAdescr('tx_ligestmembrelabo_MembreDuLabo','EXT:li_gest_theses/csh/ligesttheses_locallang_csh_tx_ligestmembrelabo_MembreDuLabo.xml');
t3lib_extMgm::addLLrefForTCAdescr('tx_ligesttheses_TheseHDR','EXT:li_gest_theses/csh/ligesttheses_locallang_csh_tx_ligesttheses_TheseHDR.xml');
t3lib_extMgm::addLLrefForTCAdescr('tx_ligesttheses_Dirige','EXT:li_gest_theses/csh/ligesttheses_locallang_csh_tx_ligesttheses_Dirige.xml');
t3lib_extMgm::addLLrefForTCAdescr('tx_ligesttheses_Cotutelle','EXT:li_gest_theses/csh/ligesttheses_locallang_csh_tx_ligesttheses_Cotutelle.xml');


// switch the XML files for the FlexForm
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:li_gest_theses/flexform_ds_pi1.xml');



if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_ligesttheses_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_ligesttheses_pi1_wizicon.php';
?>