<?php
/**
 * These should be ok.
 */
$str = 'This is fake array: []';
$arr = array();
$arr[] = 'add a value';

/**
 * These should be flagged.
 */
$arr = [];
$arr = [1,2,3];
$arr[] = ['A','B'];

$arr[] = [
    'A',
    'B'
];
