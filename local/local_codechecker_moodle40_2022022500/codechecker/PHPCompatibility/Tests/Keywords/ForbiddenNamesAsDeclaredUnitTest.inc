<?php

/**
 * These keywords are ok to use as a function name.
 */
function null() {}
function true() {}
function false() {}
function bool() {}
function int() {}
function float() {}
function string() {}
function resource() {}
function object() {}
function mixed() {}
function numeric() {}

/**
 * These are all keywords that were added to the reserved list in 7.0 or later
 * and can not be used as class, interface, trait or namespace names.
 */
class null {}
class TRUE {} // Check case-insensitivity.
class false {}
class bool {}
class Int {} // Check case-insensitivity.
class float {}
class string {}
class resource {}
class obJeCt {}  // Check case-insensitivity.
class mixed {}
class numeric {}
class iterable {}
class void {}

interface null {}
interface true {}
interface false {}
interface bool {}
interface int {}
interface float {}
interface string {}
interface resource {}
interface object {}
interface mixed {}
interface numeric {}
interface iterable {}
interface void {}

namespace null;
namespace true;
namespace false;
namespace bool;
namespace int;
namespace float;
namespace string;
namespace resource;
namespace object;
namespace mixed;
namespace numeric;
namespace iterable;
namespace void;

namespace null {}
namespace true {}
namespace false {}
namespace bool {}
namespace int {}
namespace float {}
namespace string {}
namespace resource {}
namespace object {}
namespace mixed {}
namespace numeric {}
namespace iterable {}
namespace void {}

// Multi-level namespaces.
namespace MyProject\null\Level;
namespace MyProject\Sub\true;
namespace MyProject\false\Level;
namespace MyProject\Sub\bool;
namespace MyProject\int\Level;
namespace MyProject\Sub\float;
namespace MyProject\string\Level;
namespace MyProject\Sub\resource;
namespace MyProject\object\Level;
namespace MyProject\Sub\mixed;
namespace MyProject\numeric\Level;
namespace MyProject\Sub\iterable;
namespace MyProject\void\Level;

// These have to be at the end of the file for PHP 5.2 not to fail on them...
trait null {}
trait true {}
trait false {}
trait bool {}
trait int {}
trait float {}
trait string {}
trait resource {}
trait object {}
trait mixed {}
trait numeric {}
trait iterable {}
trait void {}
