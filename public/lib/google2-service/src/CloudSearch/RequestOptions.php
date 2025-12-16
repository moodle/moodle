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

namespace Google\Service\CloudSearch;

class RequestOptions extends \Google\Model
{
  protected $debugOptionsType = DebugOptions::class;
  protected $debugOptionsDataType = '';
  /**
   * The BCP-47 language code, such as "en-US" or "sr-Latn". For more
   * information, see
   * http://www.unicode.org/reports/tr35/#Unicode_locale_identifier. For
   * translations. Set this field using the language set in browser or for the
   * page. In the event that the user's language preference is known, set this
   * field to the known user language. When specified, the documents in search
   * results are biased towards the specified language. The Suggest API uses
   * this field as a hint to make better third-party autocomplete predictions.
   *
   * @var string
   */
  public $languageCode;
  /**
   * The ID generated when you create a search application using the [admin
   * console](https://support.google.com/a/answer/9043922).
   *
   * @var string
   */
  public $searchApplicationId;
  /**
   * Current user's time zone id, such as "America/Los_Angeles" or
   * "Australia/Sydney". These IDs are defined by [Unicode Common Locale Data
   * Repository (CLDR)](http://cldr.unicode.org/) project, and currently
   * available in the file [timezone.xml](http://unicode.org/repos/cldr/trunk/co
   * mmon/bcp47/timezone.xml). This field is used to correctly interpret date
   * and time queries. If this field is not specified, the default time zone
   * (UTC) is used.
   *
   * @var string
   */
  public $timeZone;

  /**
   * Debug options of the request
   *
   * @param DebugOptions $debugOptions
   */
  public function setDebugOptions(DebugOptions $debugOptions)
  {
    $this->debugOptions = $debugOptions;
  }
  /**
   * @return DebugOptions
   */
  public function getDebugOptions()
  {
    return $this->debugOptions;
  }
  /**
   * The BCP-47 language code, such as "en-US" or "sr-Latn". For more
   * information, see
   * http://www.unicode.org/reports/tr35/#Unicode_locale_identifier. For
   * translations. Set this field using the language set in browser or for the
   * page. In the event that the user's language preference is known, set this
   * field to the known user language. When specified, the documents in search
   * results are biased towards the specified language. The Suggest API uses
   * this field as a hint to make better third-party autocomplete predictions.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * The ID generated when you create a search application using the [admin
   * console](https://support.google.com/a/answer/9043922).
   *
   * @param string $searchApplicationId
   */
  public function setSearchApplicationId($searchApplicationId)
  {
    $this->searchApplicationId = $searchApplicationId;
  }
  /**
   * @return string
   */
  public function getSearchApplicationId()
  {
    return $this->searchApplicationId;
  }
  /**
   * Current user's time zone id, such as "America/Los_Angeles" or
   * "Australia/Sydney". These IDs are defined by [Unicode Common Locale Data
   * Repository (CLDR)](http://cldr.unicode.org/) project, and currently
   * available in the file [timezone.xml](http://unicode.org/repos/cldr/trunk/co
   * mmon/bcp47/timezone.xml). This field is used to correctly interpret date
   * and time queries. If this field is not specified, the default time zone
   * (UTC) is used.
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RequestOptions::class, 'Google_Service_CloudSearch_RequestOptions');
