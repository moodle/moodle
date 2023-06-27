-- This file is part of Moodle - http://moodle.org/
--
-- Moodle is free software: you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.
--
-- Moodle is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    core_dml
 * @copyright  2009 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @version    20091010 (plz, keep this updated for easier reference)
 */

/**
 * This sql script generates various PL/SQL packages needed to provide
 * cross-db compatibility in the Moodle 2.x DB API with some operations
 * not natively supported by Oracle, namely:
 *  - locking: Application locks used by Moodle DB sessions. It uses
 *             the DBMS_LOCK package so execution must be granted
 *             to the Moodle DB user by SYS to work properly.
 *  - bit ops: To provide cross-db bitwise operations to be used by the
 *             sql_bitXXX() helper functions
 *  - one space hacks: One space empty string substitute hacks.
 *
 * Moodle will not parse this file correctly if it uses Windows line endings.
 */

CREATE OR REPLACE PACKAGE MOODLELIB AS

FUNCTION BITOR (value1 IN INTEGER, value2 IN INTEGER) RETURN INTEGER;
FUNCTION BITXOR(value1 IN INTEGER, value2 IN INTEGER) RETURN INTEGER;

FUNCTION GET_HANDLE  (lock_name IN VARCHAR2) RETURN VARCHAR2;
FUNCTION GET_LOCK    (lock_name IN VARCHAR2, lock_timeout IN INTEGER) RETURN INTEGER;
FUNCTION RELEASE_LOCK(lock_name IN VARCHAR2) RETURN INTEGER;

FUNCTION UNDO_DIRTY_HACK(hackedstring IN VARCHAR2) RETURN VARCHAR2;
FUNCTION UNDO_MEGA_HACK(hackedstring IN VARCHAR2) RETURN VARCHAR2;
FUNCTION TRICONCAT(string1 IN VARCHAR2, string2 IN VARCHAR2, string3 IN VARCHAR2) RETURN VARCHAR2;

END MOODLELIB;
/

CREATE OR REPLACE PACKAGE BODY MOODLELIB AS

FUNCTION BITOR(value1 IN INTEGER, value2 IN INTEGER) RETURN INTEGER IS

BEGIN
    RETURN value1 + value2 - BITAND(value1,value2);
END BITOR;

FUNCTION BITXOR(value1 IN INTEGER, value2 IN INTEGER) RETURN INTEGER IS

BEGIN
    RETURN MOODLELIB.BITOR(value1,value2) - BITAND(value1,value2);
END BITXOR;

FUNCTION GET_HANDLE(lock_name IN VARCHAR2) RETURN VARCHAR2 IS
    PRAGMA AUTONOMOUS_TRANSACTION;
    lock_handle VARCHAR2(128);

BEGIN
    DBMS_LOCK.ALLOCATE_UNIQUE (
        lockname => lock_name,
        lockhandle => lock_handle,
        expiration_secs => 864000);
    RETURN lock_handle;
END GET_HANDLE;

FUNCTION GET_LOCK(lock_name IN VARCHAR2, lock_timeout IN INTEGER) RETURN INTEGER IS
    lock_status NUMBER;
BEGIN
    lock_status := DBMS_LOCK.REQUEST(
                       lockhandle => GET_HANDLE(lock_name),
                       lockmode => DBMS_LOCK.X_MODE, -- eXclusive
                       timeout => lock_timeout,
                       release_on_commit => FALSE);
    CASE lock_status
        WHEN 0 THEN NULL;
        WHEN 2 THEN RAISE_APPLICATION_ERROR(-20000,'deadlock detected');
        WHEN 4 THEN RAISE_APPLICATION_ERROR(-20000,'lock already obtained');
        ELSE RAISE_APPLICATION_ERROR(-20000,'request lock failed - ' || lock_status);
    END CASE;
    RETURN 1;
END GET_LOCK;

FUNCTION RELEASE_LOCK(lock_name IN VARCHAR2) RETURN INTEGER IS
    lock_status NUMBER;
BEGIN
    lock_status := DBMS_LOCK.RELEASE(
                      lockhandle => GET_HANDLE(lock_name));
    IF lock_status > 0 THEN
        RAISE_APPLICATION_ERROR(-20000,'release lock failed - ' || lock_status);
    END IF;
    RETURN 1;
END RELEASE_LOCK;

FUNCTION UNDO_DIRTY_HACK(hackedstring IN VARCHAR2) RETURN VARCHAR2 IS

BEGIN
    IF hackedstring = ' ' THEN
        RETURN '';
    END IF;
    RETURN hackedstring;
END UNDO_DIRTY_HACK;

FUNCTION UNDO_MEGA_HACK(hackedstring IN VARCHAR2) RETURN VARCHAR2 IS

BEGIN
    IF hackedstring IS NULL THEN
        RETURN hackedstring;
    END IF;
    RETURN REPLACE(hackedstring, '*OCISP*', ' ');
END UNDO_MEGA_HACK;

FUNCTION TRICONCAT(string1 IN VARCHAR2, string2 IN VARCHAR2, string3 IN VARCHAR2) RETURN VARCHAR2 IS
    stringresult VARCHAR2(1333);
BEGIN
    IF string1 IS NULL THEN
        RETURN NULL;
    END IF;
    IF string2 IS NULL THEN
        RETURN NULL;
    END IF;
    IF string3 IS NULL THEN
        RETURN NULL;
    END IF;

    stringresult := CONCAT(CONCAT(MOODLELIB.UNDO_DIRTY_HACK(string1), MOODLELIB.UNDO_DIRTY_HACK(string2)), MOODLELIB.UNDO_DIRTY_HACK(string3));

    IF stringresult IS NULL THEN
        RETURN ' ';
    END IF;

    RETURN stringresult;
END;

END MOODLELIB;
/

SHOW ERRORS
/
