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

namespace Google\Service\AndroidPublisher;

class UserCountriesTargeting extends \Google\Collection
{
  protected $collection_key = 'countryCodes';
  /**
   * List of country codes in the two-letter CLDR territory format.
   *
   * @var string[]
   */
  public $countryCodes;
  /**
   * Indicates if the list above is exclusive.
   *
   * @var bool
   */
  public $exclude;

  /**
   * List of country codes in the two-letter CLDR territory format.
   *
   * @param string[] $countryCodes
   */
  public function setCountryCodes($countryCodes)
  {
    $this->countryCodes = $countryCodes;
  }
  /**
   * @return string[]
   */
  public function getCountryCodes()
  {
    return $this->countryCodes;
  }
  /**
   * Indicates if the list above is exclusive.
   *
   * @param bool $exclude
   */
  public function setExclude($exclude)
  {
    $this->exclude = $exclude;
  }
  /**
   * @return bool
   */
  public function getExclude()
  {
    return $this->exclude;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserCountriesTargeting::class, 'Google_Service_AndroidPublisher_UserCountriesTargeting');
