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

namespace Google\Service\GKEOnPrem;

class VmwareAdminVCenterConfig extends \Google\Model
{
  /**
   * The vCenter IP address.
   *
   * @var string
   */
  public $address;
  /**
   * Contains the vCenter CA certificate public key for SSL verification.
   *
   * @var string
   */
  public $caCertData;
  /**
   * The name of the vCenter cluster for the admin cluster.
   *
   * @var string
   */
  public $cluster;
  /**
   * The name of the virtual machine disk (VMDK) for the admin cluster.
   *
   * @var string
   */
  public $dataDisk;
  /**
   * The name of the vCenter datacenter for the admin cluster.
   *
   * @var string
   */
  public $datacenter;
  /**
   * The name of the vCenter datastore for the admin cluster.
   *
   * @var string
   */
  public $datastore;
  /**
   * The name of the vCenter folder for the admin cluster.
   *
   * @var string
   */
  public $folder;
  /**
   * The name of the vCenter resource pool for the admin cluster.
   *
   * @var string
   */
  public $resourcePool;
  /**
   * The name of the vCenter storage policy for the user cluster.
   *
   * @var string
   */
  public $storagePolicyName;

  /**
   * The vCenter IP address.
   *
   * @param string $address
   */
  public function setAddress($address)
  {
    $this->address = $address;
  }
  /**
   * @return string
   */
  public function getAddress()
  {
    return $this->address;
  }
  /**
   * Contains the vCenter CA certificate public key for SSL verification.
   *
   * @param string $caCertData
   */
  public function setCaCertData($caCertData)
  {
    $this->caCertData = $caCertData;
  }
  /**
   * @return string
   */
  public function getCaCertData()
  {
    return $this->caCertData;
  }
  /**
   * The name of the vCenter cluster for the admin cluster.
   *
   * @param string $cluster
   */
  public function setCluster($cluster)
  {
    $this->cluster = $cluster;
  }
  /**
   * @return string
   */
  public function getCluster()
  {
    return $this->cluster;
  }
  /**
   * The name of the virtual machine disk (VMDK) for the admin cluster.
   *
   * @param string $dataDisk
   */
  public function setDataDisk($dataDisk)
  {
    $this->dataDisk = $dataDisk;
  }
  /**
   * @return string
   */
  public function getDataDisk()
  {
    return $this->dataDisk;
  }
  /**
   * The name of the vCenter datacenter for the admin cluster.
   *
   * @param string $datacenter
   */
  public function setDatacenter($datacenter)
  {
    $this->datacenter = $datacenter;
  }
  /**
   * @return string
   */
  public function getDatacenter()
  {
    return $this->datacenter;
  }
  /**
   * The name of the vCenter datastore for the admin cluster.
   *
   * @param string $datastore
   */
  public function setDatastore($datastore)
  {
    $this->datastore = $datastore;
  }
  /**
   * @return string
   */
  public function getDatastore()
  {
    return $this->datastore;
  }
  /**
   * The name of the vCenter folder for the admin cluster.
   *
   * @param string $folder
   */
  public function setFolder($folder)
  {
    $this->folder = $folder;
  }
  /**
   * @return string
   */
  public function getFolder()
  {
    return $this->folder;
  }
  /**
   * The name of the vCenter resource pool for the admin cluster.
   *
   * @param string $resourcePool
   */
  public function setResourcePool($resourcePool)
  {
    $this->resourcePool = $resourcePool;
  }
  /**
   * @return string
   */
  public function getResourcePool()
  {
    return $this->resourcePool;
  }
  /**
   * The name of the vCenter storage policy for the user cluster.
   *
   * @param string $storagePolicyName
   */
  public function setStoragePolicyName($storagePolicyName)
  {
    $this->storagePolicyName = $storagePolicyName;
  }
  /**
   * @return string
   */
  public function getStoragePolicyName()
  {
    return $this->storagePolicyName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareAdminVCenterConfig::class, 'Google_Service_GKEOnPrem_VmwareAdminVCenterConfig');
