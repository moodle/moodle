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

class GoogleCloudAiplatformV1ModalityTokenCount extends \Google\Model
{
  /**
   * When a modality is not specified, it is treated as `TEXT`.
   */
  public const MODALITY_MODALITY_UNSPECIFIED = 'MODALITY_UNSPECIFIED';
  /**
   * The `Part` contains plain text.
   */
  public const MODALITY_TEXT = 'TEXT';
  /**
   * The `Part` contains an image.
   */
  public const MODALITY_IMAGE = 'IMAGE';
  /**
   * The `Part` contains a video.
   */
  public const MODALITY_VIDEO = 'VIDEO';
  /**
   * The `Part` contains audio.
   */
  public const MODALITY_AUDIO = 'AUDIO';
  /**
   * The `Part` contains a document, such as a PDF.
   */
  public const MODALITY_DOCUMENT = 'DOCUMENT';
  /**
   * The modality that this token count applies to.
   *
   * @var string
   */
  public $modality;
  /**
   * The number of tokens counted for this modality.
   *
   * @var int
   */
  public $tokenCount;

  /**
   * The modality that this token count applies to.
   *
   * Accepted values: MODALITY_UNSPECIFIED, TEXT, IMAGE, VIDEO, AUDIO, DOCUMENT
   *
   * @param self::MODALITY_* $modality
   */
  public function setModality($modality)
  {
    $this->modality = $modality;
  }
  /**
   * @return self::MODALITY_*
   */
  public function getModality()
  {
    return $this->modality;
  }
  /**
   * The number of tokens counted for this modality.
   *
   * @param int $tokenCount
   */
  public function setTokenCount($tokenCount)
  {
    $this->tokenCount = $tokenCount;
  }
  /**
   * @return int
   */
  public function getTokenCount()
  {
    return $this->tokenCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ModalityTokenCount::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ModalityTokenCount');
