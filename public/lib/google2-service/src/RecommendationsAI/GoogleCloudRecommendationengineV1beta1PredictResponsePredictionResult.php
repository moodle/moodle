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

namespace Google\Service\RecommendationsAI;

class GoogleCloudRecommendationengineV1beta1PredictResponsePredictionResult extends \Google\Model
{
  /**
   * ID of the recommended catalog item
   *
   * @var string
   */
  public $id;
  /**
   * Additional item metadata / annotations. Possible values: * `catalogItem`:
   * JSON representation of the catalogItem. Will be set if `returnCatalogItem`
   * is set to true in `PredictRequest.params`. * `score`: Prediction score in
   * double value. Will be set if `returnItemScore` is set to true in
   * `PredictRequest.params`.
   *
   * @var array[]
   */
  public $itemMetadata;

  /**
   * ID of the recommended catalog item
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Additional item metadata / annotations. Possible values: * `catalogItem`:
   * JSON representation of the catalogItem. Will be set if `returnCatalogItem`
   * is set to true in `PredictRequest.params`. * `score`: Prediction score in
   * double value. Will be set if `returnItemScore` is set to true in
   * `PredictRequest.params`.
   *
   * @param array[] $itemMetadata
   */
  public function setItemMetadata($itemMetadata)
  {
    $this->itemMetadata = $itemMetadata;
  }
  /**
   * @return array[]
   */
  public function getItemMetadata()
  {
    return $this->itemMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecommendationengineV1beta1PredictResponsePredictionResult::class, 'Google_Service_RecommendationsAI_GoogleCloudRecommendationengineV1beta1PredictResponsePredictionResult');
