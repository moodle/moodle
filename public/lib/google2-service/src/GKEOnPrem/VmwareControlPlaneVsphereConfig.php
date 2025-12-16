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

class VmwareControlPlaneVsphereConfig extends \Google\Model
{
  /**
   * The Vsphere datastore used by the control plane Node.
   *
   * @var string
   */
  public $datastore;
  /**
   * The Vsphere storage policy used by the control plane Node.
   *
   * @var string
   */
  public $storagePolicyName;

  /**
   * The Vsphere datastore used by the control plane Node.
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
   * The Vsphere storage policy used by the control plane Node.
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
class_alias(VmwareControlPlaneVsphereConfig::class, 'Google_Service_GKEOnPrem_VmwareControlPlaneVsphereConfig');
