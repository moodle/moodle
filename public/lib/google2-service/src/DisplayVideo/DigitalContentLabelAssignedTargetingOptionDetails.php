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

class DigitalContentLabelAssignedTargetingOptionDetails extends \Google\Model
{
  /**
   * Content label is not specified in this version. This enum is a place holder
   * for a default value and does not represent a real content rating.
   */
  public const EXCLUDED_CONTENT_RATING_TIER_CONTENT_RATING_TIER_UNSPECIFIED = 'CONTENT_RATING_TIER_UNSPECIFIED';
  /**
   * Content that has not been labeled.
   */
  public const EXCLUDED_CONTENT_RATING_TIER_CONTENT_RATING_TIER_UNRATED = 'CONTENT_RATING_TIER_UNRATED';
  /**
   * Content suitable for general audiences.
   */
  public const EXCLUDED_CONTENT_RATING_TIER_CONTENT_RATING_TIER_GENERAL = 'CONTENT_RATING_TIER_GENERAL';
  /**
   * Content suitable for most audiences with parental guidance.
   */
  public const EXCLUDED_CONTENT_RATING_TIER_CONTENT_RATING_TIER_PARENTAL_GUIDANCE = 'CONTENT_RATING_TIER_PARENTAL_GUIDANCE';
  /**
   * Content suitable for teen and older audiences.
   */
  public const EXCLUDED_CONTENT_RATING_TIER_CONTENT_RATING_TIER_TEENS = 'CONTENT_RATING_TIER_TEENS';
  /**
   * Content suitable only for mature audiences.
   */
  public const EXCLUDED_CONTENT_RATING_TIER_CONTENT_RATING_TIER_MATURE = 'CONTENT_RATING_TIER_MATURE';
  /**
   * Content suitable for family audiences. It is a subset of
   * CONTENT_RATING_TIER_GENERAL. Only applicable to YouTube and Partners line
   * items.
   */
  public const EXCLUDED_CONTENT_RATING_TIER_CONTENT_RATING_TIER_FAMILIES = 'CONTENT_RATING_TIER_FAMILIES';
  /**
   * Required. The display name of the digital content label rating tier to be
   * EXCLUDED.
   *
   * @var string
   */
  public $excludedContentRatingTier;

  /**
   * Required. The display name of the digital content label rating tier to be
   * EXCLUDED.
   *
   * Accepted values: CONTENT_RATING_TIER_UNSPECIFIED,
   * CONTENT_RATING_TIER_UNRATED, CONTENT_RATING_TIER_GENERAL,
   * CONTENT_RATING_TIER_PARENTAL_GUIDANCE, CONTENT_RATING_TIER_TEENS,
   * CONTENT_RATING_TIER_MATURE, CONTENT_RATING_TIER_FAMILIES
   *
   * @param self::EXCLUDED_CONTENT_RATING_TIER_* $excludedContentRatingTier
   */
  public function setExcludedContentRatingTier($excludedContentRatingTier)
  {
    $this->excludedContentRatingTier = $excludedContentRatingTier;
  }
  /**
   * @return self::EXCLUDED_CONTENT_RATING_TIER_*
   */
  public function getExcludedContentRatingTier()
  {
    return $this->excludedContentRatingTier;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DigitalContentLabelAssignedTargetingOptionDetails::class, 'Google_Service_DisplayVideo_DigitalContentLabelAssignedTargetingOptionDetails');
