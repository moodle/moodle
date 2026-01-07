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

class CloudAiLargeModelsVisionGenerateVideoResponseVideo extends \Google\Model
{
  /**
   * Base64 encoded bytes string representing the video.
   *
   * @var string
   */
  public $bytesBase64Encoded;
  /**
   * Cloud Storage URI where the generated video is written.
   *
   * @var string
   */
  public $gcsUri;
  /**
   * The MIME type of the content of the video. - video/mp4
   *
   * @var string
   */
  public $mimeType;

  /**
   * Base64 encoded bytes string representing the video.
   *
   * @param string $bytesBase64Encoded
   */
  public function setBytesBase64Encoded($bytesBase64Encoded)
  {
    $this->bytesBase64Encoded = $bytesBase64Encoded;
  }
  /**
   * @return string
   */
  public function getBytesBase64Encoded()
  {
    return $this->bytesBase64Encoded;
  }
  /**
   * Cloud Storage URI where the generated video is written.
   *
   * @param string $gcsUri
   */
  public function setGcsUri($gcsUri)
  {
    $this->gcsUri = $gcsUri;
  }
  /**
   * @return string
   */
  public function getGcsUri()
  {
    return $this->gcsUri;
  }
  /**
   * The MIME type of the content of the video. - video/mp4
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
class_alias(CloudAiLargeModelsVisionGenerateVideoResponseVideo::class, 'Google_Service_Aiplatform_CloudAiLargeModelsVisionGenerateVideoResponseVideo');
