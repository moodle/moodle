<?php

/* Case 1 */
class MyTest {}
/* Case 2 */
class MyTestX extends DateTime {}


namespace MyTesting;

/* Case 3 */
class MyTestA extends DateTime {}
/* Case 4 */
class MyTestB extends \DateTime {}
/* Case 5 */
class MyTestD extends anotherNS\DateTime {}
/* Case 6 */
class MyTestE extends \FQNS\DateTime {}


namespace AnotherTesting {
    /* Case 7 */
    class MyTestF extends DateTime {}
    /* Case 8 */
    class MyTestG extends \DateTime {}
    /* Case 9 */
    class MyTestI extends anotherNS\DateTime {}
    /* Case 10 */
    class MyTestJ extends \FQNS\DateTime {}
}


/* Case 11 */
class MyTestK extends DateTime {}
/* Case 12 */
class MyTestL extends \DateTime {}


namespace Yet\More\Testing;

/* Case 13 */
class MyTestN extends DateTime {}
/* Case 14 */
class MyTestO extends anotherNS\DateTime {}
/* Case 15 */
class MyTestP extends \FQNS\DateTime {}

/* Case 16 */
interface MyInterface extends \SomeInterface {}
/* Case 17 */
interface MyInterface extends SomeInterface {}
