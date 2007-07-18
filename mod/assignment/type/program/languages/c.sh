#!/bin/sh

# C compile wrapper-script for 'test_solution.sh'.
# See that script for syntax and more info.

SOURCE="$1"
DEST="$2"

# -Wall:	Report all warnings
# -O2:		Level 2 optimizations (default for speed)
# -static:	Static link with all libraries
# -lm:		Link with math-library (has to be last argument!)
gcc -Wall -O2 -static -o $DEST $SOURCE -lm
exit $?
