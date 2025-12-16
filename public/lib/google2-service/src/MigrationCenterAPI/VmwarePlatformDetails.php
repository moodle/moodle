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

class VmwarePlatformDetails extends \Google\Model
{
  /**
   * Simultaneous Multithreading status unknown.
   */
  public const ESX_HYPERTHREADING_HYPERTHREADING_STATUS_UNSPECIFIED = 'HYPERTHREADING_STATUS_UNSPECIFIED';
  /**
   * Simultaneous Multithreading is disabled or unavailable.
   */
  public const ESX_HYPERTHREADING_HYPERTHREADING_STATUS_DISABLED = 'HYPERTHREADING_STATUS_DISABLED';
  /**
   * Simultaneous Multithreading is enabled.
   */
  public const ESX_HYPERTHREADING_HYPERTHREADING_STATUS_ENABLED = 'HYPERTHREADING_STATUS_ENABLED';
  /**
   * Whether the ESX is hyperthreaded.
   *
   * @var string
   */
  public $esxHyperthreading;
  /**
   * ESX version.
   *
   * @var string
   */
  public $esxVersion;
  /**
   * VMware os enum - https://vdc-repo.vmware.com/vmwb-repository/dcr-public/da4
   * 7f910-60ac-438b-8b9b-6122f4d14524/16b7274a-bf8b-4b4c-a05e-
   * 746f2aa93c8c/doc/vim.vm.GuestOsDescriptor.GuestOsIdentifier.html.
   *
   * @var string
   */
  public $osid;
  /**
   * Folder name in vCenter where asset resides.
   *
   * @var string
   */
  public $vcenterFolder;
  /**
   * vCenter URI used in collection.
   *
   * @var string
   */
  public $vcenterUri;
  /**
   * vCenter version.
   *
   * @var string
   */
  public $vcenterVersion;
  /**
   * vCenter VM ID.
   *
   * @var string
   */
  public $vcenterVmId;

  /**
   * Whether the ESX is hyperthreaded.
   *
   * Accepted values: HYPERTHREADING_STATUS_UNSPECIFIED,
   * HYPERTHREADING_STATUS_DISABLED, HYPERTHREADING_STATUS_ENABLED
   *
   * @param self::ESX_HYPERTHREADING_* $esxHyperthreading
   */
  public function setEsxHyperthreading($esxHyperthreading)
  {
    $this->esxHyperthreading = $esxHyperthreading;
  }
  /**
   * @return self::ESX_HYPERTHREADING_*
   */
  public function getEsxHyperthreading()
  {
    return $this->esxHyperthreading;
  }
  /**
   * ESX version.
   *
   * @param string $esxVersion
   */
  public function setEsxVersion($esxVersion)
  {
    $this->esxVersion = $esxVersion;
  }
  /**
   * @return string
   */
  public function getEsxVersion()
  {
    return $this->esxVersion;
  }
  /**
   * VMware os enum - https://vdc-repo.vmware.com/vmwb-repository/dcr-public/da4
   * 7f910-60ac-438b-8b9b-6122f4d14524/16b7274a-bf8b-4b4c-a05e-
   * 746f2aa93c8c/doc/vim.vm.GuestOsDescriptor.GuestOsIdentifier.html.
   *
   * @param string $osid
   */
  public function setOsid($osid)
  {
    $this->osid = $osid;
  }
  /**
   * @return string
   */
  public function getOsid()
  {
    return $this->osid;
  }
  /**
   * Folder name in vCenter where asset resides.
   *
   * @param string $vcenterFolder
   */
  public function setVcenterFolder($vcenterFolder)
  {
    $this->vcenterFolder = $vcenterFolder;
  }
  /**
   * @return string
   */
  public function getVcenterFolder()
  {
    return $this->vcenterFolder;
  }
  /**
   * vCenter URI used in collection.
   *
   * @param string $vcenterUri
   */
  public function setVcenterUri($vcenterUri)
  {
    $this->vcenterUri = $vcenterUri;
  }
  /**
   * @return string
   */
  public function getVcenterUri()
  {
    return $this->vcenterUri;
  }
  /**
   * vCenter version.
   *
   * @param string $vcenterVersion
   */
  public function setVcenterVersion($vcenterVersion)
  {
    $this->vcenterVersion = $vcenterVersion;
  }
  /**
   * @return string
   */
  public function getVcenterVersion()
  {
    return $this->vcenterVersion;
  }
  /**
   * vCenter VM ID.
   *
   * @param string $vcenterVmId
   */
  public function setVcenterVmId($vcenterVmId)
  {
    $this->vcenterVmId = $vcenterVmId;
  }
  /**
   * @return string
   */
  public function getVcenterVmId()
  {
    return $this->vcenterVmId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwarePlatformDetails::class, 'Google_Service_MigrationCenterAPI_VmwarePlatformDetails');
