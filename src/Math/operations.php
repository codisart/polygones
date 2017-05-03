<?php
namespace Math;

/**
 * Find lowest value without considering falsy values (NULL, FALSE, "")
 * @param array ...$values
 * @return mixed returns the parameter value considered "lowest" according to standard comparisons.
 */
function min(...$values)
{
    return \min(
        array_filter($values, 'strlen')
    );
}

/**
 * Find highest value without considering falsy values (NULL, FALSE, "")
 * @param array ...$values
 * @return mixed returns the parameter value considered "lowest" according to standard comparisons.
 */
function max(...$values)
{
    return \max(
        array_filter($values, 'strlen')
    );
}

function isBetween($int, $first, $second)
{
    $min = \min($first, $second);
    $max = \max($first, $second);
    return ($min <= $int && $int <= $max);
}

function isStrictBetween($int, $first, $second)
{
    $min = \min($first, $second);
    $max = \max($first, $second);
    return ($min < $int && $int < $max);
}
