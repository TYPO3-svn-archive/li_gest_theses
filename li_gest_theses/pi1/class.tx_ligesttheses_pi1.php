<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Bruno Gallet <gallet.bruno@gmail.com>
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

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields_pid, $from_table_pid, $where_clause_pid, $groupBy_pid, $orderBy_pid, $limit_pid);

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
	 * @return Une chaîne de caractères contenant la traduction a afficher
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
				$where_clause_uid = $table.'.uid='.$uid_parent.' AND '.$table.'.deleted<>1';
				$groupBy_uid = '';
				$orderBy_uid = '';
				$limit_uid = '';

				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields_uid, $from_table_uid, $where_clause_uid, $groupBy_uid, $orderBy_uid, $limit_uid);

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

				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields_uid, $from_table_uid, $where_clause_uid, $groupBy_uid, $orderBy_uid, $limit_uid);

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
	
	/**
	 * Choix des membres
	 * Cette fonction permet de créer une contrainte concernant les membres
	 * @param $uid_membres Chaîne de caractères contenant les identifiants des membres (uid) séparés par des virgules
	 * @return Une chaîne de caractères contenant une contrainte à rajouter à une requête
	 */
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
	
	/**
	 * Choix des encadrants
	 * Cette fonction permet de créer une contrainte concernant les encadrants
	 * @param $uid_encadrants Chaîne de caractères contenant les identifiants des encadrants (uid) séparés par des virgules
	 * @param $est_directeur Indique si l'encadrant doit être ou non directeur de la thèse
	 * @return Une chaîne de caractères contenant une contrainte à rajouter à une requête
	 */
	private function encadrant($uid_encadrants,$est_directeur)
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

				if($est_directeur == "Oui")
				{
					$encadrants = $encadrants.' AND tx_ligesttheses_Dirige.EstDirecteur="V"';
				}
				else if($est_directeur == "Non")
				{
					$encadrants = $encadrants.' AND tx_ligesttheses_Dirige.EstDirecteur="F"';
				}
			}

			return $encadrants;
	}


	
	/**
	 * Choix des équipes
	 * Cette fonction permet de créer une contrainte concernant les équipes
	 * @param $uid_equipes Chaîne de caractères contenant les identifiants des équipes (uid) séparés par des virgules
	 * @return Une chaîne de caractères contenant une contrainte à rajouter à une requête
	 */
	private function equipe($uid_equipes)
	{
			//Création de la contrainte permettant l'affichage que de certaines équipes...
			$equipes='';

			if($uid_equipes<>'')
			{
				$equipes=' AND ( ';
				$premier=true;

				$tableau_equipes = Explode(",",$uid_equipes);

				foreach ($tableau_equipes as $equipe_courante) {
					if ($premier <> true)
					{
						$equipes = $equipes.' OR ';
					}
					else
					{
						$premier=false;
					}
					$equipes=$equipes.'tx_ligestmembrelabo_EstMembreDe.idEquipe='.$equipe_courante;
				}

				$equipes = $equipes.' )';

			}

			return $equipes;
	}


	
	
	
	/**
	 * Choix des structures
	 * Cette fonction permet de créer une contrainte concernant les structures
	 * @param $uid_categories Chaîne de caractères contenant les identifiants des structures (uid) séparés par une virgule
	 * @return Une chaîne de caractères contenant une contrainte à rajouter à une requête
	 */
	private function structure($uid_structures)
	{
			//Création de la contrainte permettant l'affichage que de certains types de structures...
			$structures='';

			if($uid_structures<>'')
			{
				$structures=' AND ( ';
				$premier=true;

				//Recherche des structures filles
				$tableau_temporaire = Explode(",",$uid_structures);
				$tableau_structures = array(); //structures et sous-structures...
				
				foreach ($tableau_temporaire as $structure_courante) {
					$tableau_structures = array_merge($tableau_structures,$this->rechercheStructuresFille($structure_courante));
				}
				
				//On ajoute les structures de départs
				foreach ($tableau_temporaire as $value) {
					$taille_tableau = count($tableau_structures);

					$tableau_structures[$taille_tableau] = $value;
				}


				foreach ($tableau_structures as $structure_courante) {
					if ($premier <> true)
					{
						$structures = $structures.' OR ';
					}
					else
					{
						$premier=false;
					}
					$structures=$structures.'tx_ligestmembrelabo_Structure.uid='.$structure_courante;
				}
	
				$structures = $structures.' )';

			}
				
			return $structures;
	}
	
	
	//Recherche des structures filles
	/**
	 * Recherche de structures filles
	 * @param $id_parent identifiant de la structure principale
	 * @return Un tableau contenant toutes les sous-structures trouvées...
	 */
	private function rechercheStructuresFille($id_parent)
	{
		$tableau = array(); //tableau contenant toutes les sous-structures trouvées...
		
		$tableau_temp = array(); //tableau intermédiaire contenant les sous-structures à stocker
		
		//Requête pour trouver toutes les sous-structures à partir de la structure courante
		$select_fields_pid = 'tx_ligestmembrelabo_Structure.uid';
		$from_table_pid = 'tx_ligestmembrelabo_Structure';
		$where_clause_pid = 'tx_ligestmembrelabo_Structure.idStructureParente='.$id_parent;
		$groupBy_pid = '';
		$orderBy_pid = '';
		$limit_pid = '';


		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields_pid, $from_table_pid, $where_clause_pid, $groupBy_pid, $orderBy_pid, $limit_pid);

		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))
		{
			$structure_courante = $row['uid'];


			//On stocke l'uid courant dans le tableau
			$taille_tableau = count($tableau);
						
			$tableau[$taille_tableau] = $structure_courante;

			$tableau_temp = $this->rechercheStructuresFille($structure_courante);

			foreach ($tableau_temp as $value) {
				$taille_tableau = count($tableau);
					
				$tableau[$taille_tableau] = $value;
			}
		}
		return $tableau;
	}
	
	
	
	/**
	 * Choix des résultats
	 * Cette fonction permet de créer une contrainte concernant les résultats
	 * @param $uid_resultats Chaîne de caractères contenant les identifiants des résultats séparés par des virgules
	 * @return Une chaîne de caractères contenant une contrainte à rajouter à une requête
	 */
	private function resultat($uid_resultats)
	{
			//Création de la contrainte permettant l'affichage que de certains résultats...
			$resultats='';

			if($uid_resultats<>'')
			{
				$resultats=' AND ( ';
				$premier=true;

				$tableau_resultats = Explode(",",$uid_resultats);

				foreach ($tableau_resultats as $resultat_courant) {
					if ($premier <> true)
					{
						$resultats = $resultats.' OR ';
					}
					else
					{
						$premier=false;
					}
					$resultats=$resultats.'tx_ligesttheses_TheseHDR.Resultat="'.$resultat_courant.'"';
				}

				$resultats = $resultats.' )';

			}

			return $resultats;
	}
	
	
	/**
	 * Choix des financements
	 * Cette fonction permet de créer une contrainte concernant les financements
	 * @param $uid_financements Chaîne de caractères contenant les identifiants des financements séparés par des virgules
	 * @return Une chaîne de caractères contenant une contrainte à rajouter à une requête
	 */
	private function financement($uid_financements)
	{
			//Création de la contrainte permettant l'affichage que de certains financements...
			$financements='';

			if($uid_financements<>'')
			{
				$financements=' AND ( ';
				$premier=true;

				$tableau_financements = Explode(",",$uid_financements);

				foreach ($tableau_financements as $financement_courant) {
					if ($premier <> true)
					{
						$financements = $financements.' OR ';
					}
					else
					{
						$premier=false;
					}
					$financements=$financements.'tx_ligesttheses_TheseHDR.Financement="'.$financement_courant.'"';
				}

				$financements = $financements.' )';

			}

			return $financements;
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


					if(ereg('groupe',$key)){
						$temp = $key;
						foreach ($val as $key2 => $val2 )
						{
							$temp = $temp.'/'.$key2;
							foreach ($val2 as $key3 => $val3 )
							{
								$this->lConf[$key3] = $this->pi_getFFvalue($piFlexForm, $key.'/'.$key2.'/'.$key3, $sheet);
							}
						}
		
					
					}
				}

			}

		}
		$this->pi_loadLL();

		//Gestion de gabarits (Template)

		$this->templateCode = $this->cObj->fileResource($this->lConf["template_file"]);

		$template = array();

		$template['total'] = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE###');

		$template['item'] = $this->cObj->getSubpart($template['total'], '###ITEM###');
	
		// Contient les informations sur tous ceux qui s'occupe d'une thèse
		$template['dirige'] = $this->cObj->getSubpart($template['item'], '###DIRIGE###');

		// Contient uniquement ceux qui sont directeur de la thèse
		$template['dirige_estdirecteur'] = $this->cObj->getSubpart($template['item'], '###DIRIGE_EST_DIRECTEUR###');

		// Contient uniquement tous ceux qui ne sont pas directeur de la thèse
		$template['dirige_nestpas_directeur'] = $this->cObj->getSubpart($template['item'], '###DIRIGE_NESTPAS_DIRECTEUR###');
		
		$template['cotutelle'] = $this->cObj->getSubpart($template['item'], '###COTUTELLE###');

	
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
			$table = 'tx_ligesttheses_TheseHDR, tx_ligestmembrelabo_MembreDuLabo';
			$where = 'tx_ligesttheses_TheseHDR.deleted<>1 AND tx_ligesttheses_TheseHDR.sys_language_uid=0 AND tx_ligestmembrelabo_MembreDuLabo.deleted<>1 AND tx_ligestmembrelabo_MembreDuLabo.uid=tx_ligesttheses_TheseHDR.idMembreLabo';
			
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
			
			/*
			//Gestion de la date de début et de fin de thèse/HDR
			if($this->lConf['datethesehdr']=='Actuels')
			{
				$where = $where.' AND tx_ligesttheses_TheseHDR.DateDebut<="'.date('Y-m-d').'" AND (tx_ligesttheses_TheseHDR.DateSoutenance>="'.date('Y-m-d').'" OR tx_ligesttheses_TheseHDR.DateSoutenance="0000-00-00")';
			}
			else if($this->lConf['datethesehdr']=='Anciens')
			{
				$where = $where.' AND tx_ligesttheses_TheseHDR.DateSoutenance<"'.date('Y-m-d').'" AND tx_ligesttheses_TheseHDR.DateSoutenance<>"0000-00-00"';
			}
*/

			//Gestion des membres
			$membre = $this->membre($this->lConf['membres']);
			if($membre<>""){
				$where = $where.$membre;
			}


			//Gestion des encadrants
			$encadrant = $this->encadrant($this->lConf['encadrants'],$this->lConf['encadrantsdirecteurs']);
			if($encadrant<>""){
				$table = $table.', tx_ligesttheses_Dirige';
				$where = $where.' AND tx_ligesttheses_Dirige.deleted<>1 AND tx_ligesttheses_Dirige.idTheseHDR = tx_ligesttheses_TheseHDR.uid';
				$where = $where.$encadrant;
			}


			//Gestion des équipes
			$equipe = $this->equipe($this->lConf['equipes']);
			if($equipe<>""){
				$table = $table.', tx_ligestmembrelabo_EstMembreDe';
				$where = $where.' AND tx_ligestmembrelabo_EstMembreDe.deleted<>1 AND tx_ligestmembrelabo_EstMembreDe.idMembreLabo = tx_ligesttheses_TheseHDR.idMembreLabo';
				$where = $where.$equipe;
			}


			//Gestion des structures
			$structure = $this->structure($this->lConf['structures']);
			if($structure<>""){
				$table = $table.', tx_ligestmembrelabo_Structure, tx_ligestmembrelabo_Exerce';
				$where = $where.' AND tx_ligestmembrelabo_Structure.deleted<>1 AND tx_ligestmembrelabo_Structure.uid = tx_ligestmembrelabo_Exerce.idStructure AND tx_ligestmembrelabo_Exerce.idMembreLabo = tx_ligesttheses_TheseHDR.idMembreLabo AND tx_ligestmembrelabo_Exerce.deleted<>1';
				$where = $where.$structure;
			}


			//Gestion des résultats
			$resultat = $this->resultat($this->lConf['resultats']);
			if($resultat<>""){
				$where = $where.$resultat;
			}

			//Gestion des financements
			$financement = $this->financement($this->lConf['financements']);
			if($financement<>""){
				$where = $where.$financement;
			}


			//Gestion des cotutelles
			$cotutelles = $this->lConf['cotutelles'];
			if($cotutelles=="Oui"){
				$where = $where." AND EXISTS (SELECT * FROM tx_ligesttheses_Cotutelle WHERE tx_ligesttheses_Cotutelle.idTheseHDR=tx_ligesttheses_TheseHDR.uid)";
			}
			else if ($cotutelles=="Non"){
				$where = $where." AND NOT EXISTS (SELECT * FROM tx_ligesttheses_Cotutelle WHERE tx_ligesttheses_Cotutelle.idTheseHDR=tx_ligesttheses_TheseHDR.uid)";
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
			if($this->lConf['tri']=='Membres')
			{
				$orderBy = 'tx_ligestmembrelabo_MembreDuLabo.NomDUsage';
			}
			else if($this->lConf['tri']=='Annees Croissantes')
			{
				$orderBy = 'tx_ligesttheses_TheseHDR.DateSoutenance';
			}
			else if($this->lConf['tri']=='Annees Decroissantes')
			{
				$orderBy = 'tx_ligesttheses_TheseHDR.DateSoutenance DESC';
			}

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
		$markerArray_Cotutelle = array();


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
			if($champTitre<>''){
				$markerArray['###Titre_Separateur###'] = $this->lConf['separateurTheseTitre'];
			}
			else{
				$markerArray['###Titre_Separateur###'] = '';
			}
			
			

				//Champ Resume (multilingue)
				$champResume='';
				$champResume=$row['Resume'];
					//On recherche le libellé traduit de Resume
				$champResume=$this->rechercherUidLangue($row['uidthese'],$row['sys_language_uid'],$row['l18n_parent'],$row['Resume'],'tx_ligesttheses_TheseHDR','Resume');
			$markerArray['###Resume###'] = $champResume;
			if($champResume<>''){
				$markerArray['###Resume_Separateur###'] = $this->lConf['separateurTheseResume'];
			}
			else{
				$markerArray['###Resume_Separateur###'] = '';
			}
			
			if($row['DateDebut']=='0000-00-00'){
				$markerArray['###DateDebut###'] = $this->lConf['datedebutthesehdr'];
				if($this->lConf['datedebutthesehdr']<>''){
					$markerArray['###DateDebut_Separateur###'] = $this->lConf['separateurTheseDateDebut'];
				}
				else{
					$markerArray['###DateDebut_Separateur###'] = '';
				}
			}
			else{			
				$date_explosee = explode("-", $row['DateDebut']);

				$annee = (int)$date_explosee[0];
				$mois = (int)$date_explosee[1];
				$jour = (int)$date_explosee[2];

				// la fonction date permet de reformater une date au format souhaité
				$markerArray['###DateDebut###'] = date($this->lConf['formatdate'],mktime(0, 0, 0, $mois, $jour, $annee));
				
				if($row['DateDebut']<>''){
					$markerArray['###DateDebut_Separateur###'] = $this->lConf['separateurTheseDateDebut'];
				}
				else{
					$markerArray['###DateDebut_Separateur###'] = '';
				}
			}
	
	
			if($row['DateSoutenance']=='0000-00-00'){
				$markerArray['###DateSoutenance###'] = $this->lConf['datesoutenancethesehdr'];
				if($this->lConf['datesoutenancethesehdr']<>''){
					$markerArray['###DateSoutenance_Separateur###'] = $this->lConf['separateurTheseDateSoutenance'];
				}
				else{
					$markerArray['###DateSoutenance_Separateur###'] = '';
				}
			}
			else{
				$date_explosee = explode("-", $row['DateSoutenance']);

				$annee = (int)$date_explosee[0];
				$mois = (int)$date_explosee[1];
				$jour = (int)$date_explosee[2];

				// la fonction date permet de reformater une date au format souhaité
				$markerArray['###DateSoutenance###'] = date($this->lConf['formatdate'],mktime(0, 0, 0, $mois, $jour, $annee));
				
				if($row['DateSoutenance']<>''){
					$markerArray['###DateSoutenance_Separateur###'] = $this->lConf['separateurTheseDateSoutenance'];
				}
				else{
					$markerArray['###DateSoutenance_Separateur###'] = '';
				}
			}
			
				//Champ Jury (multilingue)
				$champJury='';
				$champJury=$row['Jury'];
					//On recherche le libellé traduit de Jury
				$champJury=$this->rechercherUidLangue($row['uidthese'],$row['sys_language_uid'],$row['l18n_parent'],$row['Jury'],'tx_ligesttheses_TheseHDR','Jury');
			$markerArray['###Jury###'] = $champJury;
			if($champJury<>''){
				$markerArray['###Jury_Separateur###'] = $this->lConf['separateurTheseJury'];
			}
			else{
				$markerArray['###Jury_Separateur###'] = '';
			}
			
			
			$result=$row['Resultat'];
			if($result=="E")
			{
				$markerArray['###Resultat###'] = $this->lConf['resultatE'];
				if($this->lConf['resultatE']<>''){
					$markerArray['###Resultat_Separateur###'] = $this->lConf['separateurTheseResultat'];
				}
				else{
					$markerArray['###Resultat_Separateur###'] = '';
				}
			}
			else if($result=="A")
			{
				$markerArray['###Resultat###'] = $this->lConf['resultatA'];
				if($this->lConf['resultatA']<>''){
					$markerArray['###Resultat_Separateur###'] = $this->lConf['separateurTheseResultat'];
				}
				else{
					$markerArray['###Resultat_Separateur###'] = '';
				}
			}
			else if($result=="H")
			{
				$markerArray['###Resultat###'] = $this->lConf['resultatH'];
				if($this->lConf['resultatH']<>''){
					$markerArray['###Resultat_Separateur###'] = $this->lConf['separateurTheseResultat'];
				}
				else{
					$markerArray['###Resultat_Separateur###'] = '';
				}
			}
			else if($result=="T")
			{
				$markerArray['###Resultat###'] = $this->lConf['resultatT'];
				if($this->lConf['resultatT']<>''){
					$markerArray['###Resultat_Separateur###'] = $this->lConf['separateurTheseResultat'];
				}
				else{
					$markerArray['###Resultat_Separateur###'] = '';
				}
			}
			else // if($result=="F")
			{
				$markerArray['###Resultat###'] = $this->lConf['resultatF'];
				if($this->lConf['resultatF']<>''){
					$markerArray['###Resultat_Separateur###'] = $this->lConf['separateurTheseResultat'];
				}
				else{
					$markerArray['###Resultat_Separateur###'] = '';
				}
			}
			//$markerArray['###Resultat###'] = $row['Resultat'];
			

			
			

			
			
			
			/*
			$finance=$row['Financement'];
			if($finance=="ar")
			{
				$markerArray['###Financement###'] = $this->lConf['financementar'];
				if($this->lConf['financementar']<>''){
					$markerArray['###Financement_Separateur###'] = $this->lConf['separateurTheseFinancement'];
				}
				else{
					$markerArray['###Financement_Separateur###'] = '';
				}
			}
			else if($finance=="bcg")
			{
				$markerArray['###Financement###'] = $this->lConf['financementbcg'];
				if($this->lConf['financementbcg']<>''){
					$markerArray['###Financement_Separateur###'] = $this->lConf['separateurTheseFinancement'];
				}
				else{
					$markerArray['###Financement_Separateur###'] = '';
				}
			}
			else if($finance=="bcs")
			{
				$markerArray['###Financement###'] = $this->lConf['financementbcs'];
				if($this->lConf['financementbcs']<>''){
					$markerArray['###Financement_Separateur###'] = $this->lConf['separateurTheseFinancement'];
				}
				else{
					$markerArray['###Financement_Separateur###'] = '';
				}
			}
			else if($finance=="bpe")
			{
				$markerArray['###Financement###'] = $this->lConf['financementbpe'];
				if($this->lConf['financementbpe']<>''){
					$markerArray['###Financement_Separateur###'] = $this->lConf['separateurTheseFinancement'];
				}
				else{
					$markerArray['###Financement_Separateur###'] = '';
				}
			}
			else if($finance=="br")
			{
				$markerArray['###Financement###'] = $this->lConf['financementbr'];
				if($this->lConf['financementbr']<>''){
					$markerArray['###Financement_Separateur###'] = $this->lConf['separateurTheseFinancement'];
				}
				else{
					$markerArray['###Financement_Separateur###'] = '';
				}
			}
			else if($finance=="brc")
			{
				$markerArray['###Financement###'] = $this->lConf['financementbrc'];
				if($this->lConf['financementbrc']<>''){
					$markerArray['###Financement_Separateur###'] = $this->lConf['separateurTheseFinancement'];
				}
				else{
					$markerArray['###Financement_Separateur###'] = '';
				}
			}
			else if($finance=="c")
			{
				$markerArray['###Financement###'] = $this->lConf['financementc'];
				if($this->lConf['financementc']<>''){
					$markerArray['###Financement_Separateur###'] = $this->lConf['separateurTheseFinancement'];
				}
				else{
					$markerArray['###Financement_Separateur###'] = '';
				}
			}
			else if($finance=="anr")
			{
				$markerArray['###Financement###'] = $this->lConf['financementanr'];
				if($this->lConf['financementanr']<>''){
					$markerArray['###Financement_Separateur###'] = $this->lConf['separateurTheseFinancement'];
				}
				else{
					$markerArray['###Financement_Separateur###'] = '';
				}
			}
			else // if($finance=="s")
			{
				$markerArray['###Financement###'] = $this->lConf['financementsalarie'];
				if($this->lConf['financementsalarie']<>''){
					$markerArray['###Financement_Separateur###'] = $this->lConf['separateurTheseFinancement'];
				}
				else{
					$markerArray['###Financement_Separateur###'] = '';
				}
			}*/

			
			if($row['Type']=="T")
			{
				$markerArray['###Type###'] = $this->lConf['these'];
				if($row['Type']<>''){
					$markerArray['###Type_Separateur###'] = $this->lConf['separateurTheseType'];
				}
				else{
					$markerArray['###Type_Separateur###'] = '';
				}
			}
			else
			{
				$markerArray['###Type###'] = $this->lConf['hdr'];
				if($row['Type']<>''){
					$markerArray['###Type_Separateur###'] = $this->lConf['separateurTheseType'];
				}
				else{
					$markerArray['###Type_Separateur###'] = '';
				}
			}
			
			
			//**************************************
			// Table financement
			//**************************************
			$finance=$row['Financement'];
			$Financement_select_fields = "tx_ligesttheses_Financement.uid AS uidfinancement, tx_ligesttheses_Financement.*";
			$Financement_from_table = "tx_ligesttheses_Financement";
			$Financement_where_clause = "tx_ligesttheses_Financement.uid = ".$finance." AND tx_ligesttheses_Financement.deleted<>1";
			$Financement_groupBy = "";
			$Financement_orderBy = "tx_ligesttheses_Financement.Libelle";
			$Financement_limit = "";

			$Financement_res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($Financement_select_fields, $Financement_from_table, $Financement_where_clause, $Financement_groupBy, $Financement_orderBy, $Financement_limit);

			while($Financement_row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($Financement_res))       {
				
					//Champ Financement (multilingue)
					$champFinancement='';
					$champFinancement=$Financement_row['Libelle'];
						//On recherche le libellé traduit de Resume
					$champFinancement=$this->rechercherUidLangue($Financement_row['uidfinancement'],$Financement_row['sys_language_uid'],$Financement_row['l18n_parent'],$Financement_row['Libelle'],'tx_ligesttheses_Financement','Libelle');
				$markerArray['###Financement###'] = $champFinancement;
				if($champFinancement<>''){
					$markerArray['###Financement_Separateur###'] = $this->lConf['separateurTheseFinancement'];
				}
				else{
					$markerArray['###Financement_Separateur###'] = '';
				}


			
			}
			
			/*
				//Champ Financement (multilingue)
				$champFinancement='';
				$champFinancement=$row['Financement'];
					//On recherche le libellé traduit de Resume
				$champFinancement=$this->rechercherUidLangue($row['uidthese'],$row['sys_language_uid'],$row['l18n_parent'],$row['Resume'],'tx_ligesttheses_TheseHDR','Resume');
			$markerArray['###Financement###'] = $champFinancement;
			if($champResume<>''){
				$markerArray['###Financement_Separateur###'] = $this->lConf['separateurTheseFinancement'];
			}
			else{
				$markerArray['###Financement_Separateur###'] = '';
			}
			*/
			
			
			
			
			
			
			//**************************************
			// Table membre du labo
			//**************************************

				$Membre_select_fields = "tx_ligestmembrelabo_MembreDuLabo.uid AS uidmembre, tx_ligestmembrelabo_MembreDuLabo.*, tx_ligesttheses_TheseHDR.*";
				$Membre_from_table = "tx_ligestmembrelabo_MembreDuLabo, tx_ligesttheses_TheseHDR";
				$Membre_where_clause = "tx_ligesttheses_TheseHDR.uid = ".$uid." AND tx_ligesttheses_TheseHDR.idMembreLabo = tx_ligestmembrelabo_MembreDuLabo.uid AND tx_ligesttheses_TheseHDR.deleted<>1 AND tx_ligestmembrelabo_MembreDuLabo.deleted<>1";
				$Membre_groupBy = "";
				$Membre_orderBy = "tx_ligestmembrelabo_MembreDuLabo.NomDUsage";
				$Membre_limit = "";

				$Membre_res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($Membre_select_fields, $Membre_from_table, $Membre_where_clause, $Membre_groupBy, $Membre_orderBy, $Membre_limit);

				while($membre_row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($Membre_res))       {

					$markerArray['###NomDUsage###'] = $membre_row['NomDUsage'];
					if($membre_row['NomDUsage']<>''){
						$markerArray['###NomDUsage_Separateur###'] = $this->lConf['separateurNomDUsage'];
					}
					else{
						$markerArray['###NomDUsage_Separateur###'] = '';
					}
					$markerArray['###NOMDUSAGE###'] = mb_strtoupper($membre_row['NomDUsage'],"UTF-8");
					if($membre_row['NomDUsage']<>''){
						$markerArray['###NOMDUSAGE_Separateur###'] = $this->lConf['separateurNomDUsage'];
					}
					else{
						$markerArray['###NOMDUSAGE_Separateur###'] = '';
					}


					$markerArray['###NomMarital###'] = $membre_row['NomMarital'];
					if($membre_row['NomMarital']<>''){
						$markerArray['###NomMarital_Separateur###'] = $this->lConf['separateurNomMarital'];
					}
					else{
						$markerArray['###NomMarital_Separateur###'] = '';
					}
					
					$markerArray['###NOMMARITAL###'] = mb_strtoupper($membre_row['NomMarital'],"UTF-8");
					if($membre_row['NomMarital']<>''){
						$markerArray['###NOMMARITAL_Separateur###'] = $this->lConf['separateurNomMarital'];
					}
					else{
						$markerArray['###NOMMARITAL_Separateur###'] = '';
					}
					
					
					$markerArray['###NomPreMarital###'] = $membre_row['NomPreMarital'];
					if($membre_row['NomPreMarital']<>''){
						$markerArray['###NomPreMarital_Separateur###'] = $this->lConf['separateurNomPreMarital'];
					}
					else{
						$markerArray['###NomPreMarital_Separateur###'] = '';
					}
					
					$markerArray['###NOMPREMARITAL###'] = mb_strtoupper($membre_row['NomPreMarital'],"UTF-8");
					if($membre_row['NomPreMarital']<>''){
						$markerArray['###NOMPREMARITAL_Separateur###'] = $this->lConf['separateurNomPreMarital'];
					}
					else{
						$markerArray['###NOMPREMARITAL_Separateur###'] = '';
					}
					
					
					$markerArray['###Prenom###'] = $membre_row['Prenom'];
					if($membre_row['Prenom']<>''){
						$markerArray['###Prenom_Separateur###'] = $this->lConf['separateurPrenom'];
					}
					else{
						$markerArray['###Prenom_Separateur###'] = '';
					}
					
					
					$markerArray['###PRENOM###'] = mb_strtoupper($membre_row['Prenom'],"UTF-8");
					if($membre_row['Prenom']<>''){
						$markerArray['###PRENOM_Separateur###'] = $this->lConf['separateurPrenom'];
					}
					else{
						$markerArray['###PRENOM_Separateur###'] = '';
					}
					
					
					// Afficher les initiales d'un membre

					// On sépare les prénoms s'ils contiennent un - (cas des prénoms composés)
					$prenoms = explode("-",$membre_row['Prenom']);
					$initiales_prenom = "";
					$premier_prenom = true;
					// Pour chaque prénom, on récupère l'initiale. On sépare ces initiales par des tirets
					foreach ($prenoms as $prenom_courant) {
						if($premier_prenom != true)
						{
							$initiales_prenom = $initiales_prenom."-";
						}
						$initiales_prenom = $initiales_prenom.substr($prenom_courant,0,1);
						$premier_prenom = false;
					}
					if($initiales_prenom != '')
					{
						$markerArray['###InitialePrenom###'] = $initiales_prenom.".";
					}
					
					
					
					
					if($membre_row['Prenom']<>''){
						$markerArray['###InitialePrenom_Separateur###'] = $this->lConf['separateurInitialePrenom'];
					}
					else{
						$markerArray['###InitialePrenom_Separateur###'] = '';
					}


					$markerArray['###InitialeNom###'] = substr($membre_row['NomDUsage'],0,1).'.';

					if($membre_row['NomDUsage']<>''){
						$markerArray['###InitialeNom_Separateur###'] = $this->lConf['separateurInitialeNom'];
					}
					else{
						$markerArray['###InitialeNom_Separateur###'] = '';
					}


					if($membre_row['Genre']=='H'){
						$markerArray['###Genre###'] = $this->lConf['genrehomme'];
						
						if($this->lConf['genrehomme']<>''){
							$markerArray['###Genre_Separateur###'] = $this->lConf['separateurGenre'];
						}
						else{
							$markerArray['###Genre_Separateur###'] = '';
						}
					}
					else if($membre_row['Genre']=='F'){
						$markerArray['###Genre###'] = $this->lConf['genrefemme'];
						
						if($this->lConf['genrefemme']<>''){
							$markerArray['###Genre_Separateur###'] = $this->lConf['separateurGenre'];
						}
						else{
							$markerArray['###Genre_Separateur###'] = '';
						}
					}
					else{
						$markerArray['###Genre###'] = $this->lConf['genreinconnu'];
						
						if($this->lConf['genreinconnu']<>''){
							$markerArray['###Genre_Separateur###'] = $this->lConf['separateurGenre'];
						}
						else{
							$markerArray['###Genre_Separateur###'] = '';
						}
					}
			
					
					if($membre_row['DateNaissance']=='0000-00-00'){
						$markerArray['###DateNaissance###'] = $this->lConf['datenaissance'];
						
						if($this->lConf['datenaissance']<>''){
							$markerArray['###DateNaissance_Separateur###'] = $this->lConf['separateurDateNaissance'];
						}
						else{
							$markerArray['###DateNaissance_Separateur###'] = '';
						}
					}
					else{

						$date_explosee = explode("-", $membre_row['DateNaissance']);

						$annee = (int)$date_explosee[0];
						$mois = (int)$date_explosee[1];
						$jour = (int)$date_explosee[2];

						// la fonction date permet de reformater une date au format souhaité
						$markerArray['###DateNaissance###'] = date($this->lConf['formatdate'],mktime(0, 0, 0, $mois, $jour, $annee));
						
						
						if($membre_row['DateNaissance']<>''){
							$markerArray['###DateNaissance_Separateur###'] = $this->lConf['separateurDateNaissance'];
						}
						else{
							$markerArray['###DateNaissance_Separateur###'] = '';
						}
					}

					
					$markerArray['###Nationalite###'] = $membre_row['Nationalite'];
					if($membre_row['Nationalite']<>''){
						$markerArray['###Nationalite_Separateur###'] = $this->lConf['separateurNationalite'];
					}
					else{
						$markerArray['###Nationalite_Separateur###'] = '';
					}
					
					$markerArray['###NATIONALITE###'] = mb_strtoupper($membre_row['Nationalite'],"UTF-8");
					if($membre_row['Nationalite']<>''){
						$markerArray['###NATIONALITE_Separateur###'] = $this->lConf['separateurNationalite'];
					}
					else{
						$markerArray['###NATIONALITE_Separateur###'] = '';
					}
					
					
					if($membre_row['DateArrivee']=='0000-00-00'){
						$markerArray['###DateArrivee###'] = $this->lConf['datearrivee'];
						
						if($this->lConf['datearrivee']<>''){
							$markerArray['###DateArrivee_Separateur###'] = $this->lConf['separateurDateArrivee'];
						}
						else{
							$markerArray['###DateArrivee_Separateur###'] = '';
						}
					}
					else{
						$date_explosee = explode("-", $membre_row['DateArrivee']);

						$annee = (int)$date_explosee[0];
						$mois = (int)$date_explosee[1];
						$jour = (int)$date_explosee[2];

						// la fonction date permet de reformater une date au format souhaité
						$markerArray['###DateArrivee###'] = date($this->lConf['formatdate'],mktime(0, 0, 0, $mois, $jour, $annee));
						
						
						if($membre_row['DateArrivee']<>''){
							$markerArray['###DateArrivee_Separateur###'] = $this->lConf['separateurDateArrivee'];
						}
						else{
							$markerArray['###DateArrivee_Separateur###'] = '';
						}
					}

					
					if($membre_row['DateSortie']=='0000-00-00'){
						$markerArray['###DateSortie###'] = $this->lConf['datesortie'];
						
						if($this->lConf['datesortie']<>''){
							$markerArray['###DateSortie_Separateur###'] = $this->lConf['separateurDateSortie'];
						}
						else{
							$markerArray['###DateSortie_Separateur###'] = '';
						}
					}
					else{

						$date_explosee = explode("-", $membre_row['DateSortie']);

						$annee = (int)$date_explosee[0];
						$mois = (int)$date_explosee[1];
						$jour = (int)$date_explosee[2];

						// la fonction date permet de reformater une date au format souhaité
						$markerArray['###DateSortie###'] = date($this->lConf['formatdate'],mktime(0, 0, 0, $mois, $jour, $annee));
						
						
						if($membre_row['DateSortie']<>''){
							$markerArray['###DateSortie_Separateur###'] = $this->lConf['separateurDateSortie'];
						}
						else{
							$markerArray['###DateSortie_Separateur###'] = '';
						}
					}
			
					
					$markerArray['###NumINE###'] = $membre_row['NumINE'];
					if($membre_row['NumINE']<>''){
						$markerArray['###NumINE_Separateur###'] = $this->lConf['separateurNumINE'];
					}
					else{
						$markerArray['###NumINE_Separateur###'] = '';
					}
					
					
					$markerArray['###SectionCNU###'] = $membre_row['SectionCNU'];
					if($membre_row['SectionCNU']<>''){
						$markerArray['###SectionCNU_Separateur###'] = $this->lConf['separateurSectionCNU'];
					}
					else{
						$markerArray['###SectionCNU_Separateur###'] = '';
					}

					$markerArray['###CoordonneesRecherche###'] = nl2br($membre_row['CoordonneesRecherche']);
					if($membre_row['CoordonneesRecherche']<>''){
						$markerArray['###CoordonneesRecherche_Separateur###'] = $this->lConf['separateurCoordonneesRecherche'];
					}
					else{
						$markerArray['###CoordonneesRecherche_Separateur###'] = '';
					}
					
					
					$markerArray['###CoordonneesRecherche_Ligne###'] = $membre_row['CoordonneesRecherche'];
					if($membre_row['CoordonneesRecherche']<>''){
						$markerArray['###CoordonneesRecherche_Ligne_Separateur###'] = $this->lConf['separateurCoordonneesRecherche'];
					}
					else{
						$markerArray['###CoordonneesRecherche_Ligne_Separateur###'] = '';
					}
					
					
					$markerArray['###CoordonneesEnseignement###'] = nl2br($membre_row['CoordonneesEnseignement']);
					if($membre_row['CoordonneesEnseignement']<>''){
						$markerArray['###CoordonneesEnseignement_Separateur###'] = $this->lConf['separateurCoordonneesEnseignement'];
					}
					else{
						$markerArray['###CoordonneesEnseignement_Separateur###'] = '';
					}
					
					$markerArray['###CoordonneesEnseignement_Ligne###'] = $membre_row['CoordonneesEnseignement'];
					if($membre_row['CoordonneesEnseignement']<>''){
						$markerArray['###CoordonneesEnseignement_Ligne_Separateur###'] = $this->lConf['separateurCoordonneesEnseignement'];
					}
					else{
						$markerArray['###CoordonneesEnseignement_Ligne_Separateur###'] = '';
					}

					
					$markerArray['###CoordonneesPersonnelles###'] = nl2br($membre_row['CoordonneesPersonnelles']);
					if($membre_row['CoordonneesPersonnelles']<>''){
						$markerArray['###CoordonneesPersonnelles_Separateur###'] = $this->lConf['separateurCoordonneesPersonnelles'];
					}
					else{
						$markerArray['###CoordonneesPersonnelles_Separateur###'] = '';
					}
					
					$markerArray['###CoordonneesPersonnelles_Ligne###'] = $membre_row['CoordonneesPersonnelles'];
					if($membre_row['CoordonneesPersonnelles']<>''){
						$markerArray['###CoordonneesPersonnelles_Ligne_Separateur###'] = $this->lConf['separateurCoordonneesPersonnelles'];
					}
					else{
						$markerArray['###CoordonneesPersonnelles_Ligne_Separateur###'] = '';
					}
					
					
					$markerArray['###email###'] = $membre_row['email'];

					if($membre_row['email']<>''){
						$markerArray['###email_Separateur###'] = $this->lConf['separateuremail'];
					}
					else{
						$markerArray['###email_Separateur###'] = '';
					}
					
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

			
					if($membre_row['PageWeb']<>''){
						$markerArray['###PageWeb_Separateur###'] = $this->lConf['separateurPageWeb'];
					}
					else{
						$markerArray['###PageWeb_Separateur###'] = '';
					}
			
				}

			
			
			//**************************************
			// Tables Dirige
			//**************************************
				$contentDirige = '';
				$contentDirige_estdirecteur = '';
				$contentDirige_nestpas_directeur = '';

				$Dirige_select_fields = "tx_ligesttheses_Dirige.uid AS uiddirige, tx_ligesttheses_Dirige.*, tx_ligestmembrelabo_MembreDuLabo.*";
				$Dirige_from_table = "tx_ligesttheses_Dirige, tx_ligestmembrelabo_MembreDuLabo";
				$Dirige_where_clause = "tx_ligesttheses_Dirige.idTheseHDR = ".$uid." AND tx_ligesttheses_Dirige.idMembreLabo = tx_ligestmembrelabo_MembreDuLabo.uid AND tx_ligesttheses_Dirige.deleted<>1 AND tx_ligestmembrelabo_MembreDuLabo.deleted<>1";
				$Dirige_groupBy = "";
				$Dirige_orderBy = "tx_ligesttheses_Dirige.DateDebut DESC";
				$Dirige_limit = "";

				$Dirige_res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($Dirige_select_fields, $Dirige_from_table, $Dirige_where_clause, $Dirige_groupBy, $Dirige_orderBy, $Dirige_limit);

				while($dirige_row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($Dirige_res))       {

				
					$markerArray_Dirige['###Dirige_CoefficientDEncadrement###'] = $dirige_row['CoefficientDEncadrement'];
					if($dirige_row['CoefficientDEncadrement']<>''){
						$markerArray_Dirige['###Dirige_CoefficientDEncadrement_Separateur###'] = $this->lConf['separateurCoefficientDEncadrement'];
					}
					else{
						$markerArray_Dirige['###Dirige_CoefficientDEncadrement_Separateur###'] = '';
					}

					
					if($dirige_row['EstDirecteur']=='V')
					{
						$markerArray_Dirige['###Dirige_EstDirecteur###'] = $this->lConf['estDirecteur'];
						if($this->lConf['estDirecteur']<>''){
							$markerArray_Dirige['###Dirige_EstDirecteur_Separateur###'] = $this->lConf['separateurEstDirecteur'];
						}
						else{
							$markerArray_Dirige['###Dirige_EstDirecteur_Separateur###'] = '';
						}
					}
					else
					{
						$markerArray_Dirige['###Dirige_EstDirecteur###'] = $this->lConf['NEstPasDirecteur'];
						if($this->lConf['NEstPasDirecteur']<>''){
							$markerArray_Dirige['###Dirige_EstDirecteur_Separateur###'] = $this->lConf['separateurEstDirecteur'];
						}
						else{
							$markerArray_Dirige['###Dirige_EstDirecteur_Separateur###'] = '';
						}
					}
					
					
					

					
					
					if($dirige_row['DateDebut']=='0000-00-00'){
						$markerArray_Dirige['###Dirige_DateDebut###'] = $this->lConf['datedebutthesehdr'];
						if($this->lConf['datedebutthesehdr']<>''){
							$markerArray_Dirige['###Dirige_DateDebut_Separateur###'] = $this->lConf['separateurDirigeDateDebut'];
						}
						else{
							$markerArray_Dirige['###Dirige_DateDebut_Separateur###'] = '';
						}
					}
					else{

						$date_explosee = explode("-", $dirige_row['DateDebut']);

						$annee = (int)$date_explosee[0];
						$mois = (int)$date_explosee[1];
						$jour = (int)$date_explosee[2];

						// la fonction date permet de reformater une date au format souhaité
						$markerArray_Dirige['###Dirige_DateDebut###'] = date($this->lConf['formatdate'],mktime(0, 0, 0, $mois, $jour, $annee));
						
						
						if($dirige_row['DateDebut']<>''){
							$markerArray_Dirige['###Dirige_DateDebut_Separateur###'] = $this->lConf['separateurDirigeDateDebut'];
						}
						else{
							$markerArray_Dirige['###Dirige_DateDebut_Separateur###'] = '';
						}
					}
			
			
					if($dirige_row['DateFin']=='0000-00-00'){
						$markerArray_Dirige['###Dirige_DateFin###'] = $this->lConf['datefinthesehdr'];
						if($this->lConf['datefinthesehdr']<>''){
							$markerArray_Dirige['###Dirige_DateFin_Separateur###'] = $this->lConf['separateurDirigeDateFin'];
						}
						else{
							$markerArray_Dirige['###Dirige_DateFin_Separateur###'] = '';
						}
					}
					else{

						$date_explosee = explode("-", $dirige_row['DateFin']);

						$annee = (int)$date_explosee[0];
						$mois = (int)$date_explosee[1];
						$jour = (int)$date_explosee[2];

						// la fonction date permet de reformater une date au format souhaité
						$markerArray_Dirige['###Dirige_DateFin###'] = date($this->lConf['formatdate'],mktime(0, 0, 0, $mois, $jour, $annee));
						
						
						if($dirige_row['DateFin']<>''){
							$markerArray_Dirige['###Dirige_DateFin_Separateur###'] = $this->lConf['separateurDirigeDateFin'];
						}
						else{
							$markerArray_Dirige['###Dirige_DateFin_Separateur###'] = '';
						}
					}
						
					
					
					$markerArray_Dirige['###Dirige_NomDUsage###'] = $dirige_row['NomDUsage'];
					if($dirige_row['NomDUsage']<>''){
						$markerArray_Dirige['###Dirige_NomDUsage_Separateur###'] = $this->lConf['separateurNomDUsage'];
					}
					else{
						$markerArray_Dirige['###Dirige_NomDUsage_Separateur###'] = '';
					}
					$markerArray_Dirige['###Dirige_NOMDUSAGE###'] = mb_strtoupper($dirige_row['NomDUsage'],"UTF-8");
					if($dirige_row['NomDUsage']<>''){
						$markerArray_Dirige['###Dirige_NOMDUSAGE_Separateur###'] = $this->lConf['separateurNomDUsage'];
					}
					else{
						$markerArray_Dirige['###Dirige_NOMDUSAGE_Separateur###'] = '';
					}


					$markerArray_Dirige['###Dirige_NomMarital###'] = $dirige_row['NomMarital'];
					if($dirige_row['NomMarital']<>''){
						$markerArray_Dirige['###Dirige_NomMarital_Separateur###'] = $this->lConf['separateurNomMarital'];
					}
					else{
						$markerArray_Dirige['###Dirige_NomMarital_Separateur###'] = '';
					}
					
					$markerArray_Dirige['###Dirige_NOMMARITAL###'] = mb_strtoupper($dirige_row['NomMarital'],"UTF-8");
					if($dirige_row['NomMarital']<>''){
						$markerArray_Dirige['###Dirige_NOMMARITAL_Separateur###'] = $this->lConf['separateurNomMarital'];
					}
					else{
						$markerArray_Dirige['###Dirige_NOMMARITAL_Separateur###'] = '';
					}
					
					
					$markerArray_Dirige['###Dirige_NomPreMarital###'] = $dirige_row['NomPreMarital'];
					if($dirige_row['NomPreMarital']<>''){
						$markerArray_Dirige['###Dirige_NomPreMarital_Separateur###'] = $this->lConf['separateurNomPreMarital'];
					}
					else{
						$markerArray_Dirige['###Dirige_NomPreMarital_Separateur###'] = '';
					}
					
					$markerArray_Dirige['###Dirige_NOMPREMARITAL###'] = mb_strtoupper($dirige_row['NomPreMarital'],"UTF-8");
					if($dirige_row['NomPreMarital']<>''){
						$markerArray_Dirige['###Dirige_NOMPREMARITAL_Separateur###'] = $this->lConf['separateurNomPreMarital'];
					}
					else{
						$markerArray_Dirige['###Dirige_NOMPREMARITAL_Separateur###'] = '';
					}
					
					
					$markerArray_Dirige['###Dirige_Prenom###'] = $dirige_row['Prenom'];
					if($dirige_row['Prenom']<>''){
						$markerArray_Dirige['###Dirige_Prenom_Separateur###'] = $this->lConf['separateurPrenom'];
					}
					else{
						$markerArray_Dirige['###Dirige_Prenom_Separateur###'] = '';
					}
					
					
					$markerArray_Dirige['###Dirige_PRENOM###'] = mb_strtoupper($dirige_row['Prenom'],"UTF-8");
					if($dirige_row['Prenom']<>''){
						$markerArray_Dirige['###Dirige_PRENOM_Separateur###'] = $this->lConf['separateurPrenom'];
					}
					else{
						$markerArray_Dirige['###Dirige_PRENOM_Separateur###'] = '';
					}
					
					
					// Afficher les initiales d'un membre

					// On sépare les prénoms s'ils contiennent un - (cas des prénoms composés)
					$prenoms = explode("-",$dirige_row['Prenom']);
					$initiales_prenom = "";
					$premier_prenom = true;
					// Pour chaque prénom, on récupère l'initiale. On sépare ces initiales par des tirets
					foreach ($prenoms as $prenom_courant) {
						if($premier_prenom != true)
						{
							$initiales_prenom = $initiales_prenom."-";
						}
						$initiales_prenom = $initiales_prenom.substr($prenom_courant,0,1);
						$premier_prenom = false;
					}
					if($initiales_prenom != '')
					{
						$markerArray_Dirige['###Dirige_InitialePrenom###'] = $initiales_prenom.".";
					}
					
					
					
					
					
					if($dirige_row['Prenom']<>''){
						$markerArray_Dirige['###Dirige_InitialePrenom_Separateur###'] = $this->lConf['separateurInitialePrenom'];
					}
					else{
						$markerArray_Dirige['###Dirige_InitialePrenom_Separateur###'] = '';
					}




					$markerArray_Dirige['###Dirige_InitialeNom###'] = substr($dirige_row['NomDUsage'],0,1).".";

					if($dirige_row['NomDUsage']<>''){
						$markerArray_Dirige['###Dirige_InitialeNom_Separateur###'] = $this->lConf['separateurInitialeNom'];
					}
					else{
						$markerArray_Dirige['###Dirige_InitialeNom_Separateur###'] = '';
					}

							
					
					

					if($dirige_row['Genre']=='H'){
						$markerArray_Dirige['###Dirige_Genre###'] = $this->lConf['genrehomme'];
						
						if($this->lConf['genrehomme']<>''){
							$markerArray_Dirige['###Dirige_Genre_Separateur###'] = $this->lConf['separateurGenre'];
						}
						else{
							$markerArray_Dirige['###Dirige_Genre_Separateur###'] = '';
						}
					}
					else if($dirige_row['Genre']=='F'){
						$markerArray_Dirige['###Dirige_Genre###'] = $this->lConf['genrefemme'];
						
						if($this->lConf['genrefemme']<>''){
							$markerArray_Dirige['###Dirige_Genre_Separateur###'] = $this->lConf['separateurGenre'];
						}
						else{
							$markerArray_Dirige['###Dirige_Genre_Separateur###'] = '';
						}
					}
					else{
						$markerArray_Dirige['###Dirige_Genre###'] = $this->lConf['genreinconnu'];
						
						if($this->lConf['genreinconnu']<>''){
							$markerArray_Dirige['###Dirige_Genre_Separateur###'] = $this->lConf['separateurGenre'];
						}
						else{
							$markerArray_Dirige['###Dirige_Genre_Separateur###'] = '';
						}
					}
			
					
					if($dirige_row['DateNaissance']=='0000-00-00'){
						$markerArray_Dirige['###Dirige_DateNaissance###'] = $this->lConf['datenaissance'];
						
						if($this->lConf['datenaissance']<>''){
							$markerArray_Dirige['###Dirige_DateNaissance_Separateur###'] = $this->lConf['separateurDateNaissance'];
						}
						else{
							$markerArray_Dirige['###Dirige_DateNaissance_Separateur###'] = '';
						}
					}
					else{

						$date_explosee = explode("-", $dirige_row['DateNaissance']);

						$annee = (int)$date_explosee[0];
						$mois = (int)$date_explosee[1];
						$jour = (int)$date_explosee[2];

						// la fonction date permet de reformater une date au format souhaité
						$markerArray_Dirige['###Dirige_DateNaissance###'] = date($this->lConf['formatdate'],mktime(0, 0, 0, $mois, $jour, $annee));
						
						
						if($dirige_row['DateNaissance']<>''){
							$markerArray_Dirige['###Dirige_DateNaissance_Separateur###'] = $this->lConf['separateurDateNaissance'];
						}
						else{
							$markerArray_Dirige['###Dirige_DateNaissance_Separateur###'] = '';
						}
					}

					
					$markerArray_Dirige['###Dirige_Nationalite###'] = $dirige_row['Nationalite'];
					if($dirige_row['Nationalite']<>''){
						$markerArray_Dirige['###Dirige_Nationalite_Separateur###'] = $this->lConf['separateurNationalite'];
					}
					else{
						$markerArray_Dirige['###Dirige_Nationalite_Separateur###'] = '';
					}
					
					$markerArray_Dirige['###Dirige_NATIONALITE###'] = mb_strtoupper($dirige_row['Nationalite'],"UTF-8");
					if($dirige_row['Nationalite']<>''){
						$markerArray_Dirige['###Dirige_NATIONALITE_Separateur###'] = $this->lConf['separateurNationalite'];
					}
					else{
						$markerArray_Dirige['###Dirige_NATIONALITE_Separateur###'] = '';
					}
					
					
					if($dirige_row['DateArrivee']=='0000-00-00'){
						$markerArray_Dirige['###Dirige_DateArrivee###'] = $this->lConf['datearrivee'];
						
						if($this->lConf['datearrivee']<>''){
							$markerArray_Dirige['###Dirige_DateArrivee_Separateur###'] = $this->lConf['separateurDateArrivee'];
						}
						else{
							$markerArray_Dirige['###Dirige_DateArrivee_Separateur###'] = '';
						}
					}
					else{

						$date_explosee = explode("-", $dirige_row['DateArrivee']);

						$annee = (int)$date_explosee[0];
						$mois = (int)$date_explosee[1];
						$jour = (int)$date_explosee[2];

						// la fonction date permet de reformater une date au format souhaité
						$markerArray_Dirige['###Dirige_DateArrivee###'] = date($this->lConf['formatdate'],mktime(0, 0, 0, $mois, $jour, $annee));
						
						if($dirige_row['DateArrivee']<>''){
							$markerArray_Dirige['###Dirige_DateArrivee_Separateur###'] = $this->lConf['separateurDateArrivee'];
						}
						else{
							$markerArray_Dirige['###Dirige_DateArrivee_Separateur###'] = '';
						}
					}

					
					if($dirige_row['DateSortie']=='0000-00-00'){
						$markerArray_Dirige['###Dirige_DateSortie###'] = $this->lConf['datesortie'];
						
						if($this->lConf['datesortie']<>''){
							$markerArray_Dirige['###Dirige_DateSortie_Separateur###'] = $this->lConf['separateurDateSortie'];
						}
						else{
							$markerArray_Dirige['###Dirige_DateSortie_Separateur###'] = '';
						}
					}
					else{

						$date_explosee = explode("-", $dirige_row['DateSortie']);

						$annee = (int)$date_explosee[0];
						$mois = (int)$date_explosee[1];
						$jour = (int)$date_explosee[2];

						// la fonction date permet de reformater une date au format souhaité
						$markerArray_Dirige['###Dirige_DateSortie###'] = date($this->lConf['formatdate'],mktime(0, 0, 0, $mois, $jour, $annee));
						
						if($dirige_row['DateSortie']<>''){
							$markerArray_Dirige['###Dirige_DateSortie_Separateur###'] = $this->lConf['separateurDateSortie'];
						}
						else{
							$markerArray_Dirige['###Dirige_DateSortie_Separateur###'] = '';
						}
					}
			
					
					$markerArray_Dirige['###Dirige_NumINE###'] = $dirige_row['NumINE'];
					if($dirige_row['NumINE']<>''){
						$markerArray_Dirige['###Dirige_NumINE_Separateur###'] = $this->lConf['separateurNumINE'];
					}
					else{
						$markerArray_Dirige['###Dirige_NumINE_Separateur###'] = '';
					}
					
					
					$markerArray_Dirige['###Dirige_SectionCNU###'] = $dirige_row['SectionCNU'];
					if($dirige_row['SectionCNU']<>''){
						$markerArray_Dirige['###Dirige_SectionCNU_Separateur###'] = $this->lConf['separateurSectionCNU'];
					}
					else{
						$markerArray_Dirige['###Dirige_SectionCNU_Separateur###'] = '';
					}

					$markerArray_Dirige['###Dirige_CoordonneesRecherche###'] = nl2br($dirige_row['CoordonneesRecherche']);
					if($dirige_row['CoordonneesRecherche']<>''){
						$markerArray_Dirige['###Dirige_CoordonneesRecherche_Separateur###'] = $this->lConf['separateurCoordonneesRecherche'];
					}
					else{
						$markerArray_Dirige['###Dirige_CoordonneesRecherche_Separateur###'] = '';
					}
					
					
					$markerArray_Dirige['###Dirige_CoordonneesRecherche_Ligne###'] = $dirige_row['CoordonneesRecherche'];
					if($dirige_row['CoordonneesRecherche']<>''){
						$markerArray_Dirige['###Dirige_CoordonneesRecherche_Ligne_Separateur###'] = $this->lConf['separateurCoordonneesRecherche'];
					}
					else{
						$markerArray_Dirige['###Dirige_CoordonneesRecherche_Ligne_Separateur###'] = '';
					}
					
					
					$markerArray_Dirige['###Dirige_CoordonneesEnseignement###'] = nl2br($dirige_row['CoordonneesEnseignement']);
					if($dirige_row['CoordonneesEnseignement']<>''){
						$markerArray_Dirige['###Dirige_CoordonneesEnseignement_Separateur###'] = $this->lConf['separateurCoordonneesEnseignement'];
					}
					else{
						$markerArray_Dirige['###Dirige_CoordonneesEnseignement_Separateur###'] = '';
					}
					
					$markerArray_Dirige['###Dirige_CoordonneesEnseignement_Ligne###'] = $dirige_row['CoordonneesEnseignement'];
					if($dirige_row['CoordonneesEnseignement']<>''){
						$markerArray_Dirige['###Dirige_CoordonneesEnseignement_Ligne_Separateur###'] = $this->lConf['separateurCoordonneesEnseignement'];
					}
					else{
						$markerArray_Dirige['###Dirige_CoordonneesEnseignement_Ligne_Separateur###'] = '';
					}

					
					$markerArray_Dirige['###Dirige_CoordonneesPersonnelles###'] = nl2br($dirige_row['CoordonneesPersonnelles']);
					if($dirige_row['CoordonneesPersonnelles']<>''){
						$markerArray_Dirige['###Dirige_CoordonneesPersonnelles_Separateur###'] = $this->lConf['separateurCoordonneesPersonnelles'];
					}
					else{
						$markerArray_Dirige['###Dirige_CoordonneesPersonnelles_Separateur###'] = '';
					}
					
					$markerArray_Dirige['###Dirige_CoordonneesPersonnelles_Ligne###'] = $dirige_row['CoordonneesPersonnelles'];
					if($dirige_row['CoordonneesPersonnelles']<>''){
						$markerArray_Dirige['###Dirige_CoordonneesPersonnelles_Ligne_Separateur###'] = $this->lConf['separateurCoordonneesPersonnelles'];
					}
					else{
						$markerArray_Dirige['###Dirige_CoordonneesPersonnelles_Ligne_Separateur###'] = '';
					}
					
					
					$markerArray_Dirige['###Dirige_email###'] = $dirige_row['email'];

					if($dirige_row['email']<>''){
						$markerArray_Dirige['###Dirige_email_Separateur###'] = $this->lConf['separateuremail'];
					}
					else{
						$markerArray_Dirige['###Dirige_email_Separateur###'] = '';
					}
					
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
					$wrappedSubpartContentArray['###Dirige_PageWebLien###'] = $this->local_cObj->typolinkWrap($temp_conf);

			
					if($dirige_row['PageWeb']<>''){
						$markerArray_Dirige['###Dirige_PageWeb_Separateur###'] = $this->lConf['separateurPageWeb'];
					}
					else{
						$markerArray_Dirige['###Dirige_PageWeb_Separateur###'] = '';
					}






					if($dirige_row['EstDirecteur']=='V')
					{
						$contentDirige_estdirecteur .= $this->cObj->substituteMarkerArrayCached($template['dirige_estdirecteur'],$markerArray_Dirige,array(),$wrappedSubpartContentArray_Dirige);
					}
					else
					{
						$contentDirige_nestpas_directeur .= $this->cObj->substituteMarkerArrayCached($template['dirige_nestpas_directeur'],$markerArray_Dirige,array(),$wrappedSubpartContentArray_Dirige);
					}
					
					$contentDirige .= $this->cObj->substituteMarkerArrayCached($template['dirige'],$markerArray_Dirige,array(),$wrappedSubpartContentArray_Dirige);

				}

				$subpartArray_Item['###DIRIGE###'] = $contentDirige;
				$subpartArray_Item['###DIRIGE_EST_DIRECTEUR###'] = $contentDirige_estdirecteur;
				$subpartArray_Item['###DIRIGE_NESTPAS_DIRECTEUR###'] = $contentDirige_nestpas_directeur;



			//**************************************
			// Tables Cotutelle
			//**************************************
				$contentCotutelle = '';

				$Cotutelle_select_fields = "tx_ligesttheses_Cotutelle.uid AS uidcotutelle, tx_ligesttheses_Cotutelle.*";
				$Cotutelle_from_table = "tx_ligesttheses_Cotutelle";
				$Cotutelle_where_clause = "tx_ligesttheses_Cotutelle.idTheseHDR = ".$uid." AND tx_ligesttheses_Cotutelle.deleted<>1";
				$Cotutelle_groupBy = "";
				$Cotutelle_orderBy = "tx_ligesttheses_Cotutelle.NomCotutelle, tx_ligesttheses_Cotutelle.PrenomCotutelle";
				$Cotutelle_limit = "";

				$Cotutelle_res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($Cotutelle_select_fields, $Cotutelle_from_table, $Cotutelle_where_clause, $Cotutelle_groupBy, $Cotutelle_orderBy, $Cotutelle_limit);

				while($Cotutelle_row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($Cotutelle_res))       {

				
					$markerArray_Cotutelle['###Cotutelle_Nom###'] = $Cotutelle_row['NomCotutelle'];
					if($Cotutelle_row['NomCotutelle']<>''){
						$markerArray_Cotutelle['###Cotutelle_Nom_Separateur###'] = $this->lConf['separateurNomCotutelle'];
					}
					else{
						$markerArray_Cotutelle['###Cotutelle_Nom_Separateur###'] = '';
					}
					
					$markerArray_Cotutelle['###Cotutelle_Prenom###'] = $Cotutelle_row['PrenomCotutelle'];
					if($Cotutelle_row['PrenomCotutelle']<>''){
						$markerArray_Cotutelle['###Cotutelle_Prenom_Separateur###'] = $this->lConf['separateurPrenomCotutelle'];
					}
					else{
						$markerArray_Cotutelle['###Cotutelle_Prenom_Separateur###'] = '';
					}
					
					$markerArray_Cotutelle['###Cotutelle_Adresse###'] = nl2br($Cotutelle_row['AdresseCotutelle']);
					if($Cotutelle_row['AdresseCotutelle']<>''){
						$markerArray_Cotutelle['###Cotutelle_Adresse_Separateur###'] = $this->lConf['separateurAdresseCotutelle'];
					}
					else{
						$markerArray_Cotutelle['###Cotutelle_Adresse_Separateur###'] = '';
					}

					
					$contentCotutelle .= $this->cObj->substituteMarkerArrayCached($template['cotutelle'],$markerArray_Cotutelle,array(),$wrappedSubpartContentArray_Cotutelle);
					
				}
				
				$subpartArray_Item['###COTUTELLE###'] = $contentCotutelle;


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