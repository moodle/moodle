//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

/**
 * Field types
 * see https://mariadb.com/kb/en/library/resultset/#field-types
 */

module.exports.DECIMAL = 0;
module.exports.TINY = 1;
module.exports.SHORT = 2;
module.exports.INT = 3;
module.exports.FLOAT = 4;
module.exports.DOUBLE = 5;
module.exports.NULL = 6;
module.exports.TIMESTAMP = 7;
module.exports.BIGINT = 8;
module.exports.INT24 = 9;
module.exports.DATE = 10;
module.exports.TIME = 11;
module.exports.DATETIME = 12;
module.exports.YEAR = 13;
module.exports.NEWDATE = 14;
module.exports.VARCHAR = 15;
module.exports.BIT = 16;
module.exports.TIMESTAMP2 = 17;
module.exports.DATETIME2 = 18;
module.exports.TIME2 = 19;
module.exports.JSON = 245; //only for MySQL
module.exports.NEWDECIMAL = 246;
module.exports.ENUM = 247;
module.exports.SET = 248;
module.exports.TINY_BLOB = 249;
module.exports.MEDIUM_BLOB = 250;
module.exports.LONG_BLOB = 251;
module.exports.BLOB = 252;
module.exports.VAR_STRING = 253;
module.exports.STRING = 254;
module.exports.GEOMETRY = 255;

const typeNames = [];
typeNames[0] = 'DECIMAL';
typeNames[1] = 'TINY';
typeNames[2] = 'SHORT';
typeNames[3] = 'INT';
typeNames[4] = 'FLOAT';
typeNames[5] = 'DOUBLE';
typeNames[6] = 'NULL';
typeNames[7] = 'TIMESTAMP';
typeNames[8] = 'BIGINT';
typeNames[9] = 'INT24';
typeNames[10] = 'DATE';
typeNames[11] = 'TIME';
typeNames[12] = 'DATETIME';
typeNames[13] = 'YEAR';
typeNames[14] = 'NEWDATE';
typeNames[15] = 'VARCHAR';
typeNames[16] = 'BIT';
typeNames[17] = 'TIMESTAMP2';
typeNames[18] = 'DATETIME2';
typeNames[19] = 'TIME2';
typeNames[245] = 'JSON';
typeNames[246] = 'NEWDECIMAL';
typeNames[247] = 'ENUM';
typeNames[248] = 'SET';
typeNames[249] = 'TINY_BLOB';
typeNames[250] = 'MEDIUM_BLOB';
typeNames[251] = 'LONG_BLOB';
typeNames[252] = 'BLOB';
typeNames[253] = 'VAR_STRING';
typeNames[254] = 'STRING';
typeNames[255] = 'GEOMETRY';

module.exports.TYPES = typeNames;
