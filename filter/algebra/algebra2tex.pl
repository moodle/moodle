#!/usr/bin/perl

use lib '.';
use AlgParser;

my $parser = new AlgParserWithImplicitExpand;
my $ret;

$ret = $parser -> parse($ARGV[0]);
if ( ref($ret) ) {
    $parser -> tostring();
    $parser -> normalize();
    print $parser -> tolatex();
} else {
    print $parser->{htmlerror};
}

