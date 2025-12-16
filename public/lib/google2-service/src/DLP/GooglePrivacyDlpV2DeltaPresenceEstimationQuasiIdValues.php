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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2DeltaPresenceEstimationQuasiIdValues extends \Google\Collection
{
  protected $collection_key = 'quasiIdsValues';
  /**
   * The estimated probability that a given individual sharing these quasi-
   * identifier values is in the dataset. This value, typically called δ, is the
   * ratio between the number of records in the dataset with these quasi-
   * identifier values, and the total number of individuals (inside *and*
   * outside the dataset) with these quasi-identifier values. For example, if
   * there are 15 individuals in the dataset who share the same quasi-identifier
   * values, and an estimated 100 people in the entire population with these
   * values, then δ is 0.15.
   *
   * @var 
   */
  public $estimatedProbability;
  protected $quasiIdsValuesType = GooglePrivacyDlpV2Value::class;
  protected $quasiIdsValuesDataType = 'array';

  public function setEstimatedProbability($estimatedProbability)
  {
    $this->estimatedProbability = $estimatedProbability;
  }
  public function getEstimatedProbability()
  {
    return $this->estimatedProbability;
  }
  /**
   * The quasi-identifier values.
   *
   * @param GooglePrivacyDlpV2Value[] $quasiIdsValues
   */
  public function setQuasiIdsValues($quasiIdsValues)
  {
    $this->quasiIdsValues = $quasiIdsValues;
  }
  /**
   * @return GooglePrivacyDlpV2Value[]
   */
  public function getQuasiIdsValues()
  {
    return $this->quasiIdsValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DeltaPresenceEstimationQuasiIdValues::class, 'Google_Service_DLP_GooglePrivacyDlpV2DeltaPresenceEstimationQuasiIdValues');
