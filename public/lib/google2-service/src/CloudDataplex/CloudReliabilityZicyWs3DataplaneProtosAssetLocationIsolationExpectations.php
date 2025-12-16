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

namespace Google\Service\CloudDataplex;

class CloudReliabilityZicyWs3DataplaneProtosAssetLocationIsolationExpectations extends \Google\Model
{
  protected $requirementOverrideType = CloudReliabilityZicyWs3DataplaneProtosAssetLocationIsolationExpectationsRequirementOverride::class;
  protected $requirementOverrideDataType = '';
  /**
   * @var string
   */
  public $ziOrgPolicy;
  /**
   * @var string
   */
  public $ziRegionPolicy;
  /**
   * @var string
   */
  public $ziRegionState;
  /**
   * @var string
   */
  public $zoneIsolation;
  /**
   * @var string
   */
  public $zoneSeparation;
  /**
   * @var string
   */
  public $zsOrgPolicy;
  /**
   * @var string
   */
  public $zsRegionState;

  /**
   * @param CloudReliabilityZicyWs3DataplaneProtosAssetLocationIsolationExpectationsRequirementOverride
   */
  public function setRequirementOverride(CloudReliabilityZicyWs3DataplaneProtosAssetLocationIsolationExpectationsRequirementOverride $requirementOverride)
  {
    $this->requirementOverride = $requirementOverride;
  }
  /**
   * @return CloudReliabilityZicyWs3DataplaneProtosAssetLocationIsolationExpectationsRequirementOverride
   */
  public function getRequirementOverride()
  {
    return $this->requirementOverride;
  }
  /**
   * @param string
   */
  public function setZiOrgPolicy($ziOrgPolicy)
  {
    $this->ziOrgPolicy = $ziOrgPolicy;
  }
  /**
   * @return string
   */
  public function getZiOrgPolicy()
  {
    return $this->ziOrgPolicy;
  }
  /**
   * @param string
   */
  public function setZiRegionPolicy($ziRegionPolicy)
  {
    $this->ziRegionPolicy = $ziRegionPolicy;
  }
  /**
   * @return string
   */
  public function getZiRegionPolicy()
  {
    return $this->ziRegionPolicy;
  }
  /**
   * @param string
   */
  public function setZiRegionState($ziRegionState)
  {
    $this->ziRegionState = $ziRegionState;
  }
  /**
   * @return string
   */
  public function getZiRegionState()
  {
    return $this->ziRegionState;
  }
  /**
   * @param string
   */
  public function setZoneIsolation($zoneIsolation)
  {
    $this->zoneIsolation = $zoneIsolation;
  }
  /**
   * @return string
   */
  public function getZoneIsolation()
  {
    return $this->zoneIsolation;
  }
  /**
   * @param string
   */
  public function setZoneSeparation($zoneSeparation)
  {
    $this->zoneSeparation = $zoneSeparation;
  }
  /**
   * @return string
   */
  public function getZoneSeparation()
  {
    return $this->zoneSeparation;
  }
  /**
   * @param string
   */
  public function setZsOrgPolicy($zsOrgPolicy)
  {
    $this->zsOrgPolicy = $zsOrgPolicy;
  }
  /**
   * @return string
   */
  public function getZsOrgPolicy()
  {
    return $this->zsOrgPolicy;
  }
  /**
   * @param string
   */
  public function setZsRegionState($zsRegionState)
  {
    $this->zsRegionState = $zsRegionState;
  }
  /**
   * @return string
   */
  public function getZsRegionState()
  {
    return $this->zsRegionState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudReliabilityZicyWs3DataplaneProtosAssetLocationIsolationExpectations::class, 'Google_Service_CloudDataplex_CloudReliabilityZicyWs3DataplaneProtosAssetLocationIsolationExpectations');
