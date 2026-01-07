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

class GooglePrivacyDlpV2KMapEstimationQuasiIdValues extends \Google\Collection
{
  protected $collection_key = 'quasiIdsValues';
  /**
   * The estimated anonymity for these quasi-identifier values.
   *
   * @var string
   */
  public $estimatedAnonymity;
  protected $quasiIdsValuesType = GooglePrivacyDlpV2Value::class;
  protected $quasiIdsValuesDataType = 'array';

  /**
   * The estimated anonymity for these quasi-identifier values.
   *
   * @param string $estimatedAnonymity
   */
  public function setEstimatedAnonymity($estimatedAnonymity)
  {
    $this->estimatedAnonymity = $estimatedAnonymity;
  }
  /**
   * @return string
   */
  public function getEstimatedAnonymity()
  {
    return $this->estimatedAnonymity;
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
class_alias(GooglePrivacyDlpV2KMapEstimationQuasiIdValues::class, 'Google_Service_DLP_GooglePrivacyDlpV2KMapEstimationQuasiIdValues');
