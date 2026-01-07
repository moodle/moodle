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

class CreateImageRequest extends \Google\Model
{
  protected $elementPropertiesType = PageElementProperties::class;
  protected $elementPropertiesDataType = '';
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
   * The image URL. The image is fetched once at insertion time and a copy is
   * stored for display inside the presentation. Images must be less than 50 MB
   * in size, can't exceed 25 megapixels, and must be in one of PNG, JPEG, or
   * GIF formats. The provided URL must be publicly accessible and up to 2 KB in
   * length. The URL is saved with the image, and exposed through the
   * Image.source_url field.
   *
   * @var string
   */
  public $url;

  /**
   * The element properties for the image. When the aspect ratio of the provided
   * size does not match the image aspect ratio, the image is scaled and
   * centered with respect to the size in order to maintain the aspect ratio.
   * The provided transform is applied after this operation. The
   * PageElementProperties.size property is optional. If you don't specify the
   * size, the default size of the image is used. The
   * PageElementProperties.transform property is optional. If you don't specify
   * a transform, the image will be placed at the top-left corner of the page.
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
   * The image URL. The image is fetched once at insertion time and a copy is
   * stored for display inside the presentation. Images must be less than 50 MB
   * in size, can't exceed 25 megapixels, and must be in one of PNG, JPEG, or
   * GIF formats. The provided URL must be publicly accessible and up to 2 KB in
   * length. The URL is saved with the image, and exposed through the
   * Image.source_url field.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateImageRequest::class, 'Google_Service_Slides_CreateImageRequest');
