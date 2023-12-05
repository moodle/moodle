//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';

require('./check-node');

const Connection = require('./lib/connection');
const ConnectionPromise = require('./lib/connection-promise');
const PoolPromise = require('./lib/pool-promise');
const Cluster = require('./lib/cluster');

const ConnOptions = require('./lib/config/connection-options');
const PoolOptions = require('./lib/config/pool-options');
const ClusterOptions = require('./lib/config/cluster-options');
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
  try {
    const options = new ConnOptions(opts);
    const conn = new Connection(options);
    const connPromise = new ConnectionPromise(conn);

    return conn.connect().then(() => Promise.resolve(connPromise));
  } catch (err) {
    return Promise.reject(err);
  }
};

module.exports.createPool = function createPool(opts) {
  const options = new PoolOptions(opts);
  const pool = new PoolPromise(options);
  // adding a default error handler to avoid exiting application on connection error.
  pool.on('error', (err) => {});
  return pool;
};

module.exports.createPoolCluster = function createPoolCluster(opts) {
  const options = new ClusterOptions(opts);
  return new Cluster(options);
};

module.exports.importFile = function importFile(opts) {
  try {
    const options = new ConnOptions(opts);
    const conn = new Connection(options);

    return conn
      .connect()
      .then(() => {
        return new Promise(conn.importFile.bind(conn, Object.assign({ skipDbCheck: true }, opts)));
      })
      .finally(() => {
        new Promise(conn.end.bind(conn, new CommandParameter())).catch(console.log);
      });
  } catch (err) {
    return Promise.reject(err);
  }
};
