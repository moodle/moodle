<?php

class ValueType extends Enum {
	public static $TBool;
	public static function TClass($c) { return new ValueType("TClass", 6, array($c)); }
	public static function TEnum($e) { return new ValueType("TEnum", 7, array($e)); }
	public static $TFloat;
	public static $TFunction;
	public static $TInt;
	public static $TNull;
	public static $TObject;
	public static $TUnknown;
	public static $__constructors = array(3 => 'TBool', 6 => 'TClass', 7 => 'TEnum', 2 => 'TFloat', 5 => 'TFunction', 1 => 'TInt', 0 => 'TNull', 4 => 'TObject', 8 => 'TUnknown');
	}
ValueType::$TBool = new ValueType("TBool", 3);
ValueType::$TFloat = new ValueType("TFloat", 2);
ValueType::$TFunction = new ValueType("TFunction", 5);
ValueType::$TInt = new ValueType("TInt", 1);
ValueType::$TNull = new ValueType("TNull", 0);
ValueType::$TObject = new ValueType("TObject", 4);
ValueType::$TUnknown = new ValueType("TUnknown", 8);
