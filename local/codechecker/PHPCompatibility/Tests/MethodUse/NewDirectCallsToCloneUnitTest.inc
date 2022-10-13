<?php

/*
 * These are not calls to the __clone() magic method.
 */
$a = __clone();
BadPracticeClassConstant::__CLONE;
$propertyAccess = $obj->__clone;
BadPracticeClassProp::$__CLONE;
$notclone = $obj->_clone();
$notclone = $obj->cloning();

class ABC {
    function __clone() {}
}

/*
 * These are class internal calls to __clone(), this is fine.
 * See: https://github.com/PHPCompatibility/PHPCompatibility/issues/629#issuecomment-532607809
 */
class DEF {
    function something()
    {
		$clone = self::__clone();
		$clone = static::__Clone();
		$clone = parent::__CLONE();
    }
}

/*
 * These should be flagged.
 */
$a = (new ABC()) ->  /* comment */ __clone();
StaticClass::__clone();
