#!/usr/bin/env perl
use strict;
use warnings;

my @valid_formats = (
    'simple',
    'oid2name',
    'oid2urn',
    'name2oid',
    'name2urn',
    'urn2oid',
    'urn2name',
    );

my $format = shift;
unless (defined($format)) {
    print(STDERR "Usage: simpleparser.pl FORMAT <FILES>\n");
    print(STDERR "Valid formats: ", join(' ', @valid_formats), "\n");
    exit(1);
}

unless (grep { $_ eq $format } @valid_formats) {
    print(STDERR "Invalid format: $format\n");
    print(STDERR "Valid formats: ", join(' ', @valid_formats), "\n");
    exit(1);
}
    

# Load file
my $text = join('', <>);

# Strip comments
$text =~ s/#.*$//gm;

my %oids;
my %names;

while ($text =~ m"attributetype\s*\(\s*([\d.]+).*?NAME\s+(?:'(.*?)'|(\(.*?\)))"sg) {
    my $oid = $1;
    my @attributes;
    if (defined($2)) {
	# Single attribute
	@attributes = ($2);
    } else {
	# Multiple attributes
	my $input = $3;
	while ($input =~ m"'(.*?)'"gs) {
	    push(@attributes, $1);
	}
    }

    foreach my $attrname (@attributes) {
	$names{$attrname} = $oid;
    }
    $oids{$oid} = [ @attributes ];
}


if ($format eq 'simple') {
    foreach my $oid (sort keys %oids) {
	my @names = @{$oids{$oid}};
	print "$oid ", join(' ', @names), "\n";
    }
    exit(0);
}

print "<?php\n";
print "\$attributemap = array(\n";

if ($format eq 'oid2name') {
    foreach my $oid (sort keys %oids) {
	my $name = $oids{$oid}->[0];
	print "\t'urn:oid:$oid' => '$name',\n";
    }
} elsif ($format eq 'oid2urn') {
    foreach my $oid (sort keys %oids) {
	my $name = $oids{$oid}->[0];
	print "\t'urn:oid:$oid' => 'urn:mace:dir:attribute-def:$name',\n";
    }
} elsif ($format eq 'name2oid') {
    foreach my $name (sort keys %names) {
	my $oid = $names{$name};
	print "\t'$name' => 'urn:oid:$oid',\n";
    }
} elsif ($format eq 'name2urn') {
    foreach my $name (sort keys %names) {
	print "\t'$name' => 'urn:mace:dir:attribute-def:$name',\n";
    }
} elsif ($format eq 'urn2oid') {
    foreach my $name (sort keys %names) {
	my $oid = $names{$name};
	print "\t'urn:mace:dir:attribute-def:$name' => 'urn:oid:$oid',\n";
    }
} elsif ($format eq 'urn2name') {
    foreach my $name (sort keys %names) {
	print "\t'urn:mace:dir:attribute-def:$name' => '$name',\n";
    }
}

print ");\n";
print "?>";

