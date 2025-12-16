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

namespace Google\Service\Dfareporting;

class TagSetting extends \Google\Model
{
  /**
   * Creates DART ad tags with a placeholder, such as kw=[keyword] and a list of
   * keywords. The site trafficker must replace [keyword] with the keywords
   * targeted by an ad.
   */
  public const KEYWORD_OPTION_PLACEHOLDER_WITH_LIST_OF_KEYWORDS = 'PLACEHOLDER_WITH_LIST_OF_KEYWORDS';
  /**
   * Creates DART ad tags that do not have a placeholder for keywords and
   * creates a list of keywords separately from the DART ad tags. Use this
   * option if the site uses a keyword referrer or is a site that uses DART for
   * Publishers.
   */
  public const KEYWORD_OPTION_IGNORE = 'IGNORE';
  /**
   * Results in unique tag generation for each relevant keyword during tag
   * export. For example, an ad with three keywords will generate three tags
   * with each tag having its kw= parameter filled in with the relevant keyword
   * values.
   */
  public const KEYWORD_OPTION_GENERATE_SEPARATE_TAG_FOR_EACH_KEYWORD = 'GENERATE_SEPARATE_TAG_FOR_EACH_KEYWORD';
  /**
   * Additional key-values to be included in tags. Each key-value pair must be
   * of the form key=value, and pairs must be separated by a semicolon (;). Keys
   * and values must not contain commas. For example, id=2;color=red is a valid
   * value for this field.
   *
   * @var string
   */
  public $additionalKeyValues;
  /**
   * Whether static landing page URLs should be included in the tags. New
   * placements will default to the value set on their site.
   *
   * @var bool
   */
  public $includeClickThroughUrls;
  /**
   * Whether click-tracking string should be included in the tags.
   *
   * @var bool
   */
  public $includeClickTracking;
  /**
   * Optional. Indicates that the unescapedlpurl macro should be included in the
   * tag for the static landing page. New placements will default to the value
   * set on their site.
   *
   * @var bool
   */
  public $includeUnescapedlpurlMacro;
  /**
   * Option specifying how keywords are embedded in ad tags. This setting can be
   * used to specify whether keyword placeholders are inserted in placement tags
   * for this site. Publishers can then add keywords to those placeholders.
   *
   * @var string
   */
  public $keywordOption;

  /**
   * Additional key-values to be included in tags. Each key-value pair must be
   * of the form key=value, and pairs must be separated by a semicolon (;). Keys
   * and values must not contain commas. For example, id=2;color=red is a valid
   * value for this field.
   *
   * @param string $additionalKeyValues
   */
  public function setAdditionalKeyValues($additionalKeyValues)
  {
    $this->additionalKeyValues = $additionalKeyValues;
  }
  /**
   * @return string
   */
  public function getAdditionalKeyValues()
  {
    return $this->additionalKeyValues;
  }
  /**
   * Whether static landing page URLs should be included in the tags. New
   * placements will default to the value set on their site.
   *
   * @param bool $includeClickThroughUrls
   */
  public function setIncludeClickThroughUrls($includeClickThroughUrls)
  {
    $this->includeClickThroughUrls = $includeClickThroughUrls;
  }
  /**
   * @return bool
   */
  public function getIncludeClickThroughUrls()
  {
    return $this->includeClickThroughUrls;
  }
  /**
   * Whether click-tracking string should be included in the tags.
   *
   * @param bool $includeClickTracking
   */
  public function setIncludeClickTracking($includeClickTracking)
  {
    $this->includeClickTracking = $includeClickTracking;
  }
  /**
   * @return bool
   */
  public function getIncludeClickTracking()
  {
    return $this->includeClickTracking;
  }
  /**
   * Optional. Indicates that the unescapedlpurl macro should be included in the
   * tag for the static landing page. New placements will default to the value
   * set on their site.
   *
   * @param bool $includeUnescapedlpurlMacro
   */
  public function setIncludeUnescapedlpurlMacro($includeUnescapedlpurlMacro)
  {
    $this->includeUnescapedlpurlMacro = $includeUnescapedlpurlMacro;
  }
  /**
   * @return bool
   */
  public function getIncludeUnescapedlpurlMacro()
  {
    return $this->includeUnescapedlpurlMacro;
  }
  /**
   * Option specifying how keywords are embedded in ad tags. This setting can be
   * used to specify whether keyword placeholders are inserted in placement tags
   * for this site. Publishers can then add keywords to those placeholders.
   *
   * Accepted values: PLACEHOLDER_WITH_LIST_OF_KEYWORDS, IGNORE,
   * GENERATE_SEPARATE_TAG_FOR_EACH_KEYWORD
   *
   * @param self::KEYWORD_OPTION_* $keywordOption
   */
  public function setKeywordOption($keywordOption)
  {
    $this->keywordOption = $keywordOption;
  }
  /**
   * @return self::KEYWORD_OPTION_*
   */
  public function getKeywordOption()
  {
    return $this->keywordOption;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TagSetting::class, 'Google_Service_Dfareporting_TagSetting');
