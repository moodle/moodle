<?php

class com_wiris_util_json_parser_JType extends Enum {
	public static $TYPE_ARRAY;
	public static $TYPE_CONSTANT;
	public static $TYPE_HEURISTIC;
	public static $TYPE_NAME;
	public static $TYPE_NUMBER;
	public static $TYPE_OBJECT;
	public static $TYPE_STRING;
	public static $__constructors = array(0 => 'TYPE_ARRAY', 6 => 'TYPE_CONSTANT', 2 => 'TYPE_HEURISTIC', 3 => 'TYPE_NAME', 5 => 'TYPE_NUMBER', 1 => 'TYPE_OBJECT', 4 => 'TYPE_STRING');
	}
com_wiris_util_json_parser_JType::$TYPE_ARRAY = new com_wiris_util_json_parser_JType("TYPE_ARRAY", 0);
com_wiris_util_json_parser_JType::$TYPE_CONSTANT = new com_wiris_util_json_parser_JType("TYPE_CONSTANT", 6);
com_wiris_util_json_parser_JType::$TYPE_HEURISTIC = new com_wiris_util_json_parser_JType("TYPE_HEURISTIC", 2);
com_wiris_util_json_parser_JType::$TYPE_NAME = new com_wiris_util_json_parser_JType("TYPE_NAME", 3);
com_wiris_util_json_parser_JType::$TYPE_NUMBER = new com_wiris_util_json_parser_JType("TYPE_NUMBER", 5);
com_wiris_util_json_parser_JType::$TYPE_OBJECT = new com_wiris_util_json_parser_JType("TYPE_OBJECT", 1);
com_wiris_util_json_parser_JType::$TYPE_STRING = new com_wiris_util_json_parser_JType("TYPE_STRING", 4);
