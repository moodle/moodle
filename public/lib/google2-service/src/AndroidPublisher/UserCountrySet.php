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

class UserCountrySet extends \Google\Collection
{
  protected $collection_key = 'countryCodes';
  /**
   * List of country codes representing countries. A Country code is represented
   * in ISO 3166 alpha-2 format. For Example:- "IT" for Italy, "GE" for Georgia.
   *
   * @var string[]
   */
  public $countryCodes;
  /**
   * Country set name.
   *
   * @var string
   */
  public $name;

  /**
   * List of country codes representing countries. A Country code is represented
   * in ISO 3166 alpha-2 format. For Example:- "IT" for Italy, "GE" for Georgia.
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
   * Country set name.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserCountrySet::class, 'Google_Service_AndroidPublisher_UserCountrySet');
