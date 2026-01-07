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

namespace Google\Service\DataFusion;

class AssetLocation extends \Google\Collection
{
  protected $collection_key = 'parentAsset';
  protected $expectedType = IsolationExpectations::class;
  protected $expectedDataType = '';
  protected $extraParametersType = ExtraParameter::class;
  protected $extraParametersDataType = 'array';
  protected $locationDataType = LocationData::class;
  protected $locationDataDataType = 'array';
  protected $parentAssetType = CloudAsset::class;
  protected $parentAssetDataType = 'array';

  /**
   * @param IsolationExpectations
   */
  public function setExpected(IsolationExpectations $expected)
  {
    $this->expected = $expected;
  }
  /**
   * @return IsolationExpectations
   */
  public function getExpected()
  {
    return $this->expected;
  }
  /**
   * @param ExtraParameter[]
   */
  public function setExtraParameters($extraParameters)
  {
    $this->extraParameters = $extraParameters;
  }
  /**
   * @return ExtraParameter[]
   */
  public function getExtraParameters()
  {
    return $this->extraParameters;
  }
  /**
   * @param LocationData[]
   */
  public function setLocationData($locationData)
  {
    $this->locationData = $locationData;
  }
  /**
   * @return LocationData[]
   */
  public function getLocationData()
  {
    return $this->locationData;
  }
  /**
   * @param CloudAsset[]
   */
  public function setParentAsset($parentAsset)
  {
    $this->parentAsset = $parentAsset;
  }
  /**
   * @return CloudAsset[]
   */
  public function getParentAsset()
  {
    return $this->parentAsset;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AssetLocation::class, 'Google_Service_DataFusion_AssetLocation');
