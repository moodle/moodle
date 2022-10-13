<?php

// These should all be flagged.
function testingA( $GLOBALS ) {}
function testingB( $_SERVER ) {}
function testingC( $_GET ) {}
function testingD( $_POST ) {}
function testingE( $_FILES ) {}
function testingF( $_COOKIE ) {}
function testingG( $_SESSION ) {}
function testingH( $_REQUEST ) {}
function testingI( $_ENV ) {}

// This should be ok.
function testingJ( $globals ) {}
function testingK( $_post ) {}
function testingL( $POST ) {}

// Closures: these should be flagged.
function ( $GLOBALS ) {}
function( $_SERVER ) {}
function($_GET) {}
