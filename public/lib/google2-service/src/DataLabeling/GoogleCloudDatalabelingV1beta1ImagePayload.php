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

namespace Google\Service\DataLabeling;

class GoogleCloudDatalabelingV1beta1ImagePayload extends \Google\Model
{
  /**
   * A byte string of a thumbnail image.
   *
   * @var string
   */
  public $imageThumbnail;
  /**
   * Image uri from the user bucket.
   *
   * @var string
   */
  public $imageUri;
  /**
   * Image format.
   *
   * @var string
   */
  public $mimeType;
  /**
   * Signed uri of the image file in the service bucket.
   *
   * @var string
   */
  public $signedUri;

  /**
   * A byte string of a thumbnail image.
   *
   * @param string $imageThumbnail
   */
  public function setImageThumbnail($imageThumbnail)
  {
    $this->imageThumbnail = $imageThumbnail;
  }
  /**
   * @return string
   */
  public function getImageThumbnail()
  {
    return $this->imageThumbnail;
  }
  /**
   * Image uri from the user bucket.
   *
   * @param string $imageUri
   */
  public function setImageUri($imageUri)
  {
    $this->imageUri = $imageUri;
  }
  /**
   * @return string
   */
  public function getImageUri()
  {
    return $this->imageUri;
  }
  /**
   * Image format.
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
  /**
   * Signed uri of the image file in the service bucket.
   *
   * @param string $signedUri
   */
  public function setSignedUri($signedUri)
  {
    $this->signedUri = $signedUri;
  }
  /**
   * @return string
   */
  public function getSignedUri()
  {
    return $this->signedUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatalabelingV1beta1ImagePayload::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1ImagePayload');
