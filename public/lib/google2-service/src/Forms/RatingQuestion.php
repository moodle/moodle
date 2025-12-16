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

class RatingQuestion extends \Google\Model
{
  /**
   * Default value. Unused.
   */
  public const ICON_TYPE_RATING_ICON_TYPE_UNSPECIFIED = 'RATING_ICON_TYPE_UNSPECIFIED';
  /**
   * A star icon.
   */
  public const ICON_TYPE_STAR = 'STAR';
  /**
   * A heart icon.
   */
  public const ICON_TYPE_HEART = 'HEART';
  /**
   * A thumbs down icon.
   */
  public const ICON_TYPE_THUMB_UP = 'THUMB_UP';
  /**
   * Required. The icon type to use for the rating.
   *
   * @var string
   */
  public $iconType;
  /**
   * Required. The rating scale level of the rating question.
   *
   * @var int
   */
  public $ratingScaleLevel;

  /**
   * Required. The icon type to use for the rating.
   *
   * Accepted values: RATING_ICON_TYPE_UNSPECIFIED, STAR, HEART, THUMB_UP
   *
   * @param self::ICON_TYPE_* $iconType
   */
  public function setIconType($iconType)
  {
    $this->iconType = $iconType;
  }
  /**
   * @return self::ICON_TYPE_*
   */
  public function getIconType()
  {
    return $this->iconType;
  }
  /**
   * Required. The rating scale level of the rating question.
   *
   * @param int $ratingScaleLevel
   */
  public function setRatingScaleLevel($ratingScaleLevel)
  {
    $this->ratingScaleLevel = $ratingScaleLevel;
  }
  /**
   * @return int
   */
  public function getRatingScaleLevel()
  {
    return $this->ratingScaleLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RatingQuestion::class, 'Google_Service_Forms_RatingQuestion');
