<?php

// These are all keywords that were added in 5.0 or later attempted to be 
// called incorrectly as functions

abstract();
callable();
catch();
// Removed: clone(); see #284
final();
finally(); // introduced in 5.5
goto();
implements();
interface();
instanceof();
insteadof();
namespace();
private();
protected();
public();
// Removed: throw(); see #118
trait();
try();

// These are all valid uses of catch
try {}
catch
(Exception $e) {}
try {} catch(Exception $e) {}
try {
} catch (Exception $e) {}

// OK: These keywords *can* be used as function names.
bool();
int();
float();
string();
NULL();
null();
TRUE();
true();
FALSE();
false();
resource();
object();
mixed();
numeric();

/*
 * As of PHP 7.0, they can be used as method names.
 * Only testing the back-compat ones with T_STRING as they are the only ones which could give a false positive.
 */
$myObject->abstract();
MyClassName::callable();
$myObject->catch();
MyClassName::final();
$myObject->finally();
MyClassName::goto();
$myObject->implements();
MyClassName::interface();
$myObject->instanceof();
MyClassName::insteadof();
$myObject->namespace();
MyClassName::private();
$myObject->protected();
MyClassName::public();
$myObject->trait();
MyClassName::try();
