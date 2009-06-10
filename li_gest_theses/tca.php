<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$admin = '0';

if (isset($GLOBALS['BE_USER']) && !empty($GLOBALS['BE_USER']))
{
	$admin = $GLOBALS['BE_USER']->isAdmin();
}

$these = '';
$dirige = '';
$cotutelle = '';
$financement = '';

if($admin == "1")
{
	$these = 'hidden;;1;;1-1-1,';
	$dirige = 'hidden;;1;;1-1-1,';
	$cotutelle = 'hidden;;1;;1-1-1, idTheseHDR,';
	$financement = 'hidden;;1;;1-1-1, ';
}
// ******************************************************************
// Création du formulaire pour la table tx_ligesttheses_TheseHDR
// ******************************************************************
$TCA["tx_ligesttheses_TheseHDR"] = array (
	"ctrl" => $TCA["tx_ligesttheses_TheseHDR"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "hidden,Titre,idMembreLabo,Type,Resume,DateDebut,DateSoutenance,Jury,Resultat,Financement,Afficher_Dirige, Afficher_Cotutelle"
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
				"eval" => "trim",
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
		"Resume" => Array (		
			"exclude" => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_TheseHDR.resume",		
			"config" => Array (
				"type" => "text",	
				"cols" => "48",	
				"rows" => "10",
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
		"DateSoutenance" => Array (		
			"exclude" => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_TheseHDR.datesoutenance",		
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
				"type" => "text",	
				"cols" => "48",	
				"rows" => "10",
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
		"Financement" => Array (		
			"exclude" => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_TheseHDR.financement",			
			'config' => Array (
				"type" => "select",
				"foreign_table" => "tx_ligesttheses_Financement",	
				"foreign_table_where" => "AND tx_ligesttheses_Financement.sys_language_uid<=0 ORDER BY tx_ligesttheses_Financement.Libelle",
				"size" => 1,
				"minitems" => 1,
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
				"size" => 6,
				"minitems" => 0,
				"maxitems" => 0,
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
							"champ"			=> "idTheseHDR",
							"lien"			=> Array('tx_ligestmembrelabo_MembreDuLabo'),
							"date_prov"		=> 'DateDebut', // Date que l'on va copier
							"date_champ"	=> 'DateDebut' // Champ où l'on veut copier la date
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
				),
			),
		),
"Afficher_Cotutelle" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_TheseHDR.afficherCotutelle",		
			"config" => Array (
				"type" => "select",
				"foreign_table" => "tx_ligesttheses_Cotutelle",	
				"foreign_table_where" => "AND tx_ligesttheses_Cotutelle.idTheseHDR=###THIS_UID### ORDER BY tx_ligesttheses_Cotutelle.NomCotutelle, tx_ligesttheses_Cotutelle.PrenomCotutelle",
				"size" => 6,
				"minitems" => 0,
				"maxitems" => 0,
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
							"table"			=> "tx_ligesttheses_Cotutelle",
							"champ"			=> "idTheseHDR"
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
							'table'=>'tx_ligesttheses_Cotutelle',
						),
						"script" => t3lib_extMgm::extRelPath("li_gest_membre_labo")."wizard/delete.php",
						"JSopenParams" => "height=1,width=1,status=0,menubar=0,scrollbars=1",
					),
				),
			),
		),
	),
	"types" => array (
		"0" => array("showitem" => $these."sys_language_uid, l18n_parent, l18n_diffsource, Titre, idMembreLabo, Type, Resume, DateDebut, DateSoutenance, Jury, Resultat, Financement, Afficher_Dirige, Afficher_Cotutelle")
	),
	"palettes" => array (
		"1" => array("showitem" => "")
	)
);



// ******************************************************************
// Création du formulaire pour la table tx_ligesttheses_Dirige
// ******************************************************************
$TCA["tx_ligesttheses_Dirige"] = array (
	"ctrl" => $TCA["tx_ligesttheses_Dirige"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "hidden,idTheseHDR,idMembreLabo,CoefficientDEncadrement,EstDirecteur,DateDebut,DateFin"
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
		"0" => array("showitem" => $dirige." idTheseHDR, idMembreLabo, CoefficientDEncadrement, EstDirecteur, DateDebut, DateFin")
	),
	"palettes" => array (
		"1" => array("showitem" => "")
	)
);

// ******************************************************************
// Création du formulaire pour la table tx_ligesttheses_Cotutelle
// ******************************************************************
$TCA["tx_ligesttheses_Cotutelle"] = array (
	"ctrl" => $TCA["tx_ligesttheses_Cotutelle"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "hidden, idTheseHDR, NomCotutelle, PrenomCotutelle, AdresseCotutelle"
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
		"idTheseHDR" => Array (		
			"exclude" => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_Cotutelle.idthesehdr",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_ligesttheses_TheseHDR",	
				"foreign_table_where" => "AND tx_ligesttheses_TheseHDR.sys_language_uid=0 ORDER BY tx_ligesttheses_TheseHDR.Titre",	
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"NomCotutelle" => Array (		
			"exclude" => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_Cotutelle.NomCotutelle",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"max" => "255",	
				"eval" => "required,trim",
			)
		),
		"PrenomCotutelle" => Array (		
			"exclude" => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_Cotutelle.PrenomCotutelle",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"max" => "255",	
				"eval" => "trim",
			)
		),
		"AdresseCotutelle" => Array (		
			"exclude" => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_Cotutelle.AdresseCotutelle",		
			"config" => Array (
				"type" => "text",	
				"cols" => "48",	
				"rows" => "10",
			)
		),
	),
	"types" => array (
		/*if($GLOBALS['BE_USER']->isAdmin())
		{
			"0" => array("showitem" => "hidden;;1;;1-1-1, idTheseHDR, NomCotutelle, PrenomCotutelle, AdresseCotutelle")
		}
		else
		{
			"0" => array("showitem" => ";;1;;1-1-1, NomCotutelle, PrenomCotutelle, AdresseCotutelle")
		}*/
		"0" => array("showitem" => $cotutelle."NomCotutelle, PrenomCotutelle, AdresseCotutelle")
	),
	"palettes" => array (
		"1" => array("showitem" => "")
	)
);

// ******************************************************************
// Création du formulaire pour la table tx_ligesttheses_Financement
// ******************************************************************
$TCA["tx_ligesttheses_Financement"] = array (
	"ctrl" => $TCA["tx_ligesttheses_Financement"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "hidden,Libelle"
	),
	"feInterface" => $TCA["tx_ligesttheses_Financement"]["feInterface"],
	"columns" => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
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
				'foreign_table'       => 'tx_ligesttheses_Financement',
				'foreign_table_where' => 'AND tx_ligesttheses_Financement.pid=###CURRENT_PID### AND tx_ligesttheses_Financement.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => array (		
			'config' => array (
				'type' => 'passthrough'
			)
		),
		"Libelle" => Array (		
			"exclude" => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			"label" => "LLL:EXT:li_gest_theses/locallang_db.xml:tx_ligesttheses_Financement.Libelle",		
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"max" => "255",
				"eval" => "trim",
			)
		),
	),
	"types" => array (
		"0" => array("showitem" => $financement."sys_language_uid, l18n_parent, l18n_diffsource, Libelle")
	),
	"palettes" => array (
		"1" => array("showitem" => "")
	)
);

?>