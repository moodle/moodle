//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

/**
 * possible server status flag value
 * see https://mariadb.com/kb/en/library/ok_packet/#server-status-flag
 * @type {number}
 */
//A transaction is currently active
module.exports.STATUS_IN_TRANS = 1;
//Autocommit mode is set
module.exports.STATUS_AUTOCOMMIT = 2;
//more results exists (more packet follow)
module.exports.MORE_RESULTS_EXISTS = 8;
module.exports.QUERY_NO_GOOD_INDEX_USED = 16;
module.exports.QUERY_NO_INDEX_USED = 32;
//when using COM_STMT_FETCH, indicate that current cursor still has result (deprecated)
module.exports.STATUS_CURSOR_EXISTS = 64;
//when using COM_STMT_FETCH, indicate that current cursor has finished to send results (deprecated)
module.exports.STATUS_LAST_ROW_SENT = 128;
//database has been dropped
module.exports.STATUS_DB_DROPPED = 1 << 8;
//current escape mode is "no backslash escape"
module.exports.STATUS_NO_BACKSLASH_ESCAPES = 1 << 9;
//A DDL change did have an impact on an existing PREPARE (an automatic re-prepare has been executed)
module.exports.STATUS_METADATA_CHANGED = 1 << 10;
module.exports.QUERY_WAS_SLOW = 1 << 11;
//this result-set contain stored procedure output parameter
module.exports.PS_OUT_PARAMS = 1 << 12;
//current transaction is a read-only transaction
module.exports.STATUS_IN_TRANS_READONLY = 1 << 13;
//session state change. see Session change type for more information
module.exports.SESSION_STATE_CHANGED = 1 << 14;
