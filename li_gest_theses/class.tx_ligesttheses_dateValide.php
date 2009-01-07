<?php

/**
 * Plugin 'Managing Member' for the 'li_gest_theses' extension.
 * Teste de la validité d'une date dans un formulaire
 * @author	Bruno Gallet <gallet.bruno@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_ligesttheses
 */
class tx_ligesttheses_dateValide {

	/**
	 * Teste la validité d'une date dans un champ (enfin... à peu prêt... il peut y avoir un problème avec le mois de février....)
	 * @return Retourne la date de départ ou un message d'erreur si cette date est invalide
	 */
	function returnFieldJS() {

			return "var modif = value;
				if (modif!='0000-00-00')
				{
					var verif = /^[0-9]{4}[-]{1}[0-9]{2}[-]{1}[0-9]{2}$/;
					var verifMois = modif.substr(5,2);
					var verifJour = modif.substr(8,2);
					if (verif.exec(modif) == null){
						alert('La date saisie est incorrrect!');
					}
					else if (parseInt(verifMois,10) > 12){
						alert('La date saisie est incorrrect!');
					}
					else if (parseInt(verifMois,10) == 1 || parseInt(verifMois,10) == 3 || parseInt(verifMois,10) == 5 || parseInt(verifMois,10) == 7 || parseInt(verifMois,10) == 8 || parseInt(verifMois,10) == 10 || parseInt(verifMois,10) == 12){
						if (parseInt(verifJour,10) > 31){
							alert('La date saisie est incorrrect!');
						}
					}
					else
					{
						if (parseInt(verifJour,10) > 30){
							alert('La date saisie est incorrrect!');
						}
					}
				}				
				return value;";
	}

	/**
	 * Teste du champ lors de la validation du formulaire
	 * On teste si le champ est bien une date, sinon on met la date '0000-00-00'
	 * On test si les séparateurs sont les bons, et s'il le faut, on les remplace
	 * @param $value Valeur du champ
	 * @return Retourne la nouvelle valeur du champ
	 */
	function evaluateFieldValue($value, $is_in, &$set) {
		if (!(checkdate(substr($value,5,2),substr($value,8,2),substr($value,0,4)))) {
			$value = '0000-00-00';
		}
		//On test le premier séparteur
		if (!(substr($value,4,1)) != '-') {
			$value = substr_replace($value,'-',4,1);
		}
		//On test le second séparteur
		if (!(substr($value,7,1)) != '-') {
			$value = substr_replace($value,'-',7,1);
		}

		return $value;
	}

	/**
	 * Teste du champ avant l'affichage du formulaire
	 * On teste si le champ est bien une date, sinon on n'afficheun champ vide.
	 * @param $params Paramètres du champs du formulaire. $params['value'] contient la valeur du champ.
	 * @return Retourne la nouvelle valeur du champ
	 */
	function deevaluateFieldValue($params) {
		$dateTest = $params['value'];
		if (!(checkdate(substr($dateTest,5,2),substr($dateTest,8,2),substr($dateTest,0,4)))) {
			$dateTest='';
		}
		/*else if($dateTest=='0000-00-00')
		{
			$dateTest='';
		}*/
		return $dateTest;
	}
	
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/li_gest_theses/class.tx_ligesttheses_dateValide.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/li_gest_theses/class.tx_ligesttheses_dateValide.php']);
}

?>