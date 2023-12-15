//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';

require('./check-node');

const ConnectionCallback = require('./lib/connection-callback');
const ClusterCallback = require('./lib/cluster-callback');
const PoolCallback = require('./lib/pool-callback');

const ConnOptions = require('./lib/config/connection-options');
const PoolOptions = require('./lib/config/pool-options');
const ClusterOptions = require('./lib/config/cluster-options');
const Connection = require('./lib/connection');
const CommandParameter = require('./lib/command-parameter');

module.exports.version = require('./package.json').version;
module.exports.SqlError = require('./lib/misc/errors').SqlError;

module.exports.defaultOptions = function defaultOptions(opts) {
  const connOpts = new ConnOptions(opts);
  const res = {};
  for (const [key, value] of Object.entries(connOpts)) {
    if (!key.startsWith('_')) {
      res[key] = value;
    }
  }
  return res;
};

module.exports.createConnection = function createConnection(opts) {
  const conn = new Connection(new ConnOptions(opts));
  const connCallback = new ConnectionCallback(conn);
  conn
    .connect()
    .then(
      function () {
        conn.emit('connect');
      }.bind(conn)
    )
    .catch(conn.emit.bind(conn, 'connect'));
  return connCallback;
};

exports.createPool = function createPool(opts) {
  const options = new PoolOptions(opts);
  const pool = new PoolCallback(options);
  // adding a default error handler to avoid exiting application on connection error.
  pool.on('error', (err) => {});
  return pool;
};

exports.createPoolCluster = function createPoolCluster(opts) {
  const options = new ClusterOptions(opts);
  return new ClusterCallback(options);
};

module.exports.importFile = function importFile(opts, callback) {
  const cb = callback ? callback : () => {};
  try {
    const options = new ConnOptions(opts);
    const conn = new Connection(options);
    conn
      .connect()
      .then(() => {
        return new Promise(conn.importFile.bind(conn, Object.assign({ skipDbCheck: true }, opts)));
      })
      .then(() => cb())
      .catch((err) => cb(err))
      .finally(() => {
        new Promise(conn.end.bind(conn, new CommandParameter())).catch(console.log);
      });
  } catch (err) {
    cb(err);
  }
};
