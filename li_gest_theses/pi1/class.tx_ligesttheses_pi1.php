<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Bruno Gallet <bruno.gallet@gmail.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Managing Thesis/HDR' for the 'li_gest_theses' extension.
 *
 * @author	Bruno Gallet <bruno.gallet@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_ligesttheses
 */
class tx_ligesttheses_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_ligesttheses_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_ligesttheses_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'li_gest_theses';	// The extension key.
	var $pi_checkCHash = true;
	
	
		/**
	 * Recherche des sous-dossiers contenant les membres du laboratoire...
	 * @param $pid_parent identifiant du dossier à explorer
	 * @return Un tableau contenant tous les sous-dossiers trouvés...
	 */
	private function rechercheFils($pid_parent)
	{
		$tableau = array(); //tableau contenant tous les sous-dossiers trouvés...
		
		$tableau_temp = array(); //tableau intermédiaire contenant les sous-dossiers à stocker
		
		//Requête pour trouver tous les sous-dossiers du dossier courant
		$select_fields_pid = 'pages.uid';
		$from_table_pid = 'pages';
		$where_clause_pid = 'pages.pid='.$pid_parent;
		$groupBy_pid = '';
		$orderBy_pid = '';
		$limit_pid = '';
		$tryMemcached_pid = '';

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields_pid, $from_table_pid, $where_clause_pid, $groupBy_pid, $orderBy_pid, $tryMemcached_pid);

		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))
		{
			$pid_courant = $row['uid'];


			//On stocke l'uid courant dans le tableau
			$taille_tableau = count($tableau);
						
			$tableau[$taille_tableau] = $pid_courant;

			$tableau_temp = $this->rechercheFils($pid_courant);

			foreach ($tableau_temp as $value) {
				$taille_tableau = count($tableau);
					
				$tableau[$taille_tableau] = $value;
			}
		}
		return $tableau;
	}
	

	/**
	 * Gestion du multilangue
	 * Cette fonction recherche le texte le plus approprié par rapport à la page chargée
	 * Cette fonction est utilisée à la suite d'une requête permettant de connaître les paramètres $uid, $sys_language_uid, $uid_parent et $texte_champ.
	 * @param $uid L'identifiant de l'enregistrement pour lequel on recherche la meilleur traduction.
	 * @param $sys_language_uid L'identifiant de la langue de l'enregistrement pour lequel on recherche la meilleur traduction.
	 * @param $uid_parent L'identifiant du parent  de l'enregistrement pour lequel on recherche la meilleur traduction.
	 * @param $texte_champ La traduction de l'enregistrement pour lequel on recherche la meilleur traduction.
	 * @param $table Le nom de la table dans laquel se trouve le champ à traduire
	 * @param $nom_champ Le nom du champ à traduire
	 * @return Une chaîne de caratères contenant la traduction a afficher
	 */
	private function rechercherUidLangue($uid,$sys_language_uid,$uid_parent,$texte_champ,$table,$nom_champ)
	{
		$texte=$texte_champ;
		//On teste si le libellé est déjà dans la bonne langue...
		if ($sys_language_uid<>$GLOBALS['TSFE']->sys_language_content)
		{
			$uid_recherche=$uid;
			$trouve=false;
			// Si on a l'id du parent
			if($uid_parent<>'0')
			{

				//Requête pour trouver les infos du parent
				$select_fields_uid = $table.'.uid, '.$table.'.sys_language_uid, '.$table.'.'.$nom_champ;
				$from_table_uid = $table;
				$where_clause_uid = $table.'.uid='.$pid_parent.' AND '.$table.'.deleted<>1';
				$groupBy_uid = '';
				$orderBy_uid = '';
				$limit_uid = '';
				$tryMemcached_uid = '';

				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields_uid, $from_table_uid, $where_clause_uid, $groupBy_uid, $orderBy_uid, $tryMemcached_uid);

				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))
				{
					if($row['sys_language_uid']==$GLOBALS['TSFE']->sys_language_content)
					{
						$texte=$row[$nom_champ];
						$trouve=true;
					}
					else
					{
						$uid_recherche=$row['uid'];
					}
				}
			}
			
			if($trouve==false)
			{
				//Requête pour trouver les infos du parent
				$select_fields_uid = $table.'.uid, '.$table.'.sys_language_uid, '.$table.'.'.$nom_champ;
				$from_table_uid = $table;
				$where_clause_uid = $table.'.l18n_parent='.$uid_recherche.' AND '.$table.'.deleted<>1';
				$groupBy_uid = '';
				$orderBy_uid = '';
				$limit_uid = '';
				$tryMemcached_uid = '';

				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields_uid, $from_table_uid, $where_clause_uid, $groupBy_uid, $orderBy_uid, $tryMemcached_uid);

				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))
				{
					if($row['sys_language_uid']==$GLOBALS['TSFE']->sys_language_content)
					{
						$texte=$row[$nom_champ];
					}
				}
			}
		}

		return $texte;
	}
	
	
	private function membre($uid_membres)
	{
			//Création de la contrainte permettant l'affichage que de certains membres...
			$membres='';

			if($uid_membres<>'')
			{
				$membres=' AND ( ';
				$premier=true;

				$tableau_membres = Explode(",",$uid_membres);

				foreach ($tableau_membres as $membre_courant) {
					if ($premier <> true)
					{
						$membres = $membres.' OR ';
					}
					else
					{
						$premier=false;
					}
					$membres=$membres.'tx_ligesttheses_TheseHDR.idMembreLabo='.$membre_courant;
				}

				$membres = $membres.' )';

			}

			return $membres;
	}
	
	
	private function encadrant($uid_encadrants)
	{
			//Création de la contrainte permettant l'affichage que de certains encadrants...
			$encadrants='';

			if($uid_encadrants<>'')
			{
				$encadrants=' AND ( ';
				$premier=true;

				$tableau_encadrants = Explode(",",$uid_encadrants);

				foreach ($tableau_encadrants as $encadrant_courant) {
					if ($premier <> true)
					{
						$encadrants = $encadrants.' OR ';
					}
					else
					{
						$premier=false;
					}
					$encadrants=$encadrants.'tx_ligesttheses_Dirige.idMembreLabo='.$encadrant_courant;
				}

				$encadrants = $encadrants.' )';

			}

			return $encadrants;
	}

	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf)	{
		//Initialisation
		$this->conf=$conf;
		$this->pi_initPIflexForm(); // Init and get the flexform data of the plugin
		$this->lConf = array(); // Setup our storage array...
		// Assign the flexform data to a local variable for easier access
		$this->pi_setPiVarDefaults();
		$piFlexForm = $this->cObj->data['pi_flexform'];
		 // Traverse the entire array based on the language...
		 // and assign each configuration option to $this->lConf array...

		foreach ( $piFlexForm['data'] as $sheet => $data )
		{
			foreach ( $data as $lang => $value )
			{
				foreach ( $value as $key => $val )
				{
					$this->lConf[$key] = $this->pi_getFFvalue($piFlexForm, $key, $sheet);
				}
			}
		}
		$this->pi_loadLL();
		
		//Gestion de gabarits (Template)
	
		$this->templateCode = $this->cObj->fileResource($this->lConf["template_file"]);

		$template = array();

		$template['total'] = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE###');

		$template['item'] = $this->cObj->getSubpart($template['total'], '###ITEM###');
	
	
		$template['dirige'] = $this->cObj->getSubpart($template['item'], '###DIRIGE###');

	
	
	
	
		//Exemple de création de requêtes
		/*----------------------------------------------------------------------------------------
		//Création de requête
		$select_fields = '*';
		$from_table = 'test';
		$where_clause = '';
		$groupBy = '';
		$orderBy = 'champ1';
		$limit = '';
		$tryMemcached = '';

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $tryMemcached);

		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))
		{
			$test = $test.$row['champ1'].' ';
		}

		----------------------------------------------------------------------------------------*/
		$select_fields = '';
		$from_table = '';
		$where_clause = '';
		$groupBy = '';
		$orderBy = '';
		$limit = '';
		$tryMemcached = '';

		
		
		
		
		if(($this->lConf['requete'])<>true){
		
		
			//Récupération de toutes les membres de l'équipe demandée ayant les postes sélectionnés
			$code = ''; //Variable contenant le code à afficher
		
			//Construction de la requête
			$select = 'DISTINCT tx_ligesttheses_TheseHDR.uid AS uidthese, tx_ligesttheses_TheseHDR.*';
			$table = 'tx_ligesttheses_TheseHDR';
			$where = 'tx_ligesttheses_TheseHDR.deleted<>1 AND tx_ligesttheses_TheseHDR.sys_language_uid=0';
			
			/********************FILTRES********************/
			//Ces filtres rajoutent des contraintes sur la requête à afficher

			
			//Gestion du type d'enregistrement choisi: thèses, HDR, ou thèses/HDR
			if($this->lConf['typethesehdr']=='These')
			{
				$where = $where.' AND tx_ligesttheses_TheseHDR.Type = "T"';
			}
			else if($this->lConf['typethesehdr']=='HDR')
			{
				$where = $where.' AND tx_ligesttheses_TheseHDR.Type = "H"';
			}
			
			
			//Gestion de la date de début et de fin de thèse/HDR
			if($this->lConf['datethesehdr']=='Actuels')
			{
				$where = $where.' AND tx_ligesttheses_TheseHDR.DateDebut<="'.date('Y-m-d').'" AND (tx_ligesttheses_TheseHDR.DateFin>="'.date('Y-m-d').'" OR tx_ligesttheses_TheseHDR.DateFin="0000-00-00")';
			}
			else if($this->lConf['datethesehdr']=='Anciens')
			{
				$where = $where.' AND tx_ligesttheses_TheseHDR.DateFin<"'.date('Y-m-d').'" AND tx_ligesttheses_TheseHDR.DateFin<>"0000-00-00"';
			}

		
			//Gestion des membres
			$membre = $this->membre($this->lConf['membres']);
			if($membre<>""){
				$where = $where.$membre;
			}
			

			//Gestion des encadrants
			$encadrant = $this->encadrant($this->lConf['encadrants']);
			if($encadrant<>""){
				$table = $table.', tx_ligesttheses_Dirige';
				$where = $where.' AND tx_ligesttheses_Dirige.deleted<>1 AND tx_ligesttheses_Dirige.idTheseHDR = tx_ligesttheses_TheseHDR.uid';
				$where = $where.$encadrant;
			}

		
		
		
		

			// Création de la clause permettant de ne choisir que certaines thèses/HDR selon les dossiers sélectionnés
			//On récupère tous les sous-dossiers...
			$dossiers = '';	
			
			$pid = array(); //dossiers sélectionnés
			$pages = array(); //dossiers et sous dossiers...
			
			$chaine = $this->lConf['pid'];

			if ($chaine!=''){
				$dossiers = $dossiers.' AND (';
				$pid = Explode(",",$chaine);
				//$pages = $pid;
				
				$premier = true;
				
				foreach ($pid as $pid_courant) {
					$pages = array_merge($pages,$this->rechercheFils($pid_courant));
				}
				
				foreach ($pid as $value) {
					$taille_tableau = count($pages);

					$pages[$taille_tableau] = $value;
				}
				
				
				foreach ($pages as $value) {
					if ($premier == true){
						$dossiers = $dossiers.'tx_ligesttheses_TheseHDR.pid='.$value;
						$premier = false;
					}
					else{
						$dossiers = $dossiers.' OR tx_ligesttheses_TheseHDR.pid='.$value;
					}
				}

				$dossiers = $dossiers.')';
			}

			$where = $where.$dossiers;


			
			
			
			
		
			$select_fields = $select;
			$from_table = $table;
			$where_clause = $where;
			$groupBy = '';
			$orderBy = 'tx_ligesttheses_TheseHDR.Titre';
			$limit = '';
			$tryMemcached = '';
		
		}
		else{
			$select_fields = $this->lConf['select'];
			$from_table = $this->lConf['from_table'];
			$where_clause = $this->lConf['where_clause'];
			$groupBy = $this->lConf['groupBy'];
			$orderBy = $this->lConf['orderBy'];
			$limit = $this->lConf['limit'];
			$tryMemcached = $this->lConf['tryMemcached'];
		}
		
		
		$markerArray = array();
		$markerArray_Dirige = array();
		
		

		$contentItem='';
		
		
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $tryMemcached);

		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))       {

			$uid=$row['uidthese'];


				//Champ Titre (multilingue)
				$champTitre='';
				$champTitre=$row['Titre'];
					//On recherche le libellé traduit de Titre
				$champTitre=$this->rechercherUidLangue($row['uidthese'],$row['sys_language_uid'],$row['l18n_parent'],$row['Titre'],'tx_ligesttheses_TheseHDR','Titre');
			$markerArray['###Titre###'] = $champTitre;

				//Champ Description (multilingue)
				$champDescription='';
				$champDescription=$row['Description'];
					//On recherche le libellé traduit de Description
				$champDescription=$this->rechercherUidLangue($row['uidthese'],$row['sys_language_uid'],$row['l18n_parent'],$row['Description'],'tx_ligesttheses_TheseHDR','Description');
			$markerArray['###Description###'] = $champDescription;
			

	
			if($row['DateDebut']=='0000-00-00'){
				$markerArray['###DateDebut###'] = $this->lConf['datedebutthesehdr'];
			}
			else{
				$markerArray['###DateDebut###'] = $row['DateDebut'];
			}
	
	
			if($row['DateFin']=='0000-00-00'){
				$markerArray['###DateFin###'] = $this->lConf['datefinthesehdr'];
			}
			else{
				$markerArray['###DateFin###'] = $row['DateFin'];
			}
			
				//Champ Jury (multilingue)
				$champJury='';
				$champJury=$row['Jury'];
					//On recherche le libellé traduit de Jury
				$champJury=$this->rechercherUidLangue($row['uidthese'],$row['sys_language_uid'],$row['l18n_parent'],$row['Jury'],'tx_ligesttheses_TheseHDR','Jury');
			$markerArray['###Jury###'] = $champJury;

			$markerArray['###Resultat###'] = $row['Resultat'];
			$markerArray['###Type###'] = $row['Type'];


			//**************************************
			// Tables membre du labo
			//**************************************

				$Membre_select_fields = "tx_ligestmembrelabo_MembreDuLabo.uid AS uidmembre, tx_ligestmembrelabo_MembreDuLabo.*, tx_ligesttheses_TheseHDR.*";
				$Membre_from_table = "tx_ligestmembrelabo_MembreDuLabo, tx_ligesttheses_TheseHDR";
				$Membre_where_clause = "tx_ligesttheses_TheseHDR.uid = ".$uid." AND tx_ligesttheses_TheseHDR.idMembreLabo = tx_ligestmembrelabo_MembreDuLabo.uid AND tx_ligesttheses_TheseHDR.deleted<>1 AND tx_ligestmembrelabo_MembreDuLabo.deleted<>1";
				$Membre_groupBy = "";
				$Membre_orderBy = "tx_ligestmembrelabo_MembreDuLabo.NomDUsage";
				$Membre_limit = "";
				$Membre_tryMemcached = "";

				$Membre_res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($Membre_select_fields, $Membre_from_table, $Membre_where_clause, $Membre_groupBy, $Membre_orderBy, $Membre_tryMemcached);

				while($membre_row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($Membre_res))       {
				
					$markerArray['###NomDUsage###'] = $membre_row['NomDUsage'];
					$markerArray['###NOMDUSAGE###'] = mb_strtoupper($membre_row['NomDUsage'],"UTF-8");

					$markerArray['###NomMaritale###'] = $membre_row['NomMaritale'];
					$markerArray['###NOMMARITALE###'] = mb_strtoupper($membre_row['NomMaritale'],"UTF-8");

					$markerArray['###NomPreMarital###'] = $membre_row['NomPreMarital'];
					$markerArray['###NOMPREMARITAL###'] = mb_strtoupper($membre_row['NomPreMarital'],"UTF-8");

					$markerArray['###Prenom###'] = $membre_row['Prenom'];
					$markerArray['###PRENOM###'] = mb_strtoupper($membre_row['Prenom'],"UTF-8");

					// Afficher les initailes d'un membre
					$markerArray['###InitialesPN###'] = substr($membre_row['Prenom'],0,1).'.'.substr($membre_row['NomDUsage'],0,1).'.';
					$markerArray['###InitialesNP###'] = substr($membre_row['NomDUsage'],0,1).'.'.substr($membre_row['Prenom'],0,1).'.';


					$markerArray['###Genre###'] = $membre_row['Genre'];
					if($membre_row['DateNaissance']=='0000-00-00'){
						$markerArray['###DateNaissance###'] = $this->lConf['datenaissance'];
					}
					else{
						$markerArray['###DateNaissance###'] = $membre_row['DateNaissance'];
					}

					$markerArray['###Nationalite###'] = $membre_row['Nationalite'];
					$markerArray['###NATIONALITE###'] = mb_strtoupper($membre_row['Nationalite'],"UTF-8");

					if($membre_row['DateArrivee']=='0000-00-00'){
						$markerArray['###DateArrivee###'] = $this->lConf['datearrivee'];
					}
					else{
						$markerArray['###DateArrivee###'] = $membre_row['DateArrivee'];
					}
					if($membre_row['DateSortie']=='0000-00-00'){
						$markerArray['###DateSortie###'] = $this->lConf['datesortie'];
					}
					else{
						$markerArray['###DateSortie###'] = $membre_row['DateSortie'];
					}
					$markerArray['###NumINE###'] = $membre_row['NumINE'];
					$markerArray['###SectionCNU###'] = $membre_row['SectionCNU'];
					$markerArray['###CoordonneesRecherche###'] = $membre_row['CoordonneesRecherche'];
					$markerArray['###CoordonneesEnseignement###'] = $membre_row['CoordonneesEnseignement'];
					$markerArray['###email###'] = $membre_row['email'];
					$markerArray['###CoordonneesPersonnelles###'] = $membre_row['CoordonneesPersonnelles'];
					//Configuration du lien PageWeb
						// configure the typolink
						$this->local_cObj = t3lib_div::makeInstance('tslib_cObj');
						$this->local_cObj->setCurrentVal($GLOBALS['TSFE']->id);
						$this->typolink_conf = $this->conf['typolink.'];
						// configure typolink
						$temp_conf = $this->typolink_conf;
						$temp_conf['parameter'] = $membre_row['PageWeb'];
						$temp_conf['extTarget'] = '';				
						$temp_conf['parameter.']['wrap'] = "|";
						// Fill wrapped subpart marker
					$wrappedSubpartContentArray['###PageWebLien###'] = $this->local_cObj->typolinkWrap($temp_conf);
					$markerArray['###PageWeb###'] = $membre_row['PageWeb'];
			
				}

			
			
			//**************************************
			// Tables Dirige
			//**************************************
				$contentDirige = '';
				$contentDirige_dernier = '';

				$Dirige_select_fields = "tx_ligesttheses_Dirige.uid AS uiddirige, tx_ligesttheses_Dirige.*, tx_ligestmembrelabo_MembreDuLabo.*";
				$Dirige_from_table = "tx_ligesttheses_Dirige, tx_ligestmembrelabo_MembreDuLabo";
				$Dirige_where_clause = "tx_ligesttheses_Dirige.idTheseHDR = ".$uid." AND tx_ligesttheses_Dirige.idMembreLabo = tx_ligestmembrelabo_MembreDuLabo.uid AND tx_ligesttheses_Dirige.deleted<>1 AND tx_ligestmembrelabo_MembreDuLabo.deleted<>1";
				$Dirige_groupBy = "";
				$Dirige_orderBy = "tx_ligesttheses_Dirige.DateDebut DESC";
				$Dirige_limit = "";
				$Dirige_tryMemcached = "";

				$Dirige_res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($Dirige_select_fields, $Dirige_from_table, $Dirige_where_clause, $Dirige_groupBy, $Dirige_orderBy, $Dirige_tryMemcached);

				while($dirige_row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($Dirige_res))       {

				$markerArray_Dirige['###Dirige_CoefficientDEncadrement###'] = $dirige_row['CoefficientDEncadrement'];

				$markerArray_Dirige['###Dirige_EstDirecteur###'] = $dirige_row['EstDirecteur'];
				
				if($dirige_row['DateDebut']=='0000-00-00'){
					$markerArray_Dirige['###Dirige_DateDebut###'] = $this->lConf['datedebutthesehdr'];
				}
				else{
					$markerArray_Dirige['###Dirige_DateDebut###'] = $dirige_row['DateDebut'];
				}
		
		
				if($dirige_row['DateFin']=='0000-00-00'){
					$markerArray_Dirige['###Dirige_DateFin###'] = $this->lConf['datefinthesehdr'];
				}
				else{
					$markerArray_Dirige['###Dirige_DateFin###'] = $dirige_row['DateFin'];
				}
					
				

				$markerArray_Dirige['###Dirige_NomDUsage###'] = $dirige_row['NomDUsage'];
				$markerArray_Dirige['###Dirige_NOMDUSAGE###'] = mb_strtoupper($dirige_row['NomDUsage'],"UTF-8");

				$markerArray_Dirige['###Dirige_NomMaritale###'] = $dirige_row['NomMaritale'];
				$markerArray_Dirige['###Dirige_NOMMARITALE###'] = mb_strtoupper($dirige_row['NomMaritale'],"UTF-8");

				$markerArray_Dirige['###Dirige_NomPreMarital###'] = $dirige_row['NomPreMarital'];
				$markerArray_Dirige['###Dirige_NOMPREMARITAL###'] = mb_strtoupper($dirige_row['NomPreMarital'],"UTF-8");

				$markerArray_Dirige['###Dirige_Prenom###'] = $dirige_row['Prenom'];
				$markerArray_Dirige['###Dirige_PRENOM###'] = mb_strtoupper($dirige_row['Prenom'],"UTF-8");

				// Afficher les initailes d'un membre
				$markerArray_Dirige['###Dirige_InitialesPN###'] = substr($dirige_row['Prenom'],0,1).'.'.substr($dirige_row['NomDUsage'],0,1).'.';
				$markerArray_Dirige['###Dirige_InitialesNP###'] = substr($dirige_row['NomDUsage'],0,1).'.'.substr($dirige_row['Prenom'],0,1).'.';


				$markerArray_Dirige['###Dirige_Genre###'] = $dirige_row['Genre'];
				if($dirige_row['DateNaissance']=='0000-00-00'){
					$markerArray_Dirige['###Dirige_DateNaissance###'] = $this->lConf['datenaissance'];
				}
				else{
					$markerArray_Dirige['###Dirige_DateNaissance###'] = $dirige_row['DateNaissance'];
				}

				$markerArray_Dirige['###Dirige_Nationalite###'] = $dirige_row['Nationalite'];
				$markerArray_Dirige['###Dirige_NATIONALITE###'] = mb_strtoupper($dirige_row['Nationalite'],"UTF-8");

				if($dirige_row['DateArrivee']=='0000-00-00'){
					$markerArray_Dirige['###Dirige_DateArrivee###'] = $this->lConf['datearrivee'];
				}
				else{
					$markerArray_Dirige['###Dirige_DateArrivee###'] = $dirige_row['DateArrivee'];
				}
				if($dirige_row['DateSortie']=='0000-00-00'){
					$markerArray_Dirige['###Dirige_DateSortie###'] = $this->lConf['datesortie'];
				}
				else{
					$markerArray_Dirige['###Dirige_DateSortie###'] = $dirige_row['DateSortie'];
				}
				$markerArray_Dirige['###Dirige_NumINE###'] = $dirige_row['NumINE'];
				$markerArray_Dirige['###Dirige_SectionCNU###'] = $dirige_row['SectionCNU'];
				$markerArray_Dirige['###Dirige_CoordonneesRecherche###'] = $dirige_row['CoordonneesRecherche'];
				$markerArray_Dirige['###Dirige_CoordonneesEnseignement###'] = $dirige_row['CoordonneesEnseignement'];
				$markerArray_Dirige['###Dirige_email###'] = $dirige_row['email'];
				$markerArray_Dirige['###Dirige_CoordonneesPersonnelles###'] = $dirige_row['CoordonneesPersonnelles'];
				//Configuration du lien PageWeb
					// configure the typolink
					$this->local_cObj = t3lib_div::makeInstance('tslib_cObj');
					$this->local_cObj->setCurrentVal($GLOBALS['TSFE']->id);
					$this->typolink_conf = $this->conf['typolink.'];
					// configure typolink
					$temp_conf = $this->typolink_conf;
					$temp_conf['parameter'] = $dirige_row['PageWeb'];
					$temp_conf['extTarget'] = '';				
					$temp_conf['parameter.']['wrap'] = "|";
					// Fill wrapped subpart marker
				$wrappedSubpartContentArray_Dirige['###Dirige_PageWebLien###'] = $this->local_cObj->typolinkWrap($temp_conf);
				$markerArray_Dirige['###Dirige_PageWeb###'] = $dirige_row['PageWeb'];
				
				
				
				
				
				
				
				
				
				
				
				
				
				
					
					
					
					
					
					
					$contentDirige .= $this->cObj->substituteMarkerArrayCached($template['dirige'],$markerArray_Dirige,array(),$wrappedSubpartContentArray_Dirige);

				}

				$subpartArray_Item['###DIRIGE###'] = $contentDirige;
			
			
			
			
			
			
			
			
			
			

				// Fill the temporary item
			$contentItem .= $this->cObj->substituteMarkerArrayCached($template['item'],$markerArray, $subpartArray_Item, $wrappedSubpartContentArray);		
		}
	
	
		// Fill the content with items in $contentItem
		$subpartArray['###CONTENT###'] = $contentItem;

		// Fill the TEMPLATE subpart
		$content = $this->cObj->substituteMarkerArrayCached($template['total'], array(), $subpartArray);
		
		//$content=$code;
	
		return $this->pi_wrapInBaseClass($content);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/li_gest_theses/pi1/class.tx_ligesttheses_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/li_gest_theses/pi1/class.tx_ligesttheses_pi1.php']);
}

?>