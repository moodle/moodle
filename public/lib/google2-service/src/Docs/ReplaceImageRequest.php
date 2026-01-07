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

namespace Google\Service\Docs;

class ReplaceImageRequest extends \Google\Model
{
  /**
   * Unspecified image replace method. This value must not be used.
   */
  public const IMAGE_REPLACE_METHOD_IMAGE_REPLACE_METHOD_UNSPECIFIED = 'IMAGE_REPLACE_METHOD_UNSPECIFIED';
  /**
   * Scales and centers the image to fill the bounds of the original image. The
   * image may be cropped in order to fill the original image's bounds. The
   * rendered size of the image will be the same as the original image.
   */
  public const IMAGE_REPLACE_METHOD_CENTER_CROP = 'CENTER_CROP';
  /**
   * The ID of the existing image that will be replaced. The ID can be retrieved
   * from the response of a get request.
   *
   * @var string
   */
  public $imageObjectId;
  /**
   * The replacement method.
   *
   * @var string
   */
  public $imageReplaceMethod;
  /**
   * The tab that the image to be replaced is in. When omitted, the request is
   * applied to the first tab. In a document containing a single tab: - If
   * provided, must match the singular tab's ID. - If omitted, the request
   * applies to the singular tab. In a document containing multiple tabs: - If
   * provided, the request applies to the specified tab. - If omitted, the
   * request applies to the first tab in the document.
   *
   * @var string
   */
  public $tabId;
  /**
   * The URI of the new image. The image is fetched once at insertion time and a
   * copy is stored for display inside the document. Images must be less than
   * 50MB, cannot exceed 25 megapixels, and must be in PNG, JPEG, or GIF format.
   * The provided URI can't surpass 2 KB in length. The URI is saved with the
   * image, and exposed through the ImageProperties.source_uri field.
   *
   * @var string
   */
  public $uri;

  /**
   * The ID of the existing image that will be replaced. The ID can be retrieved
   * from the response of a get request.
   *
   * @param string $imageObjectId
   */
  public function setImageObjectId($imageObjectId)
  {
    $this->imageObjectId = $imageObjectId;
  }
  /**
   * @return string
   */
  public function getImageObjectId()
  {
    return $this->imageObjectId;
  }
  /**
   * The replacement method.
   *
   * Accepted values: IMAGE_REPLACE_METHOD_UNSPECIFIED, CENTER_CROP
   *
   * @param self::IMAGE_REPLACE_METHOD_* $imageReplaceMethod
   */
  public function setImageReplaceMethod($imageReplaceMethod)
  {
    $this->imageReplaceMethod = $imageReplaceMethod;
  }
  /**
   * @return self::IMAGE_REPLACE_METHOD_*
   */
  public function getImageReplaceMethod()
  {
    return $this->imageReplaceMethod;
  }
  /**
   * The tab that the image to be replaced is in. When omitted, the request is
   * applied to the first tab. In a document containing a single tab: - If
   * provided, must match the singular tab's ID. - If omitted, the request
   * applies to the singular tab. In a document containing multiple tabs: - If
   * provided, the request applies to the specified tab. - If omitted, the
   * request applies to the first tab in the document.
   *
   * @param string $tabId
   */
  public function setTabId($tabId)
  {
    $this->tabId = $tabId;
  }
  /**
   * @return string
   */
  public function getTabId()
  {
    return $this->tabId;
  }
  /**
   * The URI of the new image. The image is fetched once at insertion time and a
   * copy is stored for display inside the document. Images must be less than
   * 50MB, cannot exceed 25 megapixels, and must be in PNG, JPEG, or GIF format.
   * The provided URI can't surpass 2 KB in length. The URI is saved with the
   * image, and exposed through the ImageProperties.source_uri field.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReplaceImageRequest::class, 'Google_Service_Docs_ReplaceImageRequest');
