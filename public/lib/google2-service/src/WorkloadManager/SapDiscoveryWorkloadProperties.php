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

namespace Google\Service\WorkloadManager;

class SapDiscoveryWorkloadProperties extends \Google\Collection
{
  protected $collection_key = 'softwareComponentVersions';
  protected $productVersionsType = SapDiscoveryWorkloadPropertiesProductVersion::class;
  protected $productVersionsDataType = 'array';
  protected $softwareComponentVersionsType = SapDiscoveryWorkloadPropertiesSoftwareComponentProperties::class;
  protected $softwareComponentVersionsDataType = 'array';

  /**
   * Optional. List of SAP Products and their versions running on the system.
   *
   * @param SapDiscoveryWorkloadPropertiesProductVersion[] $productVersions
   */
  public function setProductVersions($productVersions)
  {
    $this->productVersions = $productVersions;
  }
  /**
   * @return SapDiscoveryWorkloadPropertiesProductVersion[]
   */
  public function getProductVersions()
  {
    return $this->productVersions;
  }
  /**
   * Optional. A list of SAP software components and their versions running on
   * the system.
   *
   * @param SapDiscoveryWorkloadPropertiesSoftwareComponentProperties[] $softwareComponentVersions
   */
  public function setSoftwareComponentVersions($softwareComponentVersions)
  {
    $this->softwareComponentVersions = $softwareComponentVersions;
  }
  /**
   * @return SapDiscoveryWorkloadPropertiesSoftwareComponentProperties[]
   */
  public function getSoftwareComponentVersions()
  {
    return $this->softwareComponentVersions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SapDiscoveryWorkloadProperties::class, 'Google_Service_WorkloadManager_SapDiscoveryWorkloadProperties');
