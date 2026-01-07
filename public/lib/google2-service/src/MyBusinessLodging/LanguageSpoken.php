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

namespace Google\Service\MyBusinessLodging;

class LanguageSpoken extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const SPOKEN_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const SPOKEN_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const SPOKEN_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const SPOKEN_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Required. The BCP-47 language code for the spoken language. Currently
   * accepted codes: ar, de, en, es, fil, fr, hi, id, it, ja, ko, nl, pt, ru,
   * vi, yue, zh.
   *
   * @var string
   */
  public $languageCode;
  /**
   * At least one member of the staff can speak the language.
   *
   * @var bool
   */
  public $spoken;
  /**
   * Spoken exception.
   *
   * @var string
   */
  public $spokenException;

  /**
   * Required. The BCP-47 language code for the spoken language. Currently
   * accepted codes: ar, de, en, es, fil, fr, hi, id, it, ja, ko, nl, pt, ru,
   * vi, yue, zh.
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
   * At least one member of the staff can speak the language.
   *
   * @param bool $spoken
   */
  public function setSpoken($spoken)
  {
    $this->spoken = $spoken;
  }
  /**
   * @return bool
   */
  public function getSpoken()
  {
    return $this->spoken;
  }
  /**
   * Spoken exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::SPOKEN_EXCEPTION_* $spokenException
   */
  public function setSpokenException($spokenException)
  {
    $this->spokenException = $spokenException;
  }
  /**
   * @return self::SPOKEN_EXCEPTION_*
   */
  public function getSpokenException()
  {
    return $this->spokenException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LanguageSpoken::class, 'Google_Service_MyBusinessLodging_LanguageSpoken');
