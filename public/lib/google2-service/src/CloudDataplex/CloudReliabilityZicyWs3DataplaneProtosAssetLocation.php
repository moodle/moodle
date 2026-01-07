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

class CloudReliabilityZicyWs3DataplaneProtosAssetLocation extends \Google\Collection
{
  protected $collection_key = 'parentAsset';
  /**
   * @var string
   */
  public $ccfeRmsPath;
  protected $expectedType = CloudReliabilityZicyWs3DataplaneProtosAssetLocationIsolationExpectations::class;
  protected $expectedDataType = '';
  protected $extraParametersType = CloudReliabilityZicyWs3DataplaneProtosExtraParameter::class;
  protected $extraParametersDataType = 'array';
  protected $locationDataType = CloudReliabilityZicyWs3DataplaneProtosLocationData::class;
  protected $locationDataDataType = 'array';
  protected $parentAssetType = CloudReliabilityZicyWs3DataplaneProtosCloudAsset::class;
  protected $parentAssetDataType = 'array';

  /**
   * @param string
   */
  public function setCcfeRmsPath($ccfeRmsPath)
  {
    $this->ccfeRmsPath = $ccfeRmsPath;
  }
  /**
   * @return string
   */
  public function getCcfeRmsPath()
  {
    return $this->ccfeRmsPath;
  }
  /**
   * @param CloudReliabilityZicyWs3DataplaneProtosAssetLocationIsolationExpectations
   */
  public function setExpected(CloudReliabilityZicyWs3DataplaneProtosAssetLocationIsolationExpectations $expected)
  {
    $this->expected = $expected;
  }
  /**
   * @return CloudReliabilityZicyWs3DataplaneProtosAssetLocationIsolationExpectations
   */
  public function getExpected()
  {
    return $this->expected;
  }
  /**
   * @param CloudReliabilityZicyWs3DataplaneProtosExtraParameter[]
   */
  public function setExtraParameters($extraParameters)
  {
    $this->extraParameters = $extraParameters;
  }
  /**
   * @return CloudReliabilityZicyWs3DataplaneProtosExtraParameter[]
   */
  public function getExtraParameters()
  {
    return $this->extraParameters;
  }
  /**
   * @param CloudReliabilityZicyWs3DataplaneProtosLocationData[]
   */
  public function setLocationData($locationData)
  {
    $this->locationData = $locationData;
  }
  /**
   * @return CloudReliabilityZicyWs3DataplaneProtosLocationData[]
   */
  public function getLocationData()
  {
    return $this->locationData;
  }
  /**
   * @param CloudReliabilityZicyWs3DataplaneProtosCloudAsset[]
   */
  public function setParentAsset($parentAsset)
  {
    $this->parentAsset = $parentAsset;
  }
  /**
   * @return CloudReliabilityZicyWs3DataplaneProtosCloudAsset[]
   */
  public function getParentAsset()
  {
    return $this->parentAsset;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudReliabilityZicyWs3DataplaneProtosAssetLocation::class, 'Google_Service_CloudDataplex_CloudReliabilityZicyWs3DataplaneProtosAssetLocation');
