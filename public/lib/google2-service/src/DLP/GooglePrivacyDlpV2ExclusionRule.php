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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2ExclusionRule extends \Google\Model
{
  /**
   * Invalid.
   */
  public const MATCHING_TYPE_MATCHING_TYPE_UNSPECIFIED = 'MATCHING_TYPE_UNSPECIFIED';
  /**
   * Full match. - Dictionary: join of Dictionary results matched the complete
   * finding quote - Regex: all regex matches fill a finding quote from start to
   * end - Exclude infoType: completely inside affecting infoTypes findings
   */
  public const MATCHING_TYPE_MATCHING_TYPE_FULL_MATCH = 'MATCHING_TYPE_FULL_MATCH';
  /**
   * Partial match. - Dictionary: at least one of the tokens in the finding
   * matches - Regex: substring of the finding matches - Exclude infoType:
   * intersects with affecting infoTypes findings
   */
  public const MATCHING_TYPE_MATCHING_TYPE_PARTIAL_MATCH = 'MATCHING_TYPE_PARTIAL_MATCH';
  /**
   * Inverse match. - Dictionary: no tokens in the finding match the dictionary
   * - Regex: finding doesn't match the regex - Exclude infoType: no
   * intersection with affecting infoTypes findings
   */
  public const MATCHING_TYPE_MATCHING_TYPE_INVERSE_MATCH = 'MATCHING_TYPE_INVERSE_MATCH';
  protected $dictionaryType = GooglePrivacyDlpV2Dictionary::class;
  protected $dictionaryDataType = '';
  protected $excludeByHotwordType = GooglePrivacyDlpV2ExcludeByHotword::class;
  protected $excludeByHotwordDataType = '';
  protected $excludeInfoTypesType = GooglePrivacyDlpV2ExcludeInfoTypes::class;
  protected $excludeInfoTypesDataType = '';
  /**
   * How the rule is applied, see MatchingType documentation for details.
   *
   * @var string
   */
  public $matchingType;
  protected $regexType = GooglePrivacyDlpV2Regex::class;
  protected $regexDataType = '';

  /**
   * Dictionary which defines the rule.
   *
   * @param GooglePrivacyDlpV2Dictionary $dictionary
   */
  public function setDictionary(GooglePrivacyDlpV2Dictionary $dictionary)
  {
    $this->dictionary = $dictionary;
  }
  /**
   * @return GooglePrivacyDlpV2Dictionary
   */
  public function getDictionary()
  {
    return $this->dictionary;
  }
  /**
   * Drop if the hotword rule is contained in the proximate context. For tabular
   * data, the context includes the column name.
   *
   * @param GooglePrivacyDlpV2ExcludeByHotword $excludeByHotword
   */
  public function setExcludeByHotword(GooglePrivacyDlpV2ExcludeByHotword $excludeByHotword)
  {
    $this->excludeByHotword = $excludeByHotword;
  }
  /**
   * @return GooglePrivacyDlpV2ExcludeByHotword
   */
  public function getExcludeByHotword()
  {
    return $this->excludeByHotword;
  }
  /**
   * Set of infoTypes for which findings would affect this rule.
   *
   * @param GooglePrivacyDlpV2ExcludeInfoTypes $excludeInfoTypes
   */
  public function setExcludeInfoTypes(GooglePrivacyDlpV2ExcludeInfoTypes $excludeInfoTypes)
  {
    $this->excludeInfoTypes = $excludeInfoTypes;
  }
  /**
   * @return GooglePrivacyDlpV2ExcludeInfoTypes
   */
  public function getExcludeInfoTypes()
  {
    return $this->excludeInfoTypes;
  }
  /**
   * How the rule is applied, see MatchingType documentation for details.
   *
   * Accepted values: MATCHING_TYPE_UNSPECIFIED, MATCHING_TYPE_FULL_MATCH,
   * MATCHING_TYPE_PARTIAL_MATCH, MATCHING_TYPE_INVERSE_MATCH
   *
   * @param self::MATCHING_TYPE_* $matchingType
   */
  public function setMatchingType($matchingType)
  {
    $this->matchingType = $matchingType;
  }
  /**
   * @return self::MATCHING_TYPE_*
   */
  public function getMatchingType()
  {
    return $this->matchingType;
  }
  /**
   * Regular expression which defines the rule.
   *
   * @param GooglePrivacyDlpV2Regex $regex
   */
  public function setRegex(GooglePrivacyDlpV2Regex $regex)
  {
    $this->regex = $regex;
  }
  /**
   * @return GooglePrivacyDlpV2Regex
   */
  public function getRegex()
  {
    return $this->regex;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2ExclusionRule::class, 'Google_Service_DLP_GooglePrivacyDlpV2ExclusionRule');
