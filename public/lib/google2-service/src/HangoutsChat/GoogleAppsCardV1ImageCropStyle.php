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

namespace Google\Service\HangoutsChat;

class GoogleAppsCardV1ImageCropStyle extends \Google\Model
{
  /**
   * Don't use. Unspecified.
   */
  public const TYPE_IMAGE_CROP_TYPE_UNSPECIFIED = 'IMAGE_CROP_TYPE_UNSPECIFIED';
  /**
   * Default value. Applies a square crop.
   */
  public const TYPE_SQUARE = 'SQUARE';
  /**
   * Applies a circular crop.
   */
  public const TYPE_CIRCLE = 'CIRCLE';
  /**
   * Applies a rectangular crop with a custom aspect ratio. Set the custom
   * aspect ratio with `aspectRatio`.
   */
  public const TYPE_RECTANGLE_CUSTOM = 'RECTANGLE_CUSTOM';
  /**
   * Applies a rectangular crop with a 4:3 aspect ratio.
   */
  public const TYPE_RECTANGLE_4_3 = 'RECTANGLE_4_3';
  /**
   * The aspect ratio to use if the crop type is `RECTANGLE_CUSTOM`. For
   * example, here's how to apply a 16:9 aspect ratio: ``` cropStyle { "type":
   * "RECTANGLE_CUSTOM", "aspectRatio": 16/9 } ```
   *
   * @var 
   */
  public $aspectRatio;
  /**
   * The crop type.
   *
   * @var string
   */
  public $type;

  public function setAspectRatio($aspectRatio)
  {
    $this->aspectRatio = $aspectRatio;
  }
  public function getAspectRatio()
  {
    return $this->aspectRatio;
  }
  /**
   * The crop type.
   *
   * Accepted values: IMAGE_CROP_TYPE_UNSPECIFIED, SQUARE, CIRCLE,
   * RECTANGLE_CUSTOM, RECTANGLE_4_3
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1ImageCropStyle::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1ImageCropStyle');
