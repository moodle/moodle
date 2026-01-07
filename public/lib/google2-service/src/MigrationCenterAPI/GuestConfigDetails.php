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

namespace Google\Service\MigrationCenterAPI;

class GuestConfigDetails extends \Google\Model
{
  /**
   * SELinux mode unknown or unspecified.
   */
  public const SELINUX_MODE_SE_LINUX_MODE_UNSPECIFIED = 'SE_LINUX_MODE_UNSPECIFIED';
  /**
   * SELinux is disabled.
   */
  public const SELINUX_MODE_SE_LINUX_MODE_DISABLED = 'SE_LINUX_MODE_DISABLED';
  /**
   * SELinux permissive mode.
   */
  public const SELINUX_MODE_SE_LINUX_MODE_PERMISSIVE = 'SE_LINUX_MODE_PERMISSIVE';
  /**
   * SELinux enforcing mode.
   */
  public const SELINUX_MODE_SE_LINUX_MODE_ENFORCING = 'SE_LINUX_MODE_ENFORCING';
  protected $fstabType = FstabEntryList::class;
  protected $fstabDataType = '';
  protected $hostsType = HostsEntryList::class;
  protected $hostsDataType = '';
  /**
   * OS issue (typically /etc/issue in Linux).
   *
   * @var string
   */
  public $issue;
  protected $nfsExportsType = NfsExportList::class;
  protected $nfsExportsDataType = '';
  /**
   * Security-Enhanced Linux (SELinux) mode.
   *
   * @var string
   */
  public $selinuxMode;

  /**
   * Mount list (Linux fstab).
   *
   * @param FstabEntryList $fstab
   */
  public function setFstab(FstabEntryList $fstab)
  {
    $this->fstab = $fstab;
  }
  /**
   * @return FstabEntryList
   */
  public function getFstab()
  {
    return $this->fstab;
  }
  /**
   * Hosts file (/etc/hosts).
   *
   * @param HostsEntryList $hosts
   */
  public function setHosts(HostsEntryList $hosts)
  {
    $this->hosts = $hosts;
  }
  /**
   * @return HostsEntryList
   */
  public function getHosts()
  {
    return $this->hosts;
  }
  /**
   * OS issue (typically /etc/issue in Linux).
   *
   * @param string $issue
   */
  public function setIssue($issue)
  {
    $this->issue = $issue;
  }
  /**
   * @return string
   */
  public function getIssue()
  {
    return $this->issue;
  }
  /**
   * NFS exports.
   *
   * @param NfsExportList $nfsExports
   */
  public function setNfsExports(NfsExportList $nfsExports)
  {
    $this->nfsExports = $nfsExports;
  }
  /**
   * @return NfsExportList
   */
  public function getNfsExports()
  {
    return $this->nfsExports;
  }
  /**
   * Security-Enhanced Linux (SELinux) mode.
   *
   * Accepted values: SE_LINUX_MODE_UNSPECIFIED, SE_LINUX_MODE_DISABLED,
   * SE_LINUX_MODE_PERMISSIVE, SE_LINUX_MODE_ENFORCING
   *
   * @param self::SELINUX_MODE_* $selinuxMode
   */
  public function setSelinuxMode($selinuxMode)
  {
    $this->selinuxMode = $selinuxMode;
  }
  /**
   * @return self::SELINUX_MODE_*
   */
  public function getSelinuxMode()
  {
    return $this->selinuxMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GuestConfigDetails::class, 'Google_Service_MigrationCenterAPI_GuestConfigDetails');
