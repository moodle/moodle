//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';
const ErrorCodes = require('../const/error-code');

class SqlError extends Error {
  constructor(msg, sql, fatal, info, sqlState, errno, additionalStack, addHeader = undefined) {
    super(
      (addHeader === undefined || addHeader
        ? `(conn=${info ? (info.threadId ? info.threadId : -1) : -1}, no: ${errno ? errno : -1}, SQLState: ${
            sqlState ? sqlState : 'HY000'
          }) `
        : '') +
        msg +
        (sql ? '\nsql: ' + sql : '')
    );
    this.name = 'SqlError';
    this.sqlMessage = msg;
    this.sql = sql;
    this.fatal = fatal;
    this.errno = errno;
    this.sqlState = sqlState;
    if (errno > 45000 && errno < 46000) {
      //driver error
      this.code = errByNo[errno] || 'UNKNOWN';
    } else {
      this.code = ErrorCodes.codes[this.errno] || 'UNKNOWN';
    }
    if (additionalStack) {
      //adding caller stack, removing initial "Error:\n"
      this.stack += '\n From event:\n' + additionalStack.substring(additionalStack.indexOf('\n') + 1);
    }
  }

  get text() {
    return this.sqlMessage;
  }
}

/**
 * Error factory, so error get connection information.
 *
 * @param msg               current error message
 * @param errno             error number
 * @param info              connection information
 * @param sqlState          sql state
 * @param sql               sql command
 * @param fatal             is error fatal
 * @param additionalStack   additional stack trace to see
 * @param addHeader         add connection information
 * @returns {Error} the error
 */
module.exports.createError = function (
  msg,
  errno,
  info = null,
  sqlState = 'HY000',
  sql = null,
  fatal = false,
  additionalStack = undefined,
  addHeader = undefined
) {
  return new SqlError(msg, sql, fatal, info, sqlState, errno, additionalStack, addHeader);
};

/**
 * Fatal error factory, so error get connection information.
 *
 * @param msg               current error message
 * @param errno             error number
 * @param info              connection information
 * @param sqlState          sql state
 * @param sql               sql command
 * @param additionalStack   additional stack trace to see
 * @param addHeader         add connection information
 * @returns {Error} the error
 */
module.exports.createFatalError = function (
  msg,
  errno,
  info = null,
  sqlState = '08S01',
  sql = null,
  additionalStack = undefined,
  addHeader = undefined
) {
  return new SqlError(msg, sql, true, info, sqlState, errno, additionalStack, addHeader);
};

/********************************************************************************
 * Driver specific errors
 ********************************************************************************/

module.exports.ER_CONNECTION_ALREADY_CLOSED = 45001;
module.exports.ER_MYSQL_CHANGE_USER_BUG = 45003;
module.exports.ER_CMD_NOT_EXECUTED_DESTROYED = 45004;
module.exports.ER_NULL_CHAR_ESCAPEID = 45005;
module.exports.ER_NULL_ESCAPEID = 45006;
module.exports.ER_NOT_IMPLEMENTED_FORMAT = 45007;
module.exports.ER_NODE_NOT_SUPPORTED_TLS = 45008;
module.exports.ER_SOCKET_UNEXPECTED_CLOSE = 45009;
module.exports.ER_UNEXPECTED_PACKET = 45011;
module.exports.ER_CONNECTION_TIMEOUT = 45012;
module.exports.ER_CMD_CONNECTION_CLOSED = 45013;
module.exports.ER_CHANGE_USER_BAD_PACKET = 45014;
module.exports.ER_PING_BAD_PACKET = 45015;
module.exports.ER_MISSING_PARAMETER = 45016;
module.exports.ER_PARAMETER_UNDEFINED = 45017;
module.exports.ER_PLACEHOLDER_UNDEFINED = 45018;
module.exports.ER_SOCKET = 45019;
module.exports.ER_EOF_EXPECTED = 45020;
module.exports.ER_LOCAL_INFILE_DISABLED = 45021;
module.exports.ER_LOCAL_INFILE_NOT_READABLE = 45022;
module.exports.ER_SERVER_SSL_DISABLED = 45023;
module.exports.ER_AUTHENTICATION_BAD_PACKET = 45024;
module.exports.ER_AUTHENTICATION_PLUGIN_NOT_SUPPORTED = 45025;
module.exports.ER_SOCKET_TIMEOUT = 45026;
module.exports.ER_POOL_ALREADY_CLOSED = 45027;
module.exports.ER_GET_CONNECTION_TIMEOUT = 45028;
module.exports.ER_SETTING_SESSION_ERROR = 45029;
module.exports.ER_INITIAL_SQL_ERROR = 45030;
module.exports.ER_BATCH_WITH_NO_VALUES = 45031;
module.exports.ER_RESET_BAD_PACKET = 45032;
module.exports.ER_WRONG_IANA_TIMEZONE = 45033;
module.exports.ER_LOCAL_INFILE_WRONG_FILENAME = 45034;
module.exports.ER_ADD_CONNECTION_CLOSED_POOL = 45035;
module.exports.ER_WRONG_AUTO_TIMEZONE = 45036;
module.exports.ER_CLOSING_POOL = 45037;
module.exports.ER_TIMEOUT_NOT_SUPPORTED = 45038;
module.exports.ER_INITIAL_TIMEOUT_ERROR = 45039;
module.exports.ER_DUPLICATE_FIELD = 45040;
module.exports.ER_PING_TIMEOUT = 45042;
module.exports.ER_BAD_PARAMETER_VALUE = 45043;
module.exports.ER_CANNOT_RETRIEVE_RSA_KEY = 45044;
module.exports.ER_MINIMUM_NODE_VERSION_REQUIRED = 45045;
module.exports.ER_MAX_ALLOWED_PACKET = 45046;
module.exports.ER_NOT_SUPPORTED_AUTH_PLUGIN = 45047;
module.exports.ER_COMPRESSION_NOT_SUPPORTED = 45048;
module.exports.ER_UNDEFINED_SQL = 45049;
module.exports.ER_PARSING_PRECISION = 45050;
module.exports.ER_PREPARE_CLOSED = 45051;
module.exports.ER_MISSING_SQL_PARAMETER = 45052;
module.exports.ER_MISSING_SQL_FILE = 45053;
module.exports.ER_SQL_FILE_ERROR = 45054;
module.exports.ER_MISSING_DATABASE_PARAMETER = 45055;

const keys = Object.keys(module.exports);
const errByNo = {};
for (let i = 0; i < keys.length; i++) {
  const keyName = keys[i];
  if (keyName !== 'createError') {
    errByNo[module.exports[keyName]] = keyName;
  }
}

module.exports.SqlError = SqlError;
