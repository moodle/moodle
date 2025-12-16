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

namespace Google\Service\DisplayVideo;

class DoubleVerifyAppStarRating extends \Google\Model
{
  /**
   * This enum is only a placeholder and it doesn't specify any app star rating
   * options.
   */
  public const AVOIDED_STAR_RATING_APP_STAR_RATE_UNSPECIFIED = 'APP_STAR_RATE_UNSPECIFIED';
  /**
   * Official Apps with rating < 1.5 Stars.
   */
  public const AVOIDED_STAR_RATING_APP_STAR_RATE_1_POINT_5_LESS = 'APP_STAR_RATE_1_POINT_5_LESS';
  /**
   * Official Apps with rating < 2 Stars.
   */
  public const AVOIDED_STAR_RATING_APP_STAR_RATE_2_LESS = 'APP_STAR_RATE_2_LESS';
  /**
   * Official Apps with rating < 2.5 Stars.
   */
  public const AVOIDED_STAR_RATING_APP_STAR_RATE_2_POINT_5_LESS = 'APP_STAR_RATE_2_POINT_5_LESS';
  /**
   * Official Apps with rating < 3 Stars.
   */
  public const AVOIDED_STAR_RATING_APP_STAR_RATE_3_LESS = 'APP_STAR_RATE_3_LESS';
  /**
   * Official Apps with rating < 3.5 Stars.
   */
  public const AVOIDED_STAR_RATING_APP_STAR_RATE_3_POINT_5_LESS = 'APP_STAR_RATE_3_POINT_5_LESS';
  /**
   * Official Apps with rating < 4 Stars.
   */
  public const AVOIDED_STAR_RATING_APP_STAR_RATE_4_LESS = 'APP_STAR_RATE_4_LESS';
  /**
   * Official Apps with rating < 4.5 Stars.
   */
  public const AVOIDED_STAR_RATING_APP_STAR_RATE_4_POINT_5_LESS = 'APP_STAR_RATE_4_POINT_5_LESS';
  /**
   * Avoid bidding on apps with insufficient star ratings.
   *
   * @var bool
   */
  public $avoidInsufficientStarRating;
  /**
   * Avoid bidding on apps with the star ratings.
   *
   * @var string
   */
  public $avoidedStarRating;

  /**
   * Avoid bidding on apps with insufficient star ratings.
   *
   * @param bool $avoidInsufficientStarRating
   */
  public function setAvoidInsufficientStarRating($avoidInsufficientStarRating)
  {
    $this->avoidInsufficientStarRating = $avoidInsufficientStarRating;
  }
  /**
   * @return bool
   */
  public function getAvoidInsufficientStarRating()
  {
    return $this->avoidInsufficientStarRating;
  }
  /**
   * Avoid bidding on apps with the star ratings.
   *
   * Accepted values: APP_STAR_RATE_UNSPECIFIED, APP_STAR_RATE_1_POINT_5_LESS,
   * APP_STAR_RATE_2_LESS, APP_STAR_RATE_2_POINT_5_LESS, APP_STAR_RATE_3_LESS,
   * APP_STAR_RATE_3_POINT_5_LESS, APP_STAR_RATE_4_LESS,
   * APP_STAR_RATE_4_POINT_5_LESS
   *
   * @param self::AVOIDED_STAR_RATING_* $avoidedStarRating
   */
  public function setAvoidedStarRating($avoidedStarRating)
  {
    $this->avoidedStarRating = $avoidedStarRating;
  }
  /**
   * @return self::AVOIDED_STAR_RATING_*
   */
  public function getAvoidedStarRating()
  {
    return $this->avoidedStarRating;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DoubleVerifyAppStarRating::class, 'Google_Service_DisplayVideo_DoubleVerifyAppStarRating');
