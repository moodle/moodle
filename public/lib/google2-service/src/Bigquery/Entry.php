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

namespace Google\Service\Bigquery;

class Entry extends \Google\Model
{
  /**
   * Number of items being predicted as this label.
   *
   * @var string
   */
  public $itemCount;
  /**
   * The predicted label. For confidence_threshold > 0, we will also add an
   * entry indicating the number of items under the confidence threshold.
   *
   * @var string
   */
  public $predictedLabel;

  /**
   * Number of items being predicted as this label.
   *
   * @param string $itemCount
   */
  public function setItemCount($itemCount)
  {
    $this->itemCount = $itemCount;
  }
  /**
   * @return string
   */
  public function getItemCount()
  {
    return $this->itemCount;
  }
  /**
   * The predicted label. For confidence_threshold > 0, we will also add an
   * entry indicating the number of items under the confidence threshold.
   *
   * @param string $predictedLabel
   */
  public function setPredictedLabel($predictedLabel)
  {
    $this->predictedLabel = $predictedLabel;
  }
  /**
   * @return string
   */
  public function getPredictedLabel()
  {
    return $this->predictedLabel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Entry::class, 'Google_Service_Bigquery_Entry');
