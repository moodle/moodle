Description of EvalMath library import into Moodle

The EvalMath class used in this library has been heavily modified.

Our changes:
* implicit multiplication (optionally) not allowed
* new custom calc emulation functions
* removed (optionally) e and pi constants - not used in calc
* removed sample files
* Fix a == FALSE that should have been === FALSE.
* added $expecting_op = true; for branch where a function with no operands is found to fix bug.
* moved pattern for func and var names into a static var
* made a function to test a string to see if it is a valid func or var name.
* localized strings
* added round, ceil and floor functions.
* EvalMath::EvalMath() changed to EvalMath::__construct() and there is a new EvalMath::EvalMath
  function to maintain backwards compatibility

When upgrading EvalMath library, compare versions using this link (update to suit):
https://github.com/dbojdo/eval-math/compare/1.0.1...1.0.2

Update evalmath.class.php with the identified changes.

Changes by Juan Pablo de Castro (MDL-14274):
* operators >,<,>=,<=,== added.
* function if[thenelse](condition, true_value, false_value)

Changes by Stefan Erlachner, Thomas Niedermaier (MDL-64414):
* add function or:
e.g. if (or(condition_1, condition_2, ... condition_n))
* add function and:
e.g. if (and(condition_1, condition_2, ... condition_n))

Changes by Raquel Ortega (MDL-76413)
* Avoid PHP 8.2: Partially-supported callable deprecations
* eg: call_user_func_array(array('self', 'sum'), $args to call_user_func_array(array(self::class, 'sum'), $args)

Changes by Meirza (MDL-75464)
* EvalMath has unit tests in lib/tests/mathslib_test.php,
  since version 1.0.1, there are two additional tests:
  - shouldSupportModuloOperator()
  - shouldConsiderDoubleMinusAsPlus()
  To pass the test, some modifications must be made:
  - Adjust the test code so it can run properly by using \calc_formula.
  Please see the differences between the code in MDL-75464 and the upstream code.
  - In the dataprovider for both tests, add the formula in the first array with "=" at the first character.
  Before:
  ```
  'a%b', // 9%3 => 0
  ```
  After:
  ```
  '=a%b', // 9%3 => 0

Changes by Yusuf Wibisono (MDL-86344)
* Ensure preg_match subject is a string (avoid null) in EvalMath::nfx