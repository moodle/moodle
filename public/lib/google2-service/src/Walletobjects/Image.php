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

namespace Google\Service\Walletobjects;

class Image extends \Google\Model
{
  protected $contentDescriptionType = LocalizedString::class;
  protected $contentDescriptionDataType = '';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#image"`.
   *
   * @deprecated
   * @var string
   */
  public $kind;
  /**
   * An ID for an already uploaded private image. Either this or source_uri
   * should be set. Requests setting both or neither will be rejected. Please
   * contact support to use private images.
   *
   * @var string
   */
  public $privateImageId;
  protected $sourceUriType = ImageUri::class;
  protected $sourceUriDataType = '';

  /**
   * Description of the image used for accessibility.
   *
   * @param LocalizedString $contentDescription
   */
  public function setContentDescription(LocalizedString $contentDescription)
  {
    $this->contentDescription = $contentDescription;
  }
  /**
   * @return LocalizedString
   */
  public function getContentDescription()
  {
    return $this->contentDescription;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#image"`.
   *
   * @deprecated
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * An ID for an already uploaded private image. Either this or source_uri
   * should be set. Requests setting both or neither will be rejected. Please
   * contact support to use private images.
   *
   * @param string $privateImageId
   */
  public function setPrivateImageId($privateImageId)
  {
    $this->privateImageId = $privateImageId;
  }
  /**
   * @return string
   */
  public function getPrivateImageId()
  {
    return $this->privateImageId;
  }
  /**
   * A URI for the image. Either this or private_image_id should be set.
   * Requests setting both or neither will be rejected.
   *
   * @param ImageUri $sourceUri
   */
  public function setSourceUri(ImageUri $sourceUri)
  {
    $this->sourceUri = $sourceUri;
  }
  /**
   * @return ImageUri
   */
  public function getSourceUri()
  {
    return $this->sourceUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Image::class, 'Google_Service_Walletobjects_Image');
