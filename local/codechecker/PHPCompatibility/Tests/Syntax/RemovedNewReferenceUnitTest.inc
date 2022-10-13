<?php

class Foobar
{
    public $id = 0;
}

$foobar = new Foobar();
$foobar2 = &new Foobar();
$foobar3 = & new Foobar();
$foobar4 =& /* reference */ new Foobar();
$foobar5 = &                new Foobar();
