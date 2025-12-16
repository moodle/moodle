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

class GoogleCloudAiplatformV1ExportFilterSplit extends \Google\Model
{
  /**
   * Required. A filter on DataItems of the Dataset. DataItems that match this
   * filter are used to test the Model. A filter with same syntax as the one
   * used in DatasetService.ListDataItems may be used. If a single DataItem is
   * matched by more than one of the FilterSplit filters, then it is assigned to
   * the first set that applies to it in the training, validation, test order.
   *
   * @var string
   */
  public $testFilter;
  /**
   * Required. A filter on DataItems of the Dataset. DataItems that match this
   * filter are used to train the Model. A filter with same syntax as the one
   * used in DatasetService.ListDataItems may be used. If a single DataItem is
   * matched by more than one of the FilterSplit filters, then it is assigned to
   * the first set that applies to it in the training, validation, test order.
   *
   * @var string
   */
  public $trainingFilter;
  /**
   * Required. A filter on DataItems of the Dataset. DataItems that match this
   * filter are used to validate the Model. A filter with same syntax as the one
   * used in DatasetService.ListDataItems may be used. If a single DataItem is
   * matched by more than one of the FilterSplit filters, then it is assigned to
   * the first set that applies to it in the training, validation, test order.
   *
   * @var string
   */
  public $validationFilter;

  /**
   * Required. A filter on DataItems of the Dataset. DataItems that match this
   * filter are used to test the Model. A filter with same syntax as the one
   * used in DatasetService.ListDataItems may be used. If a single DataItem is
   * matched by more than one of the FilterSplit filters, then it is assigned to
   * the first set that applies to it in the training, validation, test order.
   *
   * @param string $testFilter
   */
  public function setTestFilter($testFilter)
  {
    $this->testFilter = $testFilter;
  }
  /**
   * @return string
   */
  public function getTestFilter()
  {
    return $this->testFilter;
  }
  /**
   * Required. A filter on DataItems of the Dataset. DataItems that match this
   * filter are used to train the Model. A filter with same syntax as the one
   * used in DatasetService.ListDataItems may be used. If a single DataItem is
   * matched by more than one of the FilterSplit filters, then it is assigned to
   * the first set that applies to it in the training, validation, test order.
   *
   * @param string $trainingFilter
   */
  public function setTrainingFilter($trainingFilter)
  {
    $this->trainingFilter = $trainingFilter;
  }
  /**
   * @return string
   */
  public function getTrainingFilter()
  {
    return $this->trainingFilter;
  }
  /**
   * Required. A filter on DataItems of the Dataset. DataItems that match this
   * filter are used to validate the Model. A filter with same syntax as the one
   * used in DatasetService.ListDataItems may be used. If a single DataItem is
   * matched by more than one of the FilterSplit filters, then it is assigned to
   * the first set that applies to it in the training, validation, test order.
   *
   * @param string $validationFilter
   */
  public function setValidationFilter($validationFilter)
  {
    $this->validationFilter = $validationFilter;
  }
  /**
   * @return string
   */
  public function getValidationFilter()
  {
    return $this->validationFilter;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ExportFilterSplit::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ExportFilterSplit');
