'use strict';

class CommandParameter {
  constructor(sql, values, opts, callback) {
    this.sql = sql;
    this.values = values;
    this.opts = opts;
    this.callback = callback;
  }
}

module.exports = CommandParameter;
