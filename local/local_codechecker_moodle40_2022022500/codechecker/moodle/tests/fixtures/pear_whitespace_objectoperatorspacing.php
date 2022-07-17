<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

// All these are correct, both spacing and indent.
$object->one()->two()->three();
$a = $object->one()->two()->three();

$object->one()->two()
    ->three();
$a = $object->one()->two()
    ->three();

$object->one()
    ->two()->three();
$a = $object->one()
    ->two()->three();

$object->one()
    ->two()
    ->three();

$a = $object->one()
    ->two()
    ->three();

get_object()->one()
    ->two()
    ->three();

someclass::one()
    ->two()
    ->three();

(new someclass())->one()
    ->two()
    ->three();

// These have incorrect (<4) indent.
$object->one()
  ->two()
  ->three();

$a = $object->one()
  ->two()
  ->three();

// These have incorrect (>4) indent.
$object->one()
      ->two()
      ->three();

$a = $object->one()
      ->two()
      ->three();

// These are not detected by the indent sniff. Probably an issue with it.
// Have tried some quick changes to PEAR_Sniffs_WhiteSpace_ObjectOperatorIndentSniff
// trying to support this, but leads to some false positives. So, keeping unmodified.
// More info: https://github.com/squizlabs/PHP_CodeSniffer/issues/2009
get_object()->one()
  ->two()
      ->three();

someclass::one()
  ->two()
      ->three();

(new someclass())->one()
  ->two()
      ->three();
