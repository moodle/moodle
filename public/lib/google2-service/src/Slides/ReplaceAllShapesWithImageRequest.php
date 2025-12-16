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

class ReplaceAllShapesWithImageRequest extends \Google\Collection
{
  /**
   * Unspecified image replace method. This value must not be used.
   */
  public const IMAGE_REPLACE_METHOD_IMAGE_REPLACE_METHOD_UNSPECIFIED = 'IMAGE_REPLACE_METHOD_UNSPECIFIED';
  /**
   * Scales and centers the image to fit within the bounds of the original shape
   * and maintains the image's aspect ratio. The rendered size of the image may
   * be smaller than the size of the shape. This is the default method when one
   * is not specified.
   */
  public const IMAGE_REPLACE_METHOD_CENTER_INSIDE = 'CENTER_INSIDE';
  /**
   * Scales and centers the image to fill the bounds of the original shape. The
   * image may be cropped in order to fill the shape. The rendered size of the
   * image will be the same as the original shape.
   */
  public const IMAGE_REPLACE_METHOD_CENTER_CROP = 'CENTER_CROP';
  /**
   * Scales and centers the image to fit within the bounds of the original shape
   * and maintains the image's aspect ratio. The rendered size of the image may
   * be smaller than the size of the shape. This is the default method when one
   * is not specified.
   */
  public const REPLACE_METHOD_CENTER_INSIDE = 'CENTER_INSIDE';
  /**
   * Scales and centers the image to fill the bounds of the original shape. The
   * image may be cropped in order to fill the shape. The rendered size of the
   * image will be the same as that of the original shape.
   */
  public const REPLACE_METHOD_CENTER_CROP = 'CENTER_CROP';
  protected $collection_key = 'pageObjectIds';
  protected $containsTextType = SubstringMatchCriteria::class;
  protected $containsTextDataType = '';
  /**
   * The image replace method. If you specify both a `replace_method` and an
   * `image_replace_method`, the `image_replace_method` takes precedence. If you
   * do not specify a value for `image_replace_method`, but specify a value for
   * `replace_method`, then the specified `replace_method` value is used. If you
   * do not specify either, then CENTER_INSIDE is used.
   *
   * @var string
   */
  public $imageReplaceMethod;
  /**
   * The image URL. The image is fetched once at insertion time and a copy is
   * stored for display inside the presentation. Images must be less than 50MB
   * in size, cannot exceed 25 megapixels, and must be in one of PNG, JPEG, or
   * GIF format. The provided URL can be at most 2 kB in length. The URL itself
   * is saved with the image, and exposed via the Image.source_url field.
   *
   * @var string
   */
  public $imageUrl;
  /**
   * If non-empty, limits the matches to page elements only on the given pages.
   * Returns a 400 bad request error if given the page object ID of a notes page
   * or a notes master, or if a page with that object ID doesn't exist in the
   * presentation.
   *
   * @var string[]
   */
  public $pageObjectIds;
  /**
   * The replace method. *Deprecated*: use `image_replace_method` instead. If
   * you specify both a `replace_method` and an `image_replace_method`, the
   * `image_replace_method` takes precedence.
   *
   * @deprecated
   * @var string
   */
  public $replaceMethod;

  /**
   * If set, this request will replace all of the shapes that contain the given
   * text.
   *
   * @param SubstringMatchCriteria $containsText
   */
  public function setContainsText(SubstringMatchCriteria $containsText)
  {
    $this->containsText = $containsText;
  }
  /**
   * @return SubstringMatchCriteria
   */
  public function getContainsText()
  {
    return $this->containsText;
  }
  /**
   * The image replace method. If you specify both a `replace_method` and an
   * `image_replace_method`, the `image_replace_method` takes precedence. If you
   * do not specify a value for `image_replace_method`, but specify a value for
   * `replace_method`, then the specified `replace_method` value is used. If you
   * do not specify either, then CENTER_INSIDE is used.
   *
   * Accepted values: IMAGE_REPLACE_METHOD_UNSPECIFIED, CENTER_INSIDE,
   * CENTER_CROP
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
   * The image URL. The image is fetched once at insertion time and a copy is
   * stored for display inside the presentation. Images must be less than 50MB
   * in size, cannot exceed 25 megapixels, and must be in one of PNG, JPEG, or
   * GIF format. The provided URL can be at most 2 kB in length. The URL itself
   * is saved with the image, and exposed via the Image.source_url field.
   *
   * @param string $imageUrl
   */
  public function setImageUrl($imageUrl)
  {
    $this->imageUrl = $imageUrl;
  }
  /**
   * @return string
   */
  public function getImageUrl()
  {
    return $this->imageUrl;
  }
  /**
   * If non-empty, limits the matches to page elements only on the given pages.
   * Returns a 400 bad request error if given the page object ID of a notes page
   * or a notes master, or if a page with that object ID doesn't exist in the
   * presentation.
   *
   * @param string[] $pageObjectIds
   */
  public function setPageObjectIds($pageObjectIds)
  {
    $this->pageObjectIds = $pageObjectIds;
  }
  /**
   * @return string[]
   */
  public function getPageObjectIds()
  {
    return $this->pageObjectIds;
  }
  /**
   * The replace method. *Deprecated*: use `image_replace_method` instead. If
   * you specify both a `replace_method` and an `image_replace_method`, the
   * `image_replace_method` takes precedence.
   *
   * Accepted values: CENTER_INSIDE, CENTER_CROP
   *
   * @deprecated
   * @param self::REPLACE_METHOD_* $replaceMethod
   */
  public function setReplaceMethod($replaceMethod)
  {
    $this->replaceMethod = $replaceMethod;
  }
  /**
   * @deprecated
   * @return self::REPLACE_METHOD_*
   */
  public function getReplaceMethod()
  {
    return $this->replaceMethod;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReplaceAllShapesWithImageRequest::class, 'Google_Service_Slides_ReplaceAllShapesWithImageRequest');
