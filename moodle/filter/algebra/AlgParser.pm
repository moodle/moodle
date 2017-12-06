

## Last modification: 8/3/00 by akp
## Originally written by Daniel Martin, Dept of Math, John Hopkins
## Additions and modifications were made by James Martino, Dept of Math, John Hopkins
## Additions and modifications were made by Arnold Pizer, Dept of Math, Univ of Rochester

#use Data::Dumper;

package AlgParser;
use HTML::Entities;

%close = ();

sub new {
  my $package = shift;
  my (%ret);
  $ret{string} = "";
  $ret{posarray} = [];
  $ret{parseerror} = "";
  $ret{parseresult} = [];
  bless \%ret, $package;
  return \%ret;
}

sub inittokenizer {
  my($self, $string) = @_;
  $self->{string} =~ m/\G.*$/g;
  $self->{string} = undef;
  $self->{string} = $string;
  $self->{string} =~ m/\G.*$/g;
  $self->{string} =~ m/^/g;
}

$close{'{'} = '}';
$close{'['} = ']';
$close{'('} = ')';

$binoper3 = '(?:\\^|\\*\\*)';
$binoper2 = '[/*_,]';
$binoper1 = '[-+=><%!#]';
$openparen = '[{(\\[]';
$closeparen = '[})\\]]';
$varname = '[A-Za-z](?:_[0-9]+)?';
$specialvalue = '(?:e|pi|da|db|dc|de|df|dg|dh|di|dj|dk|dl|dm|dn|do|dp|dq|dr|ds|dt|du|dv|dw|dx|dy|dz|infty|alpha|bita|gamma|zita|thita|iota|kappa|lambda|mu|nu|xi|rho|sigma|tau|phi|chi|psi|omega|zepslon|zdelta|xeta|zupslon|zeroplace)';
$numberplain = '(?:\d+(?:\.\d*)?|\.\d+)';
$numberE = '(?:' . $numberplain . 'E[-+]?\d+)';
$number = '(?:' . $numberE . '|' . $numberplain . ')';
#
#  DPVC -- 2003/03/31
#       added missing trig and inverse functions
#
#$trigfname = '(?:cosh|sinh|tanh|cot|(?:a(?:rc)?)?cos|(?:a(?:rc)?)?sin|' .
#    '(?:a(?:rc)?)?tan|sech?)';
$trigfname = '(?:(?:a(?:rc)?)?(?:sin|cos|tan|sec|csc|cot)h?)';
#
#  End DPVC
#
$otherfunc = '(?:exp|abs|logten|log|ln|sqrt|sgn|step|fact|int|lim|fun[a-zA-Z])';
$funcname = '(?:' . $otherfunc . '|' . $trigfname . ')';

$tokenregexp = "(?:($binoper3)|($binoper2)|($binoper1)|($openparen)|" .
    "($closeparen)|($funcname)|($specialvalue)|($varname)|" .
    "($numberE)|($number))";

sub nexttoken {
  my($self) = shift;
  $self->{string} =~ m/\G\s+/gc;
  my($p1) = pos($self->{string}) || 0;
  if(scalar($self->{string} =~ m/\G$tokenregexp/gc)) {
        push @{$self->{posarray}}, [$p1, pos($self->{string})];
        if (defined($1)) {return ['binop3',  $1];}
        if (defined($2)) {return ['binop2',  $2];}
        if (defined($3)) {return ['binop1',  $3];}
        if (defined($4)) {return ['openp',   $4];}
        if (defined($5)) {return ['closep',  $5];}
        if (defined($6)) {return ['func1',   $6];}
        if (defined($7)) {return ['special', $7];}
        if (defined($8)) {return ['varname', $8];}
        if (defined($9)) {return ['numberE', $9];}
        if (defined($10)) {return ['number', $10];}
  }
  else {
    push @{$self->{posarray}}, [$p1, undef];
    return undef;
  }
}

sub parse {
  my $self = shift;
  $self->{parseerror} = "";
  $self->{posarray} = [];
  $self->{parseresult} = ['top', undef];
  my (@backtrace) = (\$self->{parseresult});
  my (@pushback) = ();

  my $currentref = \$self->{parseresult}->[1];
  my $curenttok;

  my $sstring = shift;
  $self->inittokenizer($sstring);
  $currenttok = $self->nexttoken;
  if (!$currenttok) {
    if ($self->{string} =~ m/\G$/g) {
      return $self->error("empty");
    } else {
      my($mark) = pop @{$self->{posarray}};
      my $position = 1+$mark->[0];
      return $self->error("Illegal character at position $position", $mark);
    }
  }
  # so I can assume we got a token
  local $_;
  while ($currenttok) {
    $_ = $currenttok->[0];
    /binop1/ && do {
      # check if we have a binary or unary operation here.
      if (defined(${$currentref})) {
        # binary - walk up the tree until we hit an open paren or the top
        while (${$currentref}->[0] !~ /^(openp|top)/) {
          $currentref = pop @backtrace;
        }
        my $index = ((${$currentref}->[0] eq 'top')?1:3);
        ${$currentref}->[$index] = ['binop1', $currenttok->[1],
                                    ${$currentref}->[$index], undef];
        push @backtrace, $currentref;
        push @backtrace, \${$currentref}->[$index];
        $currentref = \${$currentref}->[$index]->[3];
      } else {
        # unary
        ${$currentref} = ['unop1', $currenttok->[1], undef];
        push @backtrace, $currentref;
        $currentref = \${$currentref}->[2];
      }
    };
    /binop2/ && do {
      if (defined(${$currentref})) {
        # walk up the tree until an open paren, the top, binop1 or unop1
        # I decide arbitrarily that -3*4 should be parsed as -(3*4)
        # instead of as (-3)*4.  Not that it makes a difference.

        while (${$currentref}->[0] !~ /^(openp|top|binop1)/) {
          $currentref = pop @backtrace;
        }
        my $a = ${$currentref}->[0];
        my $index = (($a eq 'top')?1:3);
        ${$currentref}->[$index] = ['binop2', $currenttok->[1],
                                    ${$currentref}->[$index], undef];
        push @backtrace, $currentref;
        push @backtrace, \${$currentref}->[$index];
        $currentref = \${$currentref}->[$index]->[3];
      } else {
        # Error
        my($mark) = pop @{$self->{posarray}};
        my $position =1+$mark->[0];
        return $self->error("Didn't expect " . $currenttok->[1] .
                            " at position $position" , $mark);
      }
    };
    /binop3/ && do {
      if (defined(${$currentref})) {
        # walk up the tree until we need to stop
        # Note that the right-associated nature of ^ means we need to
        # stop walking backwards when we hit a ^ as well.
        while (${$currentref}->[0] !~ /^(openp|top|binop[123]|unop1)/) {
          $currentref = pop @backtrace;
        }
        my $a = ${$currentref}->[0];
        my $index = ($a eq 'top')?1:($a eq 'unop1')?2:3;
        ${$currentref}->[$index] = ['binop3', $currenttok->[1],
                                    ${$currentref}->[$index], undef];
        push @backtrace, $currentref;
        push @backtrace, \${$currentref}->[$index];
        $currentref = \${$currentref}->[$index]->[3];
      } else {
        # Error
        my($mark) = pop @{$self->{posarray}};
        my $position = 1+$mark->[0];
        return $self->error("Didn't expect " . $currenttok->[1] .
                            " at position $position", $mark);
      }
    };
    /openp/ && do {
      if (defined(${$currentref})) {
        # we weren't expecting this - must be implicit
        # multiplication.
        push @pushback, $currenttok;
        $currenttok = ['binop2', 'implicit'];
        next;
      } else {
        my($me) = pop @{$self->{posarray}};
        ${$currentref} = [$currenttok->[0], $currenttok->[1], $me, undef];
        push @backtrace, $currentref;
        $currentref = \${$currentref}->[3];
      }
    };
    /func1/ && do {
      if (defined(${$currentref})) {
        # we weren't expecting this - must be implicit
        # multiplication.
        push @pushback, $currenttok;
        $currenttok = ['binop2', 'implicit'];
        next;
      } else {
        # just like a unary operator
        ${$currentref} = [$currenttok->[0], $currenttok->[1], undef];
        push @backtrace, $currentref;
        $currentref = \${$currentref}->[2];
      }
    };
    /closep/ && do {
      if (defined(${$currentref})) {
        # walk up the tree until we need to stop
        while (${$currentref}->[0] !~ /^(openp|top)/) {
          $currentref = pop @backtrace;
        }
        my $a = ${$currentref}->[0];
        if ($a eq 'top') {
          my($mark) = pop @{$self->{posarray}};
          my $position = 1+$mark->[0];
          return $self->error("Unmatched close " . $currenttok->[1] .
                              " at position $position", $mark);
        } elsif ($close{${$currentref}->[1]} ne $currenttok->[1]) {
          my($mark) = pop @{$self->{posarray}};
          my $position = 1+$mark->[0];
          return $self->error("Mismatched parens at position $position"
                              , ${$currentref}->[2], $mark);
        } else {
          ${$currentref}->[0] = 'closep';
          ${$currentref}->[2] = pop @{${$currentref}};
        }
      } else {
        # Error - something like (3+4*)
        my($mark) = pop @{$self->{posarray}};
        my $position = 1+$mark->[0];
        return $self->error("Premature close " . $currenttok->[1] .
                            " at position $position", $mark);
      }
    };
    /special|varname|numberE?/ && do {
      if (defined(${$currentref})) {
        # we weren't expecting this - must be implicit
        # multiplication.
        push @pushback, $currenttok;
        $currenttok = ['binop2', 'implicit'];
        next;
      } else {
        ${$currentref} = [$currenttok->[0], $currenttok->[1]];
      }
    };
    if (@pushback) {
      $currenttok = pop @pushback;
    } else {
      $currenttok = $self->nexttoken;
    }
  }
  # ok, we stopped parsing.  Now we need to see why.
  if ($self->{parseresult}->[0] eq 'top') {
    $self->{parseresult} = $self->arraytoexpr($self->{parseresult}->[1]);
  } else {
    return $self->error("Internal consistency error; not at top when done");
  }
  if ($self->{string} =~ m/\G\s*$/g) {
    if (!defined(${$currentref})) {
      $self->{string} .= " ";
      return $self->error("I was expecting more at the end of the line",
                        [length($self->{string})-1, length($self->{string})]);
    } else {
      # check that all the parens were closed
      while (@backtrace) {
        $currentref = pop @backtrace;
        if (${$currentref}->[0] eq 'openp') {
          my($mark) = ${$currentref}->[2];
          my $position = 1+$mark->[0];
          return $self->error("Unclosed parentheses beginning at position $position"
                         , $mark);
        }
      }
      # Ok, we must really have parsed something
      return $self->{parseresult};
    }
  } else {
      my($mark) = pop @{$self->{posarray}};
      my $position = 1+$mark->[0];
      return $self->error("Illegal character at position $position",$mark);
  }
}

sub arraytoexpr {
  my ($self) = shift;
  return Expr->fromarray(@_);
}

sub error {
  my($self, $errstr, @markers) = @_;
#  print STDERR Data::Dumper->Dump([\@markers],
#                                  ['$markers']);
  $self->{parseerror} = $errstr;
  my($htmledstring) = '<tt class="parseinput">';
  my($str) = $self->{string};
#  print STDERR Data::Dumper->Dump([$str], ['$str']);
  my($lastpos) = 0;
  $str =~ s/ /\240/g;
  while(@markers) {
    my($ref) = shift @markers;
    my($pos1) = $ref->[0];
    my($pos2) = $ref->[1];
    if (!defined($pos2)) {$pos2 = $pos1+1;}
    $htmledstring .= encode_entities(substr($str,$lastpos,$pos1-$lastpos)) .
           '<b class="parsehilight">' .
           encode_entities(substr($str,$pos1,$pos2-$pos1)) .
           '</b>';
    $lastpos = $pos2;
  }
#  print STDERR Data::Dumper->Dump([$str, $htmledstring, $lastpos],
#                                  ['$str', '$htmledstring', '$lastpos']);
  $htmledstring .= encode_entities(substr($str,$lastpos));
  $htmledstring .= '</tt>';
#  $self->{htmlerror} = '<p class="parseerr">' . "\n" .
#                       '<span class="parsedesc">' .
#                       encode_entities($errstr) . '</span><br>' . "\n" .
#                       $htmledstring . "\n" . '</p>' . "\n";
  $self->{htmlerror} =  $htmledstring ;
  $self->{htmlerror} =  'empty' if $errstr eq 'empty';
  $self->{error_msg} = $errstr;

#  warn $errstr . "\n";
  return undef;
}

sub tostring {
  my ($self) = shift;
  return $self->{parseresult}->tostring(@_);
}

sub tolatex {
  my ($self) = shift;
  return $self->{parseresult}->tolatex(@_);
}

sub tolatexstring { return tolatex(@_);}

sub exprtolatexstr {
  return exprtolatex(@_);
}

sub exprtolatex {
  my($expr) = shift;
  my($exprobj);
  if ((ref $expr) eq 'ARRAY') {
    $exprobj = Expr->new(@$expr);
  } else {
    $exprobj = $expr;
  }
  return $exprobj->tolatex();
}

sub exprtostr {
  my($expr) = shift;
  my($exprobj);
  if ((ref $expr) eq 'ARRAY') {
    $exprobj = Expr->new(@$expr);
  } else {
    $exprobj = $expr;
  }
  return $exprobj->tostring();
}

sub normalize {
  my ($self, $degree) = @_;
  $self->{parseresult} = $self->{parseresult}->normalize($degree);
}

sub normalize_expr {
  my($expr, $degree) = @_;
  my($exprobj);
  if ((ref $expr) eq 'ARRAY') {
    $exprobj = Expr->new(@$expr);
  } else {
    $exprobj = $expr;
  }
  return $exprobj->normalize($degree);
}

package AlgParserWithImplicitExpand;
@ISA=qw(AlgParser);

sub arraytoexpr {
  my ($self) = shift;
  my ($foo) = ExprWithImplicitExpand->fromarray(@_);
# print STDERR Data::Dumper->Dump([$foo],['retval']);
  return $foo;
}

package Expr;

sub new {
  my($class) = shift;
  my(@args) = @_;
  my($ret) = [@args];
  return (bless $ret, $class);
}

sub head {
  my($self) = shift;
  return ($self->[0]);
}


sub normalize {
#print STDERR "normalize\n";
#print STDERR Data::Dumper->Dump([@_]);

  my($self, $degree) = @_;
  my($class) = ref $self;
  $degree = $degree || 0;
  my($type, @args) = @$self;
  local $_;
  $_ = $type;
  my ($ret) = [$type, @args];


  if(/closep/) {
    $ret = $args[1]->normalize($degree);
  } elsif (/unop1/) {
    $ret = $class->new($type, $args[0], $args[1]->normalize($degree));
  } elsif (/binop/) {
    $ret = $class->new($type, $args[0], $args[1]->normalize($degree),
                             $args[2]->normalize($degree));
  } elsif (/func1/) {
    $args[0] =~ s/^arc/a/;
    $ret = $class->new($type, $args[0], $args[1]->normalize($degree));
  }


  if ($degree < 0) {return $ret;}


  ($type, @args) = @$ret;
  $ret = $class->new($type, @args);
  $_ = $type;
  if (/binop1/ && ($args[2]->[0] =~ 'unop1')) {
    my($h1, $h2) = ($args[0], $args[2]->[1]);
    my($s1, $s2) = ($h1 eq '-', $h2 eq '-');
    my($eventual) = ($s1==$s2);
    if ($eventual) {
      $ret = $class->new('binop1', '+', $args[1], $args[2]->[2] );
    } else {
      $ret = $class->new('binop1', '-', $args[1], $args[2]->[2] );
    }
  } elsif (/binop2/ && ($args[1]->[0] =~ 'unop1')) {
    $ret = $class->new('unop1', '-',
                       $class->new($type, $args[0], $args[1]->[2],
                                   $args[2])->normalize($degree) );
  } elsif (/binop[12]/ && ($args[2]->[0] eq $type) &&
                          ($args[0] =~ /[+*]/)) {
# Remove frivolous right-association
# For example, fix 3+(4-5) or 3*(4x)
    $ret = $class->new($type, $args[2]->[1],
                       $class->new($type, $args[0], $args[1],
                                   $args[2]->[2])->normalize($degree),
                       $args[2]->[3]);
  } elsif (/unop1/ && ($args[0] eq '+')) {
    $ret = $args[1];
  } elsif (/unop1/ && ($args[1]->[0] =~ 'unop1')) {
    $ret = $args[1]->[2];
  }
  if ($degree > 0) {
  }
  return $ret;
}

sub tostring {
# print STDERR "Expr::tostring\n";
# print STDERR Data::Dumper->Dump([@_]);
  my($self) = shift;
  my($type, @args) = @$self;
  local $_;
  $_ = $type;
  /binop1/ && do {
    my ($p1, $p2) = ('','');
    if ($args[2]->[0] eq 'binop1') {($p1,$p2)=qw{ ( ) };}
    return ($args[1]->tostring() . $args[0] . $p1 .
            $args[2]->tostring() . $p2);
  };
  /unop1/ && do {
    my ($p1, $p2) = ('','');
    if ($args[1]->[0] =~ /binop1/) {($p1,$p2)=qw{ ( ) };}
    return ($args[0] . $p1 . $args[1]->tostring() . $p2);
  };
  /binop2/ && do {
    my ($p1, $p2, $p3, $p4)=('','','','');
    if ($args[0] =~ /implicit/) {$args[0] = ' ';}
    if ($args[1]->[0] =~ /binop1/) {($p1,$p2)=qw{ ( ) };}
#    if ($args[2]->[0] =~ /binop[12]/) {($p3,$p4)=qw{ ( ) };}
    if ($args[2]->[0] =~ /binop[12]|unop1/) {($p3,$p4)=qw{ ( ) };}
    return ($p1 . $args[1]->tostring() . $p2 . $args[0] . $p3 .
            $args[2]->tostring() . $p4);
  };
  /binop3/ && do {
    my ($p1, $p2, $p3, $p4)=('','','','');
#    if ($args[1]->[0] =~ /binop[123]|numberE/) {($p1,$p2)=qw{ ( ) };}
    if ($args[1]->[0] =~ /binop[123]|unop1|numberE/) {($p1,$p2)=qw{ ( ) };}
#    if ($args[2]->[0] =~ /binop[12]|numberE/) {($p3,$p4)=qw{ ( ) };}
    if ($args[2]->[0] =~ /binop[12]|unop1|numberE/) {($p3,$p4)=qw{ ( ) };}
    return ($p1 . $args[1]->tostring() . $p2 . $args[0] . $p3 .
            $args[2]->tostring() . $p4);
  };
  /func1/ && do {
    return ($args[0] . '(' . $args[1]->tostring() . ')');
  };
  /special|varname|numberE?/ && return $args[0];
  /closep/ && do {
    my(%close) = %AlgParser::close;



    return ($args[0] . $args[1]->tostring() . $close{$args[0]});
  };
}

sub tolatex {
  my($self) = shift;
  my($type, @args) = @$self;
  local $_;
  $_ = $type;
  /binop1/ && do {
    my ($p1, $p2) = ('','');
    if ($args[2]->[0] eq 'binop1') {($p1,$p2)=qw{ \left( \right) };}
    return ($args[1]->tolatex() . $args[0] . $p1 .
            $args[2]->tolatex() . $p2);
  };
  /unop1/ && do {
    my ($p1, $p2) = ('','');
    if ($args[1]->[0] =~ /binop1/) {($p1,$p2)=qw{ \left( \right) };}
    return ($args[0] . $p1 . $args[1]->tolatex() . $p2);
  };
  /binop2/ && do {
    my ($p1, $p2, $p3, $p4) = ('','','','');
    if ($args[0] =~ /implicit/) {
      if ( (($args[1]->head eq qq(number)) &&
            ($args[2]->head eq qq(number))) ||
           (($args[1]->head eq qq(binop2)) &&
            ($args[1]->[2]->head eq qq(number))) ) {
        $args[0] = '\\,';
      } else {
        $args[0] = ' ';
      }
    }
    if ($args[1]->[0] =~ /binop1|numberE/)
      {($p1,$p2)=qw{ \left( \right) };}
 #   if ($args[2]->[0] =~ /binop[12]|numberE/)
        if ($args[2]->[0] =~ /binop[12]|numberE|unop1/)
      {($p3,$p4)=qw{ \left( \right) };}
    if ($args[0] eq '/'){
#   return('\frac{' . $p1 . $args[1]->tolatex() . $p2 . '}'.
#               '{' . $p3 . $args[2]->tolatex() . $p4 . '}' );
        return('\frac{' . $args[1]->tolatex() . '}'.
               '{' . $args[2]->tolatex() . '}' );
    }
    else{
    return ($p1 . $args[1]->tolatex() . $p2 . $args[0] . $p3 .
            $args[2]->tolatex() . $p4);
    }
  };
  /binop3/ && do {
    my ($p1, $p2, $p3, $p4)=('','','','');
#    if ($args[1]->[0] =~ /binop[123]|numberE/) {($p1,$p2)=qw{ \left( \right) };}
  if ($args[1]->[0] =~ /binop[123]|unop1|numberE/) {($p1,$p2)=qw{ \left( \right) };}
# Not necessary in latex
#   if ($args[2]->[0] =~ /binop[12]/) {($p3,$p4)=qw{ \left( \right) };}
    return ($p1 . $args[1]->tolatex() . $p2 . "^{" . $p3 .
            $args[2]->tolatex() . $p4 . "}");
  };
  /func1/ && do {
      my($p1,$p2);
      if($args[0] eq "sqrt"){($p1,$p2)=qw{ \left{ \right} };}
      else {($p1,$p2)=qw{ \left( \right) };}

      #
      #  DPVC -- 2003/03/31
      #       added missing trig functions
      #
      #$specialfunc = '(?:abs|logten|asin|acos|atan|sech|sgn|step|fact)';
      $specialfunc = '(?:abs|logten|a(?:sin|cos|tan|sec|csc|cot)h?|sgn|step|fact)';
      #
      #  End DPVC
      #

      if ($args[0] =~ /$specialfunc/) {
         return ('\mbox{' . $args[0] .'}'. $p1 . $args[1]->tolatex() . $p2);
      }
      else {
        return ('\\' . $args[0] . $p1 . $args[1]->tolatex() . $p2);
      }
  };
  /special/ && do {
    if ($args[0] eq 'pi') {return '\pi';} else {return $args[0];}
  };
  /varname|(:?number$)/ && return $args[0];
  /numberE/ && do {
    $args[0] =~ m/($AlgParser::numberplain)E([-+]?\d+)/;
    return ($1 . '\times 10^{' . $2 . '}');
  };
  /closep/ && do {
    my($backslash) = '';
    my(%close) = %AlgParser::close;
    if ($args[0] eq '{') {$backslash = '\\';}
#This is for editors to match: }
    return ('\left' . $backslash . $args[0] . $args[1]->tolatex() .
            '\right' . $backslash . $close{$args[0]});
  };
}

sub fromarray {
  my($class) = shift;
  my($expr) = shift;
  if ((ref $expr) ne qq{ARRAY}) {
    die "Program error; fromarray not passed an array ref.";
  }
  my($type, @args) = @$expr;
  foreach my $i (@args) {
    if (ref $i) {
      $i = $class->fromarray($i);
    }
  }
  return $class->new($type, @args);
}

package ExprWithImplicitExpand;
@ISA=qw(Expr);


sub tostring {
# print STDERR "ExprWIE::tostring\n";
# print STDERR Data::Dumper->Dump([@_]);
  my ($self) = shift;

  my($type, @args) = @$self;

  if (($type eq qq(binop2)) && ($args[0] eq qq(implicit))) {
    my ($p1, $p2, $p3, $p4)=('','','','');
    if ($args[1]->head =~ /binop1/) {($p1,$p2)=qw{ ( ) };}
#    if ($args[2]->head =~ /binop[12]/) {($p3,$p4)=qw{ ( ) };}
    if ($args[2]->head =~ /binop[12]|unop1/) {($p3,$p4)=qw{ ( ) };}
    return ($p1 . $args[1]->tostring() . $p2 . '*' . $p3 .
            $args[2]->tostring() . $p4);
  } else {
    return $self->SUPER::tostring(@_);
  }
}
