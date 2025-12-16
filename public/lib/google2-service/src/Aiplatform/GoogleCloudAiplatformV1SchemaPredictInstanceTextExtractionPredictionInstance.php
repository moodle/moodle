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

class GoogleCloudAiplatformV1SchemaPredictInstanceTextExtractionPredictionInstance extends \Google\Model
{
  /**
   * The text snippet to make the predictions on.
   *
   * @var string
   */
  public $content;
  /**
   * This field is only used for batch prediction. If a key is provided, the
   * batch prediction result will by mapped to this key. If omitted, then the
   * batch prediction result will contain the entire input instance. Vertex AI
   * will not check if keys in the request are duplicates, so it is up to the
   * caller to ensure the keys are unique.
   *
   * @var string
   */
  public $key;
  /**
   * The MIME type of the text snippet. The supported MIME types are listed
   * below. - text/plain
   *
   * @var string
   */
  public $mimeType;

  /**
   * The text snippet to make the predictions on.
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * This field is only used for batch prediction. If a key is provided, the
   * batch prediction result will by mapped to this key. If omitted, then the
   * batch prediction result will contain the entire input instance. Vertex AI
   * will not check if keys in the request are duplicates, so it is up to the
   * caller to ensure the keys are unique.
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * The MIME type of the text snippet. The supported MIME types are listed
   * below. - text/plain
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaPredictInstanceTextExtractionPredictionInstance::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaPredictInstanceTextExtractionPredictionInstance');
