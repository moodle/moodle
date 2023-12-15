//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';

const Query = require('./query');
const { Readable } = require('stream');

/**
 * Protocol COM_QUERY with streaming events.
 * see : https://mariadb.com/kb/en/library/com_query/
 */
class Stream extends Query {
  constructor(cmdParam, connOpts, socket) {
    super(
      () => {},
      () => {},
      connOpts,
      cmdParam
    );
    this.socket = socket;
    this.inStream = new Readable({
      objectMode: true,
      read: () => {
        this.socket.resume();
      }
    });

    this.on('fields', function (meta) {
      this.inStream.emit('fields', meta);
    });

    this.on('error', function (err) {
      this.inStream.emit('error', err);
    });

    this.on('close', function (err) {
      this.inStream.emit('error', err);
    });

    this.on('end', function (err) {
      if (err) this.inStream.emit('error', err);
      this.socket.resume();
      this.inStream.push(null);
    });

    this.inStream.close = function () {
      this.handleNewRows = () => {};
      this.socket.resume();
    }.bind(this);
  }

  handleNewRows(row) {
    if (!this.inStream.push(row)) {
      this.socket.pause();
    }
  }
}

module.exports = Stream;
