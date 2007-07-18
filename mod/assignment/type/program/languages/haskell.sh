#!/bin/sh

# Haskell compile wrapper-script for 'test_solution.sh'.
# See that script for syntax and more info.

SOURCE="$1"
DEST="$2"

# -Wall:	Report all warnings
# -O:		Optimize
# -static:	Static link Haskell libraries
# -optl-static:	Pass '-static' option to the linker
ghc -Wall -O -static -optl-static -o $DEST $SOURCE
exitcode=$?

# clean created files:
rm -f $DEST.o Main.hi

exit $exitcode
