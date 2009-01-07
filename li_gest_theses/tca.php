<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_ligesttheses_TheseHDR"] = array (
	"ctrl" => $TCA["tx_ligesttheses_TheseHDR"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "hidden,Titre,Description,DateDebut,DateFin,Jury,Resultat,Type,idMembreLabo,Afficher_Dirige"
	),
	"feInterface" => $TCA["tx_ligesttheses_TheseHDR"]["feInterface"],
	"columns" => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		"Titre" => Array (		
			"exclude" => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_TheseHDR.titre",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"max" => "255",	
				"eval" => "required,trim",
			)
		),
		"Description" => Array (		
			"exclude" => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_TheseHDR.description",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",	
				"rows" => "15",
			)
		),
		"DateDebut" => Array (		
			"exclude" => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_TheseHDR.datedebut",		
			"config" => Array (
				"type"     => "input",
				"size"     => "10",
				"max"      => "10",
				"eval"     => "required,trim,tx_ligestmembrelabo_dateValide,tx_ligestmembrelabo_dateObligatoire",
				'default' => '0000-00-00'
			)
		),
		"DateFin" => Array (		
			"exclude" => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_TheseHDR.datefin",		
			"config" => Array (
				"type"     => "input",
				"size"     => "10",
				"max"      => "10",
				"eval"     => "trim,tx_ligestmembrelabo_dateValide",
				"default"  => "0"
			)
		),
		"Jury" => Array (		
			"exclude" => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_TheseHDR.jury",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"max" => "255",	
				"eval" => "trim",
			)
		),
		"Resultat" => Array (		
			"exclude" => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_TheseHDR.resultat",			
			'config' => Array (
				'type' => 'select',
				'size' => 1,
				'maxitems' => 1,
				'items' => Array (
					Array('LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_TheseHDR.resultat.E', 'E'),
					Array('LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_TheseHDR.resultat.A', 'A'),
					Array('LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_TheseHDR.resultat.H', 'H'),
					Array('LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_TheseHDR.resultat.T', 'T'),
					Array('LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_TheseHDR.resultat.F', 'F'),
				),
				 'default' => 'E',
			)
		),
		"Type" => Array (		
			"exclude" => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_TheseHDR.type",			
			'config' => Array (
				'type' => 'select',
				'size' => 1,
				'maxitems' => 1,
				'items' => Array (
					Array('LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_TheseHDR.type.these', 'T'),
					Array('LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_TheseHDR.type.hdr', 'H'),
				),
				 'default' => 'T',
			)
		),
		"idMembreLabo" => Array (		
			"exclude" => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_TheseHDR.idmembrelabo",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_ligestmembrelabo_MembreDuLabo",	
				"foreign_table_where" => "ORDER BY tx_ligestmembrelabo_MembreDuLabo.NomDUsage, tx_ligestmembrelabo_MembreDuLabo.Prenom",		
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		'sys_language_uid' => array (		
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l18n_parent' => array (
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
				),
				'foreign_table'       => 'tx_ligesttheses_TheseHDR',
				'foreign_table_where' => 'AND tx_ligesttheses_TheseHDR.pid=###CURRENT_PID### AND tx_ligesttheses_TheseHDR.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => array (		
			'config' => array (
				'type' => 'passthrough'
			)
		),
		"Afficher_Dirige" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_TheseHDR.afficherdirige",		
			"config" => Array (
				"type" => "select",
				"foreign_table" => "tx_ligesttheses_Dirige",	
				"foreign_table_where" => "AND tx_ligesttheses_Dirige.idTheseHDR=###THIS_UID### ORDER BY tx_ligesttheses_Dirige.DateDebut DESC",
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
	),
	"types" => array (
		"0" => array("showitem" => "hidden;;1;;1-1-1, sys_language_uid, l18n_parent, l18n_diffsource, Titre, Description, DateDebut, DateFin, Jury, Resultat, Type, idMembreLabo,Afficher_Dirige")
	),
	"palettes" => array (
		"1" => array("showitem" => "")
	)
);



$TCA["tx_ligesttheses_Dirige"] = array (
	"ctrl" => $TCA["tx_ligesttheses_Dirige"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "hidden,idMembreLabo,idTheseHDR,CoefficientDEncadrement,EstDirecteur,DateDebut,DateFin"
	),
	"feInterface" => $TCA["tx_ligesttheses_Dirige"]["feInterface"],
	"columns" => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		"idMembreLabo" => Array (		
			"exclude" => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_Dirige.idmembrelabo",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_ligestmembrelabo_MembreDuLabo",	
				"foreign_table_where" => "ORDER BY tx_ligestmembrelabo_MembreDuLabo.NomDUsage, tx_ligestmembrelabo_MembreDuLabo.Prenom",		
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"idTheseHDR" => Array (		
			"exclude" => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_Dirige.idthesehdr",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_ligesttheses_TheseHDR",	
				"foreign_table_where" => "AND tx_ligesttheses_TheseHDR.sys_language_uid=0 ORDER BY tx_ligesttheses_TheseHDR.Titre",	
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"CoefficientDEncadrement" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_Dirige.coefficientdencadrement",		
			"config" => Array (
				"type" => "input",
				"size" => "10",
				"max" => "255",
				"eval" => "trim, double2",
			)
		),
		"EstDirecteur" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_Dirige.estdirecteur",			
			'config' => Array (
				'type' => 'select',
				'size' => 1,
				'maxitems' => 1,
				'items' => Array (
					Array('LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_Dirige.estdirecteur.non', 'F'),
					Array('LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_Dirige.estdirecteur.oui', 'V'),
				),
				 'default' => 'F',
			)
		),
		"DateDebut" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_Dirige.datedebut",		
			"config" => Array (
				"type"     => "input",
				"size"     => "10",
				"max"      => "10",
				"eval"     => "required,trim,tx_ligestmembrelabo_dateValide,tx_ligestmembrelabo_dateObligatoire",
				'default' => '0000-00-00'
			)
		),
		"DateFin" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_Dirige.datefin",		
			"config" => Array (
				"type"     => "input",
				"size"     => "10",
				"max"      => "10",
				"eval"     => "trim,tx_ligestmembrelabo_dateValide",
				"default"  => "0"
			)
		),
	),
	"types" => array (
		"0" => array("showitem" => "hidden;;1;;1-1-1, idMembreLabo, idTheseHDR, CoefficientDEncadrement, EstDirecteur, DateDebut, DateFin")
	),
	"palettes" => array (
		"1" => array("showitem" => "")
	)
);
?>