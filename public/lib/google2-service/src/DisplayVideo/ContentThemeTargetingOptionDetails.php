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

class ContentThemeTargetingOptionDetails extends \Google\Model
{
  /**
   * Content theme is not specified or is unknown in this version.
   */
  public const CONTENT_THEME_CONTENT_THEME_UNSPECIFIED = 'CONTENT_THEME_UNSPECIFIED';
  /**
   * Fighting video games.
   */
  public const CONTENT_THEME_CONTENT_THEME_FIGHTING_VIDEO_GAMES = 'CONTENT_THEME_FIGHTING_VIDEO_GAMES';
  /**
   * Mature games.
   */
  public const CONTENT_THEME_CONTENT_THEME_MATURE_GAMES = 'CONTENT_THEME_MATURE_GAMES';
  /**
   * Not yet determined health sources.
   */
  public const CONTENT_THEME_CONTENT_THEME_NOT_YET_DETERMINED_HEALTH_SOURCES = 'CONTENT_THEME_NOT_YET_DETERMINED_HEALTH_SOURCES';
  /**
   * Not yet determined news sources.
   */
  public const CONTENT_THEME_CONTENT_THEME_NOT_YET_DETERMINED_NEWS_SOURCES = 'CONTENT_THEME_NOT_YET_DETERMINED_NEWS_SOURCES';
  /**
   * Politics.
   */
  public const CONTENT_THEME_CONTENT_THEME_POLITICS = 'CONTENT_THEME_POLITICS';
  /**
   * Recent news.
   */
  public const CONTENT_THEME_CONTENT_THEME_RECENT_NEWS = 'CONTENT_THEME_RECENT_NEWS';
  /**
   * Religion.
   */
  public const CONTENT_THEME_CONTENT_THEME_RELIGION = 'CONTENT_THEME_RELIGION';
  /**
   * Unpleasant health content.
   */
  public const CONTENT_THEME_CONTENT_THEME_UNPLEASANT_HEALTH_CONTENT = 'CONTENT_THEME_UNPLEASANT_HEALTH_CONTENT';
  /**
   * Unpleasant news.
   */
  public const CONTENT_THEME_CONTENT_THEME_UNPLEASANT_NEWS = 'CONTENT_THEME_UNPLEASANT_NEWS';
  /**
   * Output only. An enum for the DV360 content theme content classifier.
   *
   * @var string
   */
  public $contentTheme;

  /**
   * Output only. An enum for the DV360 content theme content classifier.
   *
   * Accepted values: CONTENT_THEME_UNSPECIFIED,
   * CONTENT_THEME_FIGHTING_VIDEO_GAMES, CONTENT_THEME_MATURE_GAMES,
   * CONTENT_THEME_NOT_YET_DETERMINED_HEALTH_SOURCES,
   * CONTENT_THEME_NOT_YET_DETERMINED_NEWS_SOURCES, CONTENT_THEME_POLITICS,
   * CONTENT_THEME_RECENT_NEWS, CONTENT_THEME_RELIGION,
   * CONTENT_THEME_UNPLEASANT_HEALTH_CONTENT, CONTENT_THEME_UNPLEASANT_NEWS
   *
   * @param self::CONTENT_THEME_* $contentTheme
   */
  public function setContentTheme($contentTheme)
  {
    $this->contentTheme = $contentTheme;
  }
  /**
   * @return self::CONTENT_THEME_*
   */
  public function getContentTheme()
  {
    return $this->contentTheme;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContentThemeTargetingOptionDetails::class, 'Google_Service_DisplayVideo_ContentThemeTargetingOptionDetails');
