//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

/**
 * Capabilities list ( with 'CLIENT_' removed)
 * see : https://mariadb.com/kb/en/library/1-connecting-connecting/#capabilities
 */
/* mysql/old mariadb server/client */
module.exports.MYSQL = 1n;
/* Found instead of affected rows */
module.exports.FOUND_ROWS = 2n;
/* get all column flags */
module.exports.LONG_FLAG = 4n;
/* one can specify db on connect */
module.exports.CONNECT_WITH_DB = 8n;
/* don't allow database.table.column */
module.exports.NO_SCHEMA = 1n << 4n;
/* can use compression protocol */
module.exports.COMPRESS = 1n << 5n;
/* odbc client */
module.exports.ODBC = 1n << 6n;
/* can use LOAD DATA LOCAL */
module.exports.LOCAL_FILES = 1n << 7n;
/* ignore spaces before '' */
module.exports.IGNORE_SPACE = 1n << 8n;
/* new 4.1 protocol */
module.exports.PROTOCOL_41 = 1n << 9n;
/* this is an interactive client */
module.exports.INTERACTIVE = 1n << 10n;
/* switch to ssl after handshake */
module.exports.SSL = 1n << 11n;
/* IGNORE sigpipes */
module.exports.IGNORE_SIGPIPE = 1n << 12n;
/* client knows about transactions */
module.exports.TRANSACTIONS = 1n << 13n;
/* old flag for 4.1 protocol  */
module.exports.RESERVED = 1n << 14n;
/* new 4.1 authentication */
module.exports.SECURE_CONNECTION = 1n << 15n;
/* enable/disable multi-stmt support */
module.exports.MULTI_STATEMENTS = 1n << 16n;
/* enable/disable multi-results */
module.exports.MULTI_RESULTS = 1n << 17n;
/* multi-results in ps-protocol */
module.exports.PS_MULTI_RESULTS = 1n << 18n;
/* client supports plugin authentication */
module.exports.PLUGIN_AUTH = 1n << 19n;
/* permits connection attributes */
module.exports.CONNECT_ATTRS = 1n << 20n;
/* Enable authentication response packet to be larger than 255 bytes. */
module.exports.PLUGIN_AUTH_LENENC_CLIENT_DATA = 1n << 21n;
/* Don't close the connection for a connection with expired password. */
module.exports.CAN_HANDLE_EXPIRED_PASSWORDS = 1n << 22n;
/* Capable of handling server state change information. Its a hint to the
  server to include the state change information in Ok packet. */
module.exports.SESSION_TRACK = 1n << 23n;
/* Client no longer needs EOF packet */
module.exports.DEPRECATE_EOF = 1n << 24n;
module.exports.SSL_VERIFY_SERVER_CERT = 1n << 30n;

/* MariaDB extended capabilities */

/* Permit bulk insert*/
module.exports.MARIADB_CLIENT_STMT_BULK_OPERATIONS = 1n << 34n;

/* Clients supporting extended metadata */
module.exports.MARIADB_CLIENT_EXTENDED_TYPE_INFO = 1n << 35n;
module.exports.MARIADB_CLIENT_CACHE_METADATA = 1n << 36n;
