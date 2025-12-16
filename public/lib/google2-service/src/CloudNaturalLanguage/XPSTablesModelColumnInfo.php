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

namespace Google\Service\CloudNaturalLanguage;

class XPSTablesModelColumnInfo extends \Google\Model
{
  /**
   * The ID of the column.
   *
   * @var int
   */
  public $columnId;
  /**
   * When given as part of a Model: Measurement of how much model predictions
   * correctness on the TEST data depend on values in this column. A value
   * between 0 and 1, higher means higher influence. These values are normalized
   * - for all input feature columns of a given model they add to 1. When given
   * back by Predict or Batch Predict: Measurement of how impactful for the
   * prediction returned for the given row the value in this column was.
   * Specifically, the feature importance specifies the marginal contribution
   * that the feature made to the prediction score compared to the baseline
   * score. These values are computed using the Sampled Shapley method.
   *
   * @var float
   */
  public $featureImportance;

  /**
   * The ID of the column.
   *
   * @param int $columnId
   */
  public function setColumnId($columnId)
  {
    $this->columnId = $columnId;
  }
  /**
   * @return int
   */
  public function getColumnId()
  {
    return $this->columnId;
  }
  /**
   * When given as part of a Model: Measurement of how much model predictions
   * correctness on the TEST data depend on values in this column. A value
   * between 0 and 1, higher means higher influence. These values are normalized
   * - for all input feature columns of a given model they add to 1. When given
   * back by Predict or Batch Predict: Measurement of how impactful for the
   * prediction returned for the given row the value in this column was.
   * Specifically, the feature importance specifies the marginal contribution
   * that the feature made to the prediction score compared to the baseline
   * score. These values are computed using the Sampled Shapley method.
   *
   * @param float $featureImportance
   */
  public function setFeatureImportance($featureImportance)
  {
    $this->featureImportance = $featureImportance;
  }
  /**
   * @return float
   */
  public function getFeatureImportance()
  {
    return $this->featureImportance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSTablesModelColumnInfo::class, 'Google_Service_CloudNaturalLanguage_XPSTablesModelColumnInfo');
