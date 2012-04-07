@ECHO OFF
ECHO Initialising Moodle PHPUnit test environment...

CALL php %~dp0\util.php --diag > NUL 2>&1

IF ERRORLEVEL 133 GOTO drop
IF ERRORLEVEL 132 GOTO install
IF ERRORLEVEL 1 GOTO unknown
GOTO done

:drop
CALL php %~dp0\util.php --drop
IF ERRORLEVEL 1 GOTO done

:install
CALL php %~dp0\util.php --install
GOTO done

:unknown
CALL php %~dp0\util.php --diag

:done
