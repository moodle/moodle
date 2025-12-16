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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1ModelEvaluationSliceSliceSliceSpecSliceConfig extends \Google\Model
{
  /**
   * If all_values is set to true, then all possible labels of the keyed feature
   * will have another slice computed. Example: `{"all_values":{"value":true}}`
   *
   * @var bool
   */
  public $allValues;
  protected $rangeType = GoogleCloudAiplatformV1ModelEvaluationSliceSliceSliceSpecRange::class;
  protected $rangeDataType = '';
  protected $valueType = GoogleCloudAiplatformV1ModelEvaluationSliceSliceSliceSpecValue::class;
  protected $valueDataType = '';

  /**
   * If all_values is set to true, then all possible labels of the keyed feature
   * will have another slice computed. Example: `{"all_values":{"value":true}}`
   *
   * @param bool $allValues
   */
  public function setAllValues($allValues)
  {
    $this->allValues = $allValues;
  }
  /**
   * @return bool
   */
  public function getAllValues()
  {
    return $this->allValues;
  }
  /**
   * A range of values for a numerical feature. Example:
   * `{"range":{"low":10000.0,"high":50000.0}}` will capture 12345 and 23334 in
   * the slice.
   *
   * @param GoogleCloudAiplatformV1ModelEvaluationSliceSliceSliceSpecRange $range
   */
  public function setRange(GoogleCloudAiplatformV1ModelEvaluationSliceSliceSliceSpecRange $range)
  {
    $this->range = $range;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelEvaluationSliceSliceSliceSpecRange
   */
  public function getRange()
  {
    return $this->range;
  }
  /**
   * A unique specific value for a given feature. Example: `{ "value": {
   * "string_value": "12345" } }`
   *
   * @param GoogleCloudAiplatformV1ModelEvaluationSliceSliceSliceSpecValue $value
   */
  public function setValue(GoogleCloudAiplatformV1ModelEvaluationSliceSliceSliceSpecValue $value)
  {
    $this->value = $value;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelEvaluationSliceSliceSliceSpecValue
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ModelEvaluationSliceSliceSliceSpecSliceConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ModelEvaluationSliceSliceSliceSpecSliceConfig');
