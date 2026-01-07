<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Baremetalsolution;

class NfsExport extends \Google\Model
{
  /**
   * Unspecified value.
   */
  public const PERMISSIONS_PERMISSIONS_UNSPECIFIED = 'PERMISSIONS_UNSPECIFIED';
  /**
   * Read-only permission.
   */
  public const PERMISSIONS_READ_ONLY = 'READ_ONLY';
  /**
   * Read-write permission.
   */
  public const PERMISSIONS_READ_WRITE = 'READ_WRITE';
  /**
   * Allow dev flag in NfsShare AllowedClientsRequest.
   *
   * @var bool
   */
  public $allowDev;
  /**
   * Allow the setuid flag.
   *
   * @var bool
   */
  public $allowSuid;
  /**
   * A CIDR range.
   *
   * @var string
   */
  public $cidr;
  /**
   * Either a single machine, identified by an ID, or a comma-separated list of
   * machine IDs.
   *
   * @var string
   */
  public $machineId;
  /**
   * Network to use to publish the export.
   *
   * @var string
   */
  public $networkId;
  /**
   * Disable root squashing, which is a feature of NFS. Root squash is a special
   * mapping of the remote superuser (root) identity when using identity
   * authentication.
   *
   * @var bool
   */
  public $noRootSquash;
  /**
   * Export permissions.
   *
   * @var string
   */
  public $permissions;

  /**
   * Allow dev flag in NfsShare AllowedClientsRequest.
   *
   * @param bool $allowDev
   */
  public function setAllowDev($allowDev)
  {
    $this->allowDev = $allowDev;
  }
  /**
   * @return bool
   */
  public function getAllowDev()
  {
    return $this->allowDev;
  }
  /**
   * Allow the setuid flag.
   *
   * @param bool $allowSuid
   */
  public function setAllowSuid($allowSuid)
  {
    $this->allowSuid = $allowSuid;
  }
  /**
   * @return bool
   */
  public function getAllowSuid()
  {
    return $this->allowSuid;
  }
  /**
   * A CIDR range.
   *
   * @param string $cidr
   */
  public function setCidr($cidr)
  {
    $this->cidr = $cidr;
  }
  /**
   * @return string
   */
  public function getCidr()
  {
    return $this->cidr;
  }
  /**
   * Either a single machine, identified by an ID, or a comma-separated list of
   * machine IDs.
   *
   * @param string $machineId
   */
  public function setMachineId($machineId)
  {
    $this->machineId = $machineId;
  }
  /**
   * @return string
   */
  public function getMachineId()
  {
    return $this->machineId;
  }
  /**
   * Network to use to publish the export.
   *
   * @param string $networkId
   */
  public function setNetworkId($networkId)
  {
    $this->networkId = $networkId;
  }
  /**
   * @return string
   */
  public function getNetworkId()
  {
    return $this->networkId;
  }
  /**
   * Disable root squashing, which is a feature of NFS. Root squash is a special
   * mapping of the remote superuser (root) identity when using identity
   * authentication.
   *
   * @param bool $noRootSquash
   */
  public function setNoRootSquash($noRootSquash)
  {
    $this->noRootSquash = $noRootSquash;
  }
  /**
   * @return bool
   */
  public function getNoRootSquash()
  {
    return $this->noRootSquash;
  }
  /**
   * Export permissions.
   *
   * Accepted values: PERMISSIONS_UNSPECIFIED, READ_ONLY, READ_WRITE
   *
   * @param self::PERMISSIONS_* $permissions
   */
  public function setPermissions($permissions)
  {
    $this->permissions = $permissions;
  }
  /**
   * @return self::PERMISSIONS_*
   */
  public function getPermissions()
  {
    return $this->permissions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NfsExport::class, 'Google_Service_Baremetalsolution_NfsExport');
