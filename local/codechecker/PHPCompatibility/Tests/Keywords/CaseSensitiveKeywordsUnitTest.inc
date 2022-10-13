<?php

class MyClass {
	const SOMETHING = 123;

	public $prop = 'string';

	public function test() {
		// Correct usage.
		echo self::SOMETHING;
		echo static::SOMETHING;
		echo parent::SOMETHING;
		echo self::$prop;
		echo static::$prop;
		echo parent::$prop;

		// Problematic pre-PHP 5.5.
		echo SELF::SOMETHING;
		echo STATIC::SOMETHING;
		echo PARENT::SOMETHING;
		echo SELF::$prop;
		echo STATIC::$prop;
		echo PARENT::$prop;
	}
}
