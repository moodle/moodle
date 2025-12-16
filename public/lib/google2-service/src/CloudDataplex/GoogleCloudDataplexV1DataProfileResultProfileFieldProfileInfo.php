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

class GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfo extends \Google\Collection
{
  protected $collection_key = 'topNValues';
  /**
   * Output only. Ratio of rows with distinct values against total scanned rows.
   * Not available for complex non-groupable field type, including RECORD,
   * ARRAY, GEOGRAPHY, and JSON, as well as fields with REPEATABLE mode.
   *
   * @var 
   */
  public $distinctRatio;
  protected $doubleProfileType = GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfoDoubleFieldInfo::class;
  protected $doubleProfileDataType = '';
  protected $integerProfileType = GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfoIntegerFieldInfo::class;
  protected $integerProfileDataType = '';
  /**
   * Output only. Ratio of rows with null value against total scanned rows.
   *
   * @var 
   */
  public $nullRatio;
  protected $stringProfileType = GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfoStringFieldInfo::class;
  protected $stringProfileDataType = '';
  protected $topNValuesType = GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfoTopNValue::class;
  protected $topNValuesDataType = 'array';

  public function setDistinctRatio($distinctRatio)
  {
    $this->distinctRatio = $distinctRatio;
  }
  public function getDistinctRatio()
  {
    return $this->distinctRatio;
  }
  /**
   * Double type field information.
   *
   * @param GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfoDoubleFieldInfo $doubleProfile
   */
  public function setDoubleProfile(GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfoDoubleFieldInfo $doubleProfile)
  {
    $this->doubleProfile = $doubleProfile;
  }
  /**
   * @return GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfoDoubleFieldInfo
   */
  public function getDoubleProfile()
  {
    return $this->doubleProfile;
  }
  /**
   * Integer type field information.
   *
   * @param GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfoIntegerFieldInfo $integerProfile
   */
  public function setIntegerProfile(GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfoIntegerFieldInfo $integerProfile)
  {
    $this->integerProfile = $integerProfile;
  }
  /**
   * @return GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfoIntegerFieldInfo
   */
  public function getIntegerProfile()
  {
    return $this->integerProfile;
  }
  public function setNullRatio($nullRatio)
  {
    $this->nullRatio = $nullRatio;
  }
  public function getNullRatio()
  {
    return $this->nullRatio;
  }
  /**
   * String type field information.
   *
   * @param GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfoStringFieldInfo $stringProfile
   */
  public function setStringProfile(GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfoStringFieldInfo $stringProfile)
  {
    $this->stringProfile = $stringProfile;
  }
  /**
   * @return GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfoStringFieldInfo
   */
  public function getStringProfile()
  {
    return $this->stringProfile;
  }
  /**
   * Output only. The list of top N non-null values, frequency and ratio with
   * which they occur in the scanned data. N is 10 or equal to the number of
   * distinct values in the field, whichever is smaller. Not available for
   * complex non-groupable field type, including RECORD, ARRAY, GEOGRAPHY, and
   * JSON, as well as fields with REPEATABLE mode.
   *
   * @param GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfoTopNValue[] $topNValues
   */
  public function setTopNValues($topNValues)
  {
    $this->topNValues = $topNValues;
  }
  /**
   * @return GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfoTopNValue[]
   */
  public function getTopNValues()
  {
    return $this->topNValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfo::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfo');
