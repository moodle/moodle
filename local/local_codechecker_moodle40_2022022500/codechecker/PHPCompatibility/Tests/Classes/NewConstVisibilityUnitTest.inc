<?php

const NONCLASSCONST = 'foo';

class ConstDemo
{
    const PUBLIC_CONST_A = 1;

    // PHP 7.1+
    public const PUBLIC_CONST_B = 2;
    protected const PROTECTED_CONST = 3;
    private const PRIVATE_CONST = 4;
}

interface InterfaceDemo
{
    const PUBLIC_CONST_A = 1;

    // PHP 7.1+
    public const PUBLIC_CONST_B = 2;

    // Invalid, but the check for which visibility indicator is used is outside the scope of this library.
    protected const PROTECTED_CONST = 3;
    private const PRIVATE_CONST = 4;
}

// Test anonymous classes.
$a = new class
{
    const PUBLIC_CONST_A = 1;

    // PHP 7.1+
    public const PUBLIC_CONST_B = 2;
    protected const PROTECTED_CONST = 3;
    private const PRIVATE_CONST = 4;
}

/*
 * Test against some false positives.
 *
 * Constants defined in the global namespace can not have visibility indicators,
 * but this is outside the scope of this library. Would cause a parse error anyway.
 */
public const GLOBAL_CONSTANT = 'not valid';

class NotAClassConstant {
    public function something() {
        public const GLOBAL_CONSTANT = 'not valid';
    }
}
