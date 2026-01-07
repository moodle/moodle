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

class SensitiveCategoryAssignedTargetingOptionDetails extends \Google\Model
{
  /**
   * This enum is only a placeholder and doesn't specify a DV360 sensitive
   * category.
   */
  public const EXCLUDED_SENSITIVE_CATEGORY_SENSITIVE_CATEGORY_UNSPECIFIED = 'SENSITIVE_CATEGORY_UNSPECIFIED';
  /**
   * Adult or pornographic text, image, or video content.
   */
  public const EXCLUDED_SENSITIVE_CATEGORY_SENSITIVE_CATEGORY_ADULT = 'SENSITIVE_CATEGORY_ADULT';
  /**
   * Content that may be construed as biased against individuals, groups, or
   * organizations based on criteria such as race, religion, disability, sex,
   * age, veteran status, sexual orientation, gender identity, or political
   * affiliation. May also indicate discussion of such content, for instance, in
   * an academic or journalistic context.
   */
  public const EXCLUDED_SENSITIVE_CATEGORY_SENSITIVE_CATEGORY_DEROGATORY = 'SENSITIVE_CATEGORY_DEROGATORY';
  /**
   * Content related to audio, video, or software downloads.
   */
  public const EXCLUDED_SENSITIVE_CATEGORY_SENSITIVE_CATEGORY_DOWNLOADS_SHARING = 'SENSITIVE_CATEGORY_DOWNLOADS_SHARING';
  /**
   * Contains content related to personal weapons, including knives, guns, small
   * firearms, and ammunition. Selecting either "weapons" or "sensitive social
   * issues" will result in selecting both.
   */
  public const EXCLUDED_SENSITIVE_CATEGORY_SENSITIVE_CATEGORY_WEAPONS = 'SENSITIVE_CATEGORY_WEAPONS';
  /**
   * Contains content related to betting or wagering in a real-world or online
   * setting.
   */
  public const EXCLUDED_SENSITIVE_CATEGORY_SENSITIVE_CATEGORY_GAMBLING = 'SENSITIVE_CATEGORY_GAMBLING';
  /**
   * Content which may be considered graphically violent, gory, gruesome, or
   * shocking, such as street fighting videos, accident photos, descriptions of
   * torture, etc.
   */
  public const EXCLUDED_SENSITIVE_CATEGORY_SENSITIVE_CATEGORY_VIOLENCE = 'SENSITIVE_CATEGORY_VIOLENCE';
  /**
   * Adult content, as well as suggestive content that's not explicitly
   * pornographic. This category includes all pages categorized as adult.
   */
  public const EXCLUDED_SENSITIVE_CATEGORY_SENSITIVE_CATEGORY_SUGGESTIVE = 'SENSITIVE_CATEGORY_SUGGESTIVE';
  /**
   * Prominent use of words considered indecent, such as curse words and sexual
   * slang. Pages with only very occasional usage, such as news sites that might
   * include such words in a quotation, are not included.
   */
  public const EXCLUDED_SENSITIVE_CATEGORY_SENSITIVE_CATEGORY_PROFANITY = 'SENSITIVE_CATEGORY_PROFANITY';
  /**
   * Contains content related to alcoholic beverages, alcohol brands, recipes,
   * etc.
   */
  public const EXCLUDED_SENSITIVE_CATEGORY_SENSITIVE_CATEGORY_ALCOHOL = 'SENSITIVE_CATEGORY_ALCOHOL';
  /**
   * Contains content related to the recreational use of legal or illegal drugs,
   * as well as to drug paraphernalia or cultivation.
   */
  public const EXCLUDED_SENSITIVE_CATEGORY_SENSITIVE_CATEGORY_DRUGS = 'SENSITIVE_CATEGORY_DRUGS';
  /**
   * Contains content related to tobacco and tobacco accessories, including
   * lighters, humidors, ashtrays, etc.
   */
  public const EXCLUDED_SENSITIVE_CATEGORY_SENSITIVE_CATEGORY_TOBACCO = 'SENSITIVE_CATEGORY_TOBACCO';
  /**
   * Political news and media, including discussions of social, governmental,
   * and public policy.
   */
  public const EXCLUDED_SENSITIVE_CATEGORY_SENSITIVE_CATEGORY_POLITICS = 'SENSITIVE_CATEGORY_POLITICS';
  /**
   * Content related to religious thought or beliefs.
   */
  public const EXCLUDED_SENSITIVE_CATEGORY_SENSITIVE_CATEGORY_RELIGION = 'SENSITIVE_CATEGORY_RELIGION';
  /**
   * Content related to death, disasters, accidents, war, etc.
   */
  public const EXCLUDED_SENSITIVE_CATEGORY_SENSITIVE_CATEGORY_TRAGEDY = 'SENSITIVE_CATEGORY_TRAGEDY';
  /**
   * Content related to motor vehicle, aviation or other transportation
   * accidents.
   */
  public const EXCLUDED_SENSITIVE_CATEGORY_SENSITIVE_CATEGORY_TRANSPORTATION_ACCIDENTS = 'SENSITIVE_CATEGORY_TRANSPORTATION_ACCIDENTS';
  /**
   * Issues that evoke strong, opposing views and spark debate. These include
   * issues that are controversial in most countries and markets (such as
   * abortion), as well as those that are controversial in specific countries
   * and markets (such as immigration reform in the United States).
   */
  public const EXCLUDED_SENSITIVE_CATEGORY_SENSITIVE_CATEGORY_SENSITIVE_SOCIAL_ISSUES = 'SENSITIVE_CATEGORY_SENSITIVE_SOCIAL_ISSUES';
  /**
   * Content which may be considered shocking or disturbing, such as violent
   * news stories, stunts, or toilet humor.
   */
  public const EXCLUDED_SENSITIVE_CATEGORY_SENSITIVE_CATEGORY_SHOCKING = 'SENSITIVE_CATEGORY_SHOCKING';
  /**
   * YouTube videos embedded on websites outside of YouTube.com.
   */
  public const EXCLUDED_SENSITIVE_CATEGORY_SENSITIVE_CATEGORY_EMBEDDED_VIDEO = 'SENSITIVE_CATEGORY_EMBEDDED_VIDEO';
  /**
   * Video of live events streamed over the internet.
   */
  public const EXCLUDED_SENSITIVE_CATEGORY_SENSITIVE_CATEGORY_LIVE_STREAMING_VIDEO = 'SENSITIVE_CATEGORY_LIVE_STREAMING_VIDEO';
  /**
   * Required. An enum for the DV360 Sensitive category content classified to be
   * EXCLUDED.
   *
   * @var string
   */
  public $excludedSensitiveCategory;

  /**
   * Required. An enum for the DV360 Sensitive category content classified to be
   * EXCLUDED.
   *
   * Accepted values: SENSITIVE_CATEGORY_UNSPECIFIED, SENSITIVE_CATEGORY_ADULT,
   * SENSITIVE_CATEGORY_DEROGATORY, SENSITIVE_CATEGORY_DOWNLOADS_SHARING,
   * SENSITIVE_CATEGORY_WEAPONS, SENSITIVE_CATEGORY_GAMBLING,
   * SENSITIVE_CATEGORY_VIOLENCE, SENSITIVE_CATEGORY_SUGGESTIVE,
   * SENSITIVE_CATEGORY_PROFANITY, SENSITIVE_CATEGORY_ALCOHOL,
   * SENSITIVE_CATEGORY_DRUGS, SENSITIVE_CATEGORY_TOBACCO,
   * SENSITIVE_CATEGORY_POLITICS, SENSITIVE_CATEGORY_RELIGION,
   * SENSITIVE_CATEGORY_TRAGEDY, SENSITIVE_CATEGORY_TRANSPORTATION_ACCIDENTS,
   * SENSITIVE_CATEGORY_SENSITIVE_SOCIAL_ISSUES, SENSITIVE_CATEGORY_SHOCKING,
   * SENSITIVE_CATEGORY_EMBEDDED_VIDEO, SENSITIVE_CATEGORY_LIVE_STREAMING_VIDEO
   *
   * @param self::EXCLUDED_SENSITIVE_CATEGORY_* $excludedSensitiveCategory
   */
  public function setExcludedSensitiveCategory($excludedSensitiveCategory)
  {
    $this->excludedSensitiveCategory = $excludedSensitiveCategory;
  }
  /**
   * @return self::EXCLUDED_SENSITIVE_CATEGORY_*
   */
  public function getExcludedSensitiveCategory()
  {
    return $this->excludedSensitiveCategory;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SensitiveCategoryAssignedTargetingOptionDetails::class, 'Google_Service_DisplayVideo_SensitiveCategoryAssignedTargetingOptionDetails');
