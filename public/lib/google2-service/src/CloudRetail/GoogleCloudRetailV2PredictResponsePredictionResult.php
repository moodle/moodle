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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2PredictResponsePredictionResult extends \Google\Model
{
  /**
   * ID of the recommended product
   *
   * @var string
   */
  public $id;
  /**
   * Additional product metadata / annotations. Possible values: * `product`:
   * JSON representation of the product. Is set if `returnProduct` is set to
   * true in `PredictRequest.params`. * `score`: Prediction score in double
   * value. Is set if `returnScore` is set to true in `PredictRequest.params`.
   *
   * @var array[]
   */
  public $metadata;

  /**
   * ID of the recommended product
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
   * Additional product metadata / annotations. Possible values: * `product`:
   * JSON representation of the product. Is set if `returnProduct` is set to
   * true in `PredictRequest.params`. * `score`: Prediction score in double
   * value. Is set if `returnScore` is set to true in `PredictRequest.params`.
   *
   * @param array[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2PredictResponsePredictionResult::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2PredictResponsePredictionResult');
