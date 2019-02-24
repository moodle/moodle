Description of EvalMath library import into Moodle

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

To see all changes diff against version 1.1, available from:
http://www.phpclasses.org/browse/package/2695.html

skodak, Tim Hunt

Changes by Juan Pablo de Castro (MDL-14274):
* operators >,<,>=,<=,== added.
* function if[thenelse](condition, true_value, false_value)

Changes by Stefan Erlachner, Thomas Niedermaier (MDL-64414):
* add function or:
e.g. if (or(condition_1, condition_2, ... condition_n))
* add function and:
e.g. if (and(condition_1, condition_2, ... condition_n))