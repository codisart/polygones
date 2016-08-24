<?php
namespace Math;

use Geometry\Segment;

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

	public static function isBetween($int, $first, $second) {
		$min = min($first, $second);
		$max = max($first, $second);
		return ($min <= $int && $int <= $max);
	}

	public static function isStrictBetween($int, $first, $second) {
		$min = min($first, $second);
		$max = max($first, $second);
		return ($min < $int && $int < $max);
	}

	public static function determinant(Segment $segmentOne, Segment $segmentSecond) {
		$abscissaVertexOne = $segmentOne->getPointB()->getAbscissa() - $segmentOne->getPointA()->getAbscissa();
		$ordinateVertexOne = $segmentOne->getPointB()->getOrdinate() - $segmentOne->getPointA()->getOrdinate();
		$abscissaVertexSecond = $segmentSecond->getPointB()->getAbscissa() - $segmentSecond->getPointA()->getAbscissa();
		$ordinateVertexSecond = $segmentSecond->getPointB()->getOrdinate() - $segmentSecond->getPointA()->getOrdinate();

		return ($abscissaVertexOne * $ordinateVertexSecond) - ($abscissaVertexSecond * $ordinateVertexOne);
	}
}