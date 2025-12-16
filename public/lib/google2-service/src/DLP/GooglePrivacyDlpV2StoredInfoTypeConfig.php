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

class GooglePrivacyDlpV2StoredInfoTypeConfig extends \Google\Model
{
  /**
   * Description of the StoredInfoType (max 256 characters).
   *
   * @var string
   */
  public $description;
  protected $dictionaryType = GooglePrivacyDlpV2Dictionary::class;
  protected $dictionaryDataType = '';
  /**
   * Display name of the StoredInfoType (max 256 characters).
   *
   * @var string
   */
  public $displayName;
  protected $largeCustomDictionaryType = GooglePrivacyDlpV2LargeCustomDictionaryConfig::class;
  protected $largeCustomDictionaryDataType = '';
  protected $regexType = GooglePrivacyDlpV2Regex::class;
  protected $regexDataType = '';

  /**
   * Description of the StoredInfoType (max 256 characters).
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Store dictionary-based CustomInfoType.
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
   * Display name of the StoredInfoType (max 256 characters).
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * StoredInfoType where findings are defined by a dictionary of phrases.
   *
   * @param GooglePrivacyDlpV2LargeCustomDictionaryConfig $largeCustomDictionary
   */
  public function setLargeCustomDictionary(GooglePrivacyDlpV2LargeCustomDictionaryConfig $largeCustomDictionary)
  {
    $this->largeCustomDictionary = $largeCustomDictionary;
  }
  /**
   * @return GooglePrivacyDlpV2LargeCustomDictionaryConfig
   */
  public function getLargeCustomDictionary()
  {
    return $this->largeCustomDictionary;
  }
  /**
   * Store regular expression-based StoredInfoType.
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
class_alias(GooglePrivacyDlpV2StoredInfoTypeConfig::class, 'Google_Service_DLP_GooglePrivacyDlpV2StoredInfoTypeConfig');
