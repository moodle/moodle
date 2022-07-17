<?php

// OK.
mb_strrpos('abc abc abc', 'abc');
mb_strrpos('abc abc abc', 'abc', 0);
mb_strrpos(
    'abc abc abc',
    'abc',
    /* comment*/
    10 // phpcs:ignore Something
);
mb_strrpos('abc abc abc', 'abc', $offset, $encoding);
mb_strrpos('abc abc abc', 'abc', -5);
mb_strrpos('abc abc abc', 'abc', +1.2); // Float will be cast to int, so this is fine.
mb_strrpos('abc abc abc', 'abc', 'UTF-' + 8);

// Ignoring. Can not be reliably determined:
mb_strrpos('abc abc abc', 'abc', $offset);
mb_strrpos('abc abc abc', 'abc', $encoding);

// PHP 5.2/7.4: deprecated encoding as third param.
mb_strrpos('abc abc abc', 'abc', 'UTF-8');
mb_strrpos('abc abc abc', 'abc', "utf-$utfType");
mb_strrpos('abc abc abc', 'abc', 'utf-' . $utfType);
