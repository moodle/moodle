//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

/**
 * Session change type.
 * see : https://mariadb.com/kb/en/library/ok_packet/#session-change-type
 * @type {number}
 */

module.exports.SESSION_TRACK_SYSTEM_VARIABLES = 0;
module.exports.SESSION_TRACK_SCHEMA = 1;
module.exports.SESSION_TRACK_STATE_CHANGE = 2;
module.exports.SESSION_TRACK_GTIDS = 3;
module.exports.SESSION_TRACK_TRANSACTION_CHARACTERISTICS = 4;
module.exports.SESSION_TRACK_TRANSACTION_STATE = 5;
