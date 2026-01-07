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

namespace Google\Service\Forms;

class Image extends \Google\Model
{
  /**
   * A description of the image that is shown on hover and read by
   * screenreaders.
   *
   * @var string
   */
  public $altText;
  /**
   * Output only. A URI from which you can download the image; this is valid
   * only for a limited time.
   *
   * @var string
   */
  public $contentUri;
  protected $propertiesType = MediaProperties::class;
  protected $propertiesDataType = '';
  /**
   * Input only. The source URI is the URI used to insert the image. The source
   * URI can be empty when fetched.
   *
   * @var string
   */
  public $sourceUri;

  /**
   * A description of the image that is shown on hover and read by
   * screenreaders.
   *
   * @param string $altText
   */
  public function setAltText($altText)
  {
    $this->altText = $altText;
  }
  /**
   * @return string
   */
  public function getAltText()
  {
    return $this->altText;
  }
  /**
   * Output only. A URI from which you can download the image; this is valid
   * only for a limited time.
   *
   * @param string $contentUri
   */
  public function setContentUri($contentUri)
  {
    $this->contentUri = $contentUri;
  }
  /**
   * @return string
   */
  public function getContentUri()
  {
    return $this->contentUri;
  }
  /**
   * Properties of an image.
   *
   * @param MediaProperties $properties
   */
  public function setProperties(MediaProperties $properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return MediaProperties
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * Input only. The source URI is the URI used to insert the image. The source
   * URI can be empty when fetched.
   *
   * @param string $sourceUri
   */
  public function setSourceUri($sourceUri)
  {
    $this->sourceUri = $sourceUri;
  }
  /**
   * @return string
   */
  public function getSourceUri()
  {
    return $this->sourceUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Image::class, 'Google_Service_Forms_Image');
