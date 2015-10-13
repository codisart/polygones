<?php
namespace Utility;




/**
* collection de polygone.
*/
class Math {

	/**
	 * permet d'utiliser la fonction min de php sans renvoyer les valeurs false (NULL, FALSE, "")
	 * @return mixed la valeur du paramètre considéré comme "inférieure" suivant la comparaison standard
	 */
	public static function min() {
		$variables = array_filter(func_get_args(), 'strlen');
		return min($variables);
	}

	/**
	 * permet d'utiliser la fonction min de php sans renvoyer les valeurs false (NULL, FALSE, "")
	 * @return mixed la valeur du paramètre considéré comme "inférieure" suivant la comparaison standard
	 */
	public static function max() {
		$variables = array_filter(func_get_args(), 'strlen');
		return max($variables);
	}

	/**
	 *
	 */
	public static function isBetween($int, $first, $second) {
		$min = min($first, $second);
		$max = max($first, $second);
		return ($min < $int && $int < $max);
	}

	/**
	 * [determinant description]
	 * @param  Segment $vertexOne    [description]
	 * @param  Segment $vertexSecond [description]
	 * @return integer               [description]
	 */
	public static function determinant(Segment $vertexOne, Segment $vertexSecond) {
		$abscissaVertexOne = $vertexOne->getPointB()->getAbscisse() - $vertexOne->getPointA()->getAbscisse();
		$ordinateVertexOne = $vertexOne->getPointB()->getOrdonnee() - $vertexOne->getPointA()->getOrdonnee();
		$abscissaVertexSecond = $vertexSecond->getPointB()->getAbscisse() - $vertexSecond->getPointA()->getAbscisse();
		$ordinateVertexSecond = $vertexSecond->getPointB()->getOrdonnee() - $vertexSecond->getPointA()->getOrdonnee();

		return ($abscissaVertexOne * $ordinateVertexSecond) - ($abscissaVertexSecond * $ordinateVertexOne);
	}

	/**
	 * [scalarProduct description]
	 * @param  Segment $vertexOne    [description]
	 * @param  Segment $vertexSecond [description]
	 * @return integer                [description]
	 */
	public static function scalarProduct(Segment $vertexOne, Segment $vertexSecond) {
		$abscissaVertexOne = $vertexOne->getPointB()->getAbscisse() - $vertexOne->getPointA()->getAbscisse();
		$ordinateVertexOne = $vertexOne->getPointB()->getOrdonnee() - $vertexOne->getPointA()->getOrdonnee();
		$abscissaVertexSecond = $vertexSecond->getPointB()->getAbscisse() - $vertexSecond->getPointA()->getAbscisse();
		$ordinateVertexSecond = $vertexSecond->getPointB()->getOrdonnee() - $vertexSecond->getPointA()->getOrdonnee();

		return ($abscissaVertexOne * $abscissaVertexSecond) + ($ordinateVertexOne * $ordinateVertexSecond);
	}
}
