#! /usr/bin/perl -w

# Spell Checker Plugin for HTMLArea-3.0
# Implementation by Mihai Bazon.  Sponsored by www.americanbible.org
#
# htmlArea v3.0 - Copyright (c) 2002 interactivetools.com, inc.
# This notice MUST stay intact for use (see license.txt).
#
# A free WYSIWYG editor replacement for <textarea> fields.
# For full source code and docs, visit http://www.interactivetools.com/
#
# Version 3.0 developed by Mihai Bazon for InteractiveTools.
#	     http://students.infoiasi.ro/~mishoo
#
# $Id$

use strict;
use utf8;
use Encode;
use Text::Aspell;
use HTML::Parser;
use HTML::Entities;
use CGI;

my $debug = 0;

open (DEBUG, '>:encoding(UTF-8)', '> /tmp/spell-check-debug.log') if $debug;

# use Data::Dumper; # for debug only

my $speller = new Text::Aspell;
my $cgi = new CGI;

# FIXME: report a nice error...
die "Can't create speller!" unless $speller;

# add configurable option for this
my $dict = $cgi->param('dictionary') || 'en_US';
$speller->set_option('lang', $dict);

# ultra, fast, normal, bad-spellers
# bad-spellers seems to cause segmentation fault
$speller->set_option('sug-mode', 'ultra');

my @replacements = ();

sub text_handler {
    my ($offset, $length, $text, $is_cdata) = @_;
    if ($is_cdata or $text =~ /^\s*$/) {
        return 0;
    }
    # print STDERR "*** OFFSET: $offset, LENGTH: $length, $text\n";
    $text = decode_entities($text);
    $text =~ s/&#([0-9]+);/chr($1)/eg;
    $text =~ s/&#x([0-9a-fA-F]+);/chr(hex $1)/eg;
    my $repl = spellcheck($text);
    if ($repl) {
        push(@replacements, [ $offset, $length, $repl ]);
    }
}

my $p = HTML::Parser->new
  (api_version => 3,
   handlers => { start => [ sub {
                                my ($self, $tagname, $attrs) = @_;
                                # print STDERR "\033[1;31m parsing tag: $tagname\033[0m\n";
                                # following we skip words that have already been marked as "fixed".
                                if ($tagname eq "span" and $attrs->{class} =~ /HA-spellcheck-fixed/) {
                                    $self->handler(text => undef);
                                }
                            }, "self, tagname, attr"
                          ],
                 end => [ sub {
                              my ($self, $tagname) = @_;
                              # print STDERR "\033[1;32m END tag: $tagname\033[0m\n";
                              $self->handler(text => \&text_handler, 'offset, length, dtext, is_cdata');
                          }, "self, tagname"
                        ]
               }
  );
$p->handler(text => \&text_handler, 'offset, length, dtext, is_cdata');
$p->case_sensitive(1);
my $file_content = $cgi->param('content');

if ($debug) {
    open (FOO, '>:encoding(UTF-8)', '/tmp/spell-check-before');
    print FOO $file_content, "\n";
    close(FOO);
}

$p->parse($file_content);
$p->eof();

foreach (reverse @replacements) {
    substr($file_content, $_->[0], $_->[1], $_->[2]);
}

# we output UTF-8
binmode(STDOUT, ':encoding(UTF-8)'); # apparently, this sucks.
print "Content-type: text/html; charset: utf-8\n\n";
print qq^
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" media="all" href="spell-check-style.css" />
</head>
<body onload="window.parent.finishedSpellChecking();">^;

print $file_content;
if ($cgi->param('init') eq '1') {
    my @dicts = $speller->dictionary_info();
    my $dictionaries = '';
    foreach my $i (@dicts) {
        $dictionaries .= ',' . $i->{name} unless $i->{jargon};
    }
    $dictionaries =~ s/^,//;
    print qq^
<div id="HA-spellcheck-dictionaries"
>$dictionaries</div>
^;
}

if ($debug) {
    open (FOO, '>:encoding(UTF-8)', '/tmp/spell-check-after');
    print FOO $file_content, "\n";
    close(FOO);
}

print '</body></html>';

# Perl is beautiful.
sub spellcheck {
    my $text = shift;
    sub check {                 # called for each word in the text
        # input is in UTF-8
        my $U_word = shift;
        my $word = encode($speller->get_option('encoding'), $U_word);
        print DEBUG "*$U_word* ----> |$word|\n" if $debug;
        if ($speller->check($word)) {
            return $U_word;      # we return the word in UTF-8
        } else {
            # we should have suggestions; give them back to browser in UTF-8
            my $suggestions = decode($speller->get_option('encoding'), join(',', $speller->suggest($word)));
            my $ret = '<span class="HA-spellcheck-error">'.$U_word.'</span><span class="HA-spellcheck-suggestions">'.$suggestions.'</span>';
            return $ret;
        }
    }
    $text =~ s/([[:word:]']+)/check($1)/egs;
    # $text =~ s/(\w+)/check($1)/egs;

    # the following is definitely what we want to use; too bad it sucks most.
    # $text =~ s/(\p{IsWord}+)/check($1)/egs;
    return $text;
}
