<?php



/**
 * Plugin 'Managing Member' for the 'li_gest_theses' extension.
 * Teste de la présence d'une date dans un formulaire
 * @author	Bruno Gallet <gallet.bruno@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_ligesttheses
 */
class tx_ligesttheses_dateObligatoire {

	/**
	 * Teste la présence ou non d'une date dans un champ
	 * @return Retourne la date de départ ou un message d'erreur si cette date est vide
	 */
	function returnFieldJS() {

			return "var modif = value;
				if (modif == '0000-00-00'){
					alert('Vous devez obligatoirement saisir une date!');
				}
				return value;";
	}


	/**
	 * Teste du champ lors de la validation du formulaire
	 * On teste si le champ est bien une date, sinon on met la date du jour
	 * On test si les séparateurs sont les bons, et s'il le faut, on les remplace
	 * @param $value Valeur du champ
	 * @return Retourne la nouvelle valeur du champ
	 */
	function evaluateFieldValue($value, $is_in, &$set) {
		if (!(checkdate(substr($value,5,2),substr($value,8,2),substr($value,0,4)))) {
			$value = date('Y-m-d');
		}
		/*else if{$value=='0000-00-00'){
			$value = date('Y-m-d');
		}*/
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

	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/li_gest_theses/class.tx_ligesttheses_dateObligatoire.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/li_gest_theses/class.tx_ligesttheses_dateObligatoire.php']);
}

?>