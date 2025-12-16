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

class GoogleCloudAiplatformV1SchemaPredictInstanceTextSentimentPredictionInstance extends \Google\Model
{
  /**
   * The text snippet to make the predictions on.
   *
   * @var string
   */
  public $content;
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
class_alias(GoogleCloudAiplatformV1SchemaPredictInstanceTextSentimentPredictionInstance::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaPredictInstanceTextSentimentPredictionInstance');
