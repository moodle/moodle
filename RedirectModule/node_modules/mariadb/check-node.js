//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

'use strict';

const hasMinVersion = function (nodeVersionStr, connectorRequirement) {
  const versNode = nodeVersionStr.split('.');
  const versReq = connectorRequirement.split('.');

  const majorNode = Number(versNode[0]);
  const majorReq = Number(versReq[0]);
  if (majorNode > majorReq) return true;
  if (majorNode < majorReq) return false;

  if (versReq.length === 1) return true;

  const minorNode = Number(versNode[1]);
  const minorReq = Number(versReq[1]);
  if (minorNode > minorReq) return true;
  if (minorNode < minorReq) return false;

  return true;
};

module.exports.hasMinVersion = hasMinVersion;

const requirement = require('./package.json').engines.node;
const connectorRequirement = requirement.replace('>=', '').trim();
const currentNodeVersion = process.version.replace('v', '');
if (!hasMinVersion(currentNodeVersion, connectorRequirement)) {
  console.error(`please upgrade node: mariadb requires at least version ${connectorRequirement}`);
  process.exit(1);
}
