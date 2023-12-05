//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

/**
 * Column definition packet "Field detail" flag value
 * see : https://mariadb.com/kb/en/library/resultset/#field-detail-flag
 */

//	field cannot be null
module.exports.NOT_NULL = 1;
//	field is a primary key
module.exports.PRIMARY_KEY = 2;
//field is unique
module.exports.UNIQUE_KEY = 4;
//field is in a multiple key
module.exports.MULTIPLE_KEY = 8;
//is this field a Blob
module.exports.BLOB = 1 << 4;
//	is this field unsigned
module.exports.UNSIGNED = 1 << 5;
//is this field a zerofill
module.exports.ZEROFILL_FLAG = 1 << 6;
//whether this field has a binary collation
module.exports.BINARY_COLLATION = 1 << 7;
//Field is an enumeration
module.exports.ENUM = 1 << 8;
//field auto-increment
module.exports.AUTO_INCREMENT = 1 << 9;
//field is a timestamp value
module.exports.TIMESTAMP = 1 << 10;
//field is a SET
module.exports.SET = 1 << 11;
//field doesn't have default value
module.exports.NO_DEFAULT_VALUE_FLAG = 1 << 12;
//field is set to NOW on UPDATE
module.exports.ON_UPDATE_NOW_FLAG = 1 << 13;
//field is num
module.exports.NUM_FLAG = 1 << 14;
