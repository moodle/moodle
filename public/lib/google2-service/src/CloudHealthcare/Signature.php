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

namespace Google\Service\CloudHealthcare;

class Signature extends \Google\Model
{
  protected $imageType = Image::class;
  protected $imageDataType = '';
  /**
   * Optional. Metadata associated with the user's signature. For example, the
   * user's name or the user's title.
   *
   * @var string[]
   */
  public $metadata;
  /**
   * Optional. Timestamp of the signature.
   *
   * @var string
   */
  public $signatureTime;
  /**
   * Required. User's UUID provided by the client.
   *
   * @var string
   */
  public $userId;

  /**
   * Optional. An image of the user's signature.
   *
   * @param Image $image
   */
  public function setImage(Image $image)
  {
    $this->image = $image;
  }
  /**
   * @return Image
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * Optional. Metadata associated with the user's signature. For example, the
   * user's name or the user's title.
   *
   * @param string[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return string[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Optional. Timestamp of the signature.
   *
   * @param string $signatureTime
   */
  public function setSignatureTime($signatureTime)
  {
    $this->signatureTime = $signatureTime;
  }
  /**
   * @return string
   */
  public function getSignatureTime()
  {
    return $this->signatureTime;
  }
  /**
   * Required. User's UUID provided by the client.
   *
   * @param string $userId
   */
  public function setUserId($userId)
  {
    $this->userId = $userId;
  }
  /**
   * @return string
   */
  public function getUserId()
  {
    return $this->userId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Signature::class, 'Google_Service_CloudHealthcare_Signature');
