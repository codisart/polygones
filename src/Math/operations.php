<?php
namespace Math;

/**
 * permet d'utiliser la fonction min de php sans renvoyer les valeurs false (NULL, FALSE, "")
 * @return mixed la valeur du paramètre considéré comme "inférieure" suivant la comparaison standard
 */
function min() {
	$variables = array_filter(func_get_args(), 'strlen');
	return \min($variables);
}

/**
 * permet d'utiliser la fonction min de php sans renvoyer les valeurs false (NULL, FALSE, "")
 * @return mixed la valeur du paramètre considéré comme "inférieure" suivant la comparaison standard
 */
function max() {
	$variables = array_filter(func_get_args(), 'strlen');
	return \max($variables);
}

function isBetween($int, $first, $second) {
	$min = \min($first, $second);
	$max = \max($first, $second);
	return ($min <= $int && $int <= $max);
}

function isStrictBetween($int, $first, $second) {
	$min = \min($first, $second);
	$max = \max($first, $second);
	return ($min < $int && $int < $max);
}
