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

namespace Google\Service\Slides;

class Image extends \Google\Model
{
  /**
   * An URL to an image with a default lifetime of 30 minutes. This URL is
   * tagged with the account of the requester. Anyone with the URL effectively
   * accesses the image as the original requester. Access to the image may be
   * lost if the presentation's sharing settings change.
   *
   * @var string
   */
  public $contentUrl;
  protected $imagePropertiesType = ImageProperties::class;
  protected $imagePropertiesDataType = '';
  protected $placeholderType = Placeholder::class;
  protected $placeholderDataType = '';
  /**
   * The source URL is the URL used to insert the image. The source URL can be
   * empty.
   *
   * @var string
   */
  public $sourceUrl;

  /**
   * An URL to an image with a default lifetime of 30 minutes. This URL is
   * tagged with the account of the requester. Anyone with the URL effectively
   * accesses the image as the original requester. Access to the image may be
   * lost if the presentation's sharing settings change.
   *
   * @param string $contentUrl
   */
  public function setContentUrl($contentUrl)
  {
    $this->contentUrl = $contentUrl;
  }
  /**
   * @return string
   */
  public function getContentUrl()
  {
    return $this->contentUrl;
  }
  /**
   * The properties of the image.
   *
   * @param ImageProperties $imageProperties
   */
  public function setImageProperties(ImageProperties $imageProperties)
  {
    $this->imageProperties = $imageProperties;
  }
  /**
   * @return ImageProperties
   */
  public function getImageProperties()
  {
    return $this->imageProperties;
  }
  /**
   * Placeholders are page elements that inherit from corresponding placeholders
   * on layouts and masters. If set, the image is a placeholder image and any
   * inherited properties can be resolved by looking at the parent placeholder
   * identified by the Placeholder.parent_object_id field.
   *
   * @param Placeholder $placeholder
   */
  public function setPlaceholder(Placeholder $placeholder)
  {
    $this->placeholder = $placeholder;
  }
  /**
   * @return Placeholder
   */
  public function getPlaceholder()
  {
    return $this->placeholder;
  }
  /**
   * The source URL is the URL used to insert the image. The source URL can be
   * empty.
   *
   * @param string $sourceUrl
   */
  public function setSourceUrl($sourceUrl)
  {
    $this->sourceUrl = $sourceUrl;
  }
  /**
   * @return string
   */
  public function getSourceUrl()
  {
    return $this->sourceUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Image::class, 'Google_Service_Slides_Image');
