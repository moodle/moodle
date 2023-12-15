//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';

const Command = require('../../command');

/**
 * Base authentication plugin
 */
class PluginAuth extends Command {
  constructor(cmdParam, multiAuthResolver, reject) {
    super(cmdParam, multiAuthResolver, reject);
    this.onPacketReceive = multiAuthResolver;
  }
}

module.exports = PluginAuth;
