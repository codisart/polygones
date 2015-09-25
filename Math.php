<?php
namespace Anah\AnahOpah\Utility;




/**
* collection de polygone.
*/
class Math {

	/**
	 * permet d'utiliser la fonction min de php sans renvoyer les valeurs false (NULL, FALSE, "")
	 * @return mixed la valeur du paramètre considéré comme "inférieure" suivant la comparaison standard
	 */
	public function min() {
		$variables = array_filter(func_get_args(), 'strlen');
		return min($variables);
	}

	/**
	 * permet d'utiliser la fonction min de php sans renvoyer les valeurs false (NULL, FALSE, "")
	 * @return mixed la valeur du paramètre considéré comme "inférieure" suivant la comparaison standard
	 */
	public function max() {
		$variables = array_filter(func_get_args(), 'strlen');
		return max($variables);
	}

	/**
	 *
	 */
	public function isBetween($int, $first, $second) {
		$min = min($first, $second);
		$max = max($first, $second);
		return ($min < $int && $int < $max);
	}
}
