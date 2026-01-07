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

class LanguageTargeting extends \Google\Collection
{
  protected $collection_key = 'value';
  /**
   * Alternative languages.
   *
   * @var string[]
   */
  public $alternatives;
  /**
   * ISO-639: 2 or 3 letter language code.
   *
   * @var string[]
   */
  public $value;

  /**
   * Alternative languages.
   *
   * @param string[] $alternatives
   */
  public function setAlternatives($alternatives)
  {
    $this->alternatives = $alternatives;
  }
  /**
   * @return string[]
   */
  public function getAlternatives()
  {
    return $this->alternatives;
  }
  /**
   * ISO-639: 2 or 3 letter language code.
   *
   * @param string[] $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string[]
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LanguageTargeting::class, 'Google_Service_AndroidPublisher_LanguageTargeting');
