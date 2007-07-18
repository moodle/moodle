#!/bin/sh

# Psacal compile wrapper-script for 'test_solution.sh'.
# See that script for syntax and more info.

SOURCE="$1"
DEST="$2"

# -viwn:	Verbose warnings, notes and informational messages
# -02:		Level 2 optimizations (default for speed)
# -Sg:		Support label and goto commands (for those who need it ;-)
# -XS:		Static link with all libraries
fpc -viwn -O2 -Sg -XS -o$DEST $SOURCE
exitcode=$?

# clean created object files:
rm -f $DEST.o

exit $exitcode
