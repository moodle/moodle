<?php

/* Case 1 */
const BAR = false;

function something()
{
    /* Case 2 */
    const BAR = false;
}

class MyClass {
    /* Case 3 */
    const FOO = true;

    public function something()
    {
        /* Case 4 */
        const BAR = false;
    }
}

$a = new class {
    /* Case 5 */
    const FOO = true;

    public function something()
    {
        /* Case 6 */
        const BAR = false;
    }
}

interface MyInterface {
    /* Case 7 */
    const FOO = true;
}

trait MyTrait {
    // Constants are not allowed in traits.
    /* Case 8 */
    const BAR = false;

    public function something()
    {
        /* Case 9 */
        const BAR = false;
    }
}
