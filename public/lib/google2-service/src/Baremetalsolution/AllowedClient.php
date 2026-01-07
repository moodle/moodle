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

class AllowedClient extends \Google\Model
{
  /**
   * Permissions were not specified.
   */
  public const MOUNT_PERMISSIONS_MOUNT_PERMISSIONS_UNSPECIFIED = 'MOUNT_PERMISSIONS_UNSPECIFIED';
  /**
   * NFS share can be mount with read-only permissions.
   */
  public const MOUNT_PERMISSIONS_READ = 'READ';
  /**
   * NFS share can be mount with read-write permissions.
   */
  public const MOUNT_PERMISSIONS_READ_WRITE = 'READ_WRITE';
  /**
   * Allow dev flag. Which controls whether to allow creation of devices.
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
   * The subnet of IP addresses permitted to access the share.
   *
   * @var string
   */
  public $allowedClientsCidr;
  /**
   * Mount permissions.
   *
   * @var string
   */
  public $mountPermissions;
  /**
   * The network the access point sits on.
   *
   * @var string
   */
  public $network;
  /**
   * Output only. The path to access NFS, in format shareIP:/InstanceID
   * InstanceID is the generated ID instead of customer provided name. example
   * like "10.0.0.0:/g123456789-nfs001"
   *
   * @var string
   */
  public $nfsPath;
  /**
   * Disable root squashing, which is a feature of NFS. Root squash is a special
   * mapping of the remote superuser (root) identity when using identity
   * authentication.
   *
   * @var bool
   */
  public $noRootSquash;
  /**
   * Output only. The IP address of the share on this network. Assigned
   * automatically during provisioning based on the network's services_cidr.
   *
   * @var string
   */
  public $shareIp;

  /**
   * Allow dev flag. Which controls whether to allow creation of devices.
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
   * The subnet of IP addresses permitted to access the share.
   *
   * @param string $allowedClientsCidr
   */
  public function setAllowedClientsCidr($allowedClientsCidr)
  {
    $this->allowedClientsCidr = $allowedClientsCidr;
  }
  /**
   * @return string
   */
  public function getAllowedClientsCidr()
  {
    return $this->allowedClientsCidr;
  }
  /**
   * Mount permissions.
   *
   * Accepted values: MOUNT_PERMISSIONS_UNSPECIFIED, READ, READ_WRITE
   *
   * @param self::MOUNT_PERMISSIONS_* $mountPermissions
   */
  public function setMountPermissions($mountPermissions)
  {
    $this->mountPermissions = $mountPermissions;
  }
  /**
   * @return self::MOUNT_PERMISSIONS_*
   */
  public function getMountPermissions()
  {
    return $this->mountPermissions;
  }
  /**
   * The network the access point sits on.
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Output only. The path to access NFS, in format shareIP:/InstanceID
   * InstanceID is the generated ID instead of customer provided name. example
   * like "10.0.0.0:/g123456789-nfs001"
   *
   * @param string $nfsPath
   */
  public function setNfsPath($nfsPath)
  {
    $this->nfsPath = $nfsPath;
  }
  /**
   * @return string
   */
  public function getNfsPath()
  {
    return $this->nfsPath;
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
   * Output only. The IP address of the share on this network. Assigned
   * automatically during provisioning based on the network's services_cidr.
   *
   * @param string $shareIp
   */
  public function setShareIp($shareIp)
  {
    $this->shareIp = $shareIp;
  }
  /**
   * @return string
   */
  public function getShareIp()
  {
    return $this->shareIp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AllowedClient::class, 'Google_Service_Baremetalsolution_AllowedClient');
