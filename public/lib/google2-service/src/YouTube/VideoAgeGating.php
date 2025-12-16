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

namespace Google\Service\YouTube;

class VideoAgeGating extends \Google\Model
{
  public const VIDEO_GAME_RATING_anyone = 'anyone';
  public const VIDEO_GAME_RATING_m15Plus = 'm15Plus';
  public const VIDEO_GAME_RATING_m16Plus = 'm16Plus';
  public const VIDEO_GAME_RATING_m17Plus = 'm17Plus';
  /**
   * Indicates whether or not the video has alcoholic beverage content. Only
   * users of legal purchasing age in a particular country, as identified by
   * ICAP, can view the content.
   *
   * @var bool
   */
  public $alcoholContent;
  /**
   * Age-restricted trailers. For redband trailers and adult-rated video-games.
   * Only users aged 18+ can view the content. The the field is true the content
   * is restricted to viewers aged 18+. Otherwise The field won't be present.
   *
   * @var bool
   */
  public $restricted;
  /**
   * Video game rating, if any.
   *
   * @var string
   */
  public $videoGameRating;

  /**
   * Indicates whether or not the video has alcoholic beverage content. Only
   * users of legal purchasing age in a particular country, as identified by
   * ICAP, can view the content.
   *
   * @param bool $alcoholContent
   */
  public function setAlcoholContent($alcoholContent)
  {
    $this->alcoholContent = $alcoholContent;
  }
  /**
   * @return bool
   */
  public function getAlcoholContent()
  {
    return $this->alcoholContent;
  }
  /**
   * Age-restricted trailers. For redband trailers and adult-rated video-games.
   * Only users aged 18+ can view the content. The the field is true the content
   * is restricted to viewers aged 18+. Otherwise The field won't be present.
   *
   * @param bool $restricted
   */
  public function setRestricted($restricted)
  {
    $this->restricted = $restricted;
  }
  /**
   * @return bool
   */
  public function getRestricted()
  {
    return $this->restricted;
  }
  /**
   * Video game rating, if any.
   *
   * Accepted values: anyone, m15Plus, m16Plus, m17Plus
   *
   * @param self::VIDEO_GAME_RATING_* $videoGameRating
   */
  public function setVideoGameRating($videoGameRating)
  {
    $this->videoGameRating = $videoGameRating;
  }
  /**
   * @return self::VIDEO_GAME_RATING_*
   */
  public function getVideoGameRating()
  {
    return $this->videoGameRating;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VideoAgeGating::class, 'Google_Service_YouTube_VideoAgeGating');
