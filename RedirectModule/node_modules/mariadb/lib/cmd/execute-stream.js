'use strict';

const Execute = require('./execute');
const { Readable } = require('stream');

/**
 * Protocol COM_STMT_EXECUTE with streaming events.
 * see : https://mariadb.com/kb/en/com_stmt_execute/
 */
class ExecuteStream extends Execute {
  constructor(cmdParam, connOpts, prepare, socket) {
    super(
      () => {},
      () => {},
      connOpts,
      cmdParam,
      prepare
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

module.exports = ExecuteStream;
