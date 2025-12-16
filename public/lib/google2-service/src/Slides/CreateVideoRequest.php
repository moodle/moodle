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

class CreateVideoRequest extends \Google\Model
{
  /**
   * The video source is unspecified.
   */
  public const SOURCE_SOURCE_UNSPECIFIED = 'SOURCE_UNSPECIFIED';
  /**
   * The video source is YouTube.
   */
  public const SOURCE_YOUTUBE = 'YOUTUBE';
  /**
   * The video source is Google Drive.
   */
  public const SOURCE_DRIVE = 'DRIVE';
  protected $elementPropertiesType = PageElementProperties::class;
  protected $elementPropertiesDataType = '';
  /**
   * The video source's unique identifier for this video. e.g. For YouTube video
   * https://www.youtube.com/watch?v=7U3axjORYZ0, the ID is 7U3axjORYZ0. For a
   * Google Drive video
   * https://drive.google.com/file/d/1xCgQLFTJi5_Xl8DgW_lcUYq5e-q6Hi5Q the ID is
   * 1xCgQLFTJi5_Xl8DgW_lcUYq5e-q6Hi5Q. To access a Google Drive video file, you
   * might need to add a resource key to the HTTP header for a subset of old
   * files. For more information, see [Access link-shared files using resource
   * keys](https://developers.google.com/drive/api/v3/resource-keys).
   *
   * @var string
   */
  public $id;
  /**
   * A user-supplied object ID. If you specify an ID, it must be unique among
   * all pages and page elements in the presentation. The ID must start with an
   * alphanumeric character or an underscore (matches regex `[a-zA-Z0-9_]`);
   * remaining characters may include those as well as a hyphen or colon
   * (matches regex `[a-zA-Z0-9_-:]`). The length of the ID must not be less
   * than 5 or greater than 50. If you don't specify an ID, a unique one is
   * generated.
   *
   * @var string
   */
  public $objectId;
  /**
   * The video source.
   *
   * @var string
   */
  public $source;

  /**
   * The element properties for the video. The PageElementProperties.size
   * property is optional. If you don't specify a size, a default size is chosen
   * by the server. The PageElementProperties.transform property is optional.
   * The transform must not have shear components. If you don't specify a
   * transform, the video will be placed at the top left corner of the page.
   *
   * @param PageElementProperties $elementProperties
   */
  public function setElementProperties(PageElementProperties $elementProperties)
  {
    $this->elementProperties = $elementProperties;
  }
  /**
   * @return PageElementProperties
   */
  public function getElementProperties()
  {
    return $this->elementProperties;
  }
  /**
   * The video source's unique identifier for this video. e.g. For YouTube video
   * https://www.youtube.com/watch?v=7U3axjORYZ0, the ID is 7U3axjORYZ0. For a
   * Google Drive video
   * https://drive.google.com/file/d/1xCgQLFTJi5_Xl8DgW_lcUYq5e-q6Hi5Q the ID is
   * 1xCgQLFTJi5_Xl8DgW_lcUYq5e-q6Hi5Q. To access a Google Drive video file, you
   * might need to add a resource key to the HTTP header for a subset of old
   * files. For more information, see [Access link-shared files using resource
   * keys](https://developers.google.com/drive/api/v3/resource-keys).
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
   * A user-supplied object ID. If you specify an ID, it must be unique among
   * all pages and page elements in the presentation. The ID must start with an
   * alphanumeric character or an underscore (matches regex `[a-zA-Z0-9_]`);
   * remaining characters may include those as well as a hyphen or colon
   * (matches regex `[a-zA-Z0-9_-:]`). The length of the ID must not be less
   * than 5 or greater than 50. If you don't specify an ID, a unique one is
   * generated.
   *
   * @param string $objectId
   */
  public function setObjectId($objectId)
  {
    $this->objectId = $objectId;
  }
  /**
   * @return string
   */
  public function getObjectId()
  {
    return $this->objectId;
  }
  /**
   * The video source.
   *
   * Accepted values: SOURCE_UNSPECIFIED, YOUTUBE, DRIVE
   *
   * @param self::SOURCE_* $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return self::SOURCE_*
   */
  public function getSource()
  {
    return $this->source;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateVideoRequest::class, 'Google_Service_Slides_CreateVideoRequest');
