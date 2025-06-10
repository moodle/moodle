<?php

class com_wiris_quizzes_test_LockTester {
	public function __construct() { 
	}
	public function run() {
		$v = new com_wiris_quizzes_impl_SharedVariables();
		$v->lockVariable(com_wiris_quizzes_test_LockTester::$VAR_TESTLOCKVARIABLES);
		$num = $v->getVariable(com_wiris_quizzes_test_LockTester::$VAR_TESTLOCKVARIABLES);
		$n = 0;
		if($num !== null) {
			$n = Std::parseInt($num);
		}
		$n++;
		$v->setVariable(com_wiris_quizzes_test_LockTester::$VAR_TESTLOCKVARIABLES, "" . _hx_string_rec($n, ""));
		haxe_Log::trace("" . _hx_string_rec($n, ""), _hx_anonymous(array("fileName" => "LockTester.hx", "lineNumber" => 21, "className" => "com.wiris.quizzes.test.LockTester", "methodName" => "run")));
		$v->unlockVariable(com_wiris_quizzes_test_LockTester::$VAR_TESTLOCKVARIABLES);
	}
	static $VAR_TESTLOCKVARIABLES = "testlockvariables";
	static function main($argv) {
		$t = new com_wiris_quizzes_test_LockTester();
		$t->run();
	}
	function __toString() { return 'com.wiris.quizzes.test.LockTester'; }
}
