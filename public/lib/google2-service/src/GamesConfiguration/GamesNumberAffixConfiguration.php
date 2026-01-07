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

namespace Google\Service\GamesConfiguration;

class GamesNumberAffixConfiguration extends \Google\Model
{
  protected $fewType = LocalizedStringBundle::class;
  protected $fewDataType = '';
  protected $manyType = LocalizedStringBundle::class;
  protected $manyDataType = '';
  protected $oneType = LocalizedStringBundle::class;
  protected $oneDataType = '';
  protected $otherType = LocalizedStringBundle::class;
  protected $otherDataType = '';
  protected $twoType = LocalizedStringBundle::class;
  protected $twoDataType = '';
  protected $zeroType = LocalizedStringBundle::class;
  protected $zeroDataType = '';

  /**
   * When the language requires special treatment of "small" numbers (as with 2,
   * 3, and 4 in Czech; or numbers ending 2, 3, or 4 but not 12, 13, or 14 in
   * Polish).
   *
   * @param LocalizedStringBundle $few
   */
  public function setFew(LocalizedStringBundle $few)
  {
    $this->few = $few;
  }
  /**
   * @return LocalizedStringBundle
   */
  public function getFew()
  {
    return $this->few;
  }
  /**
   * When the language requires special treatment of "large" numbers (as with
   * numbers ending 11-99 in Maltese).
   *
   * @param LocalizedStringBundle $many
   */
  public function setMany(LocalizedStringBundle $many)
  {
    $this->many = $many;
  }
  /**
   * @return LocalizedStringBundle
   */
  public function getMany()
  {
    return $this->many;
  }
  /**
   * When the language requires special treatment of numbers like one (as with
   * the number 1 in English and most other languages; in Russian, any number
   * ending in 1 but not ending in 11 is in this class).
   *
   * @param LocalizedStringBundle $one
   */
  public function setOne(LocalizedStringBundle $one)
  {
    $this->one = $one;
  }
  /**
   * @return LocalizedStringBundle
   */
  public function getOne()
  {
    return $this->one;
  }
  /**
   * When the language does not require special treatment of the given quantity
   * (as with all numbers in Chinese, or 42 in English).
   *
   * @param LocalizedStringBundle $other
   */
  public function setOther(LocalizedStringBundle $other)
  {
    $this->other = $other;
  }
  /**
   * @return LocalizedStringBundle
   */
  public function getOther()
  {
    return $this->other;
  }
  /**
   * When the language requires special treatment of numbers like two (as with 2
   * in Welsh, or 102 in Slovenian).
   *
   * @param LocalizedStringBundle $two
   */
  public function setTwo(LocalizedStringBundle $two)
  {
    $this->two = $two;
  }
  /**
   * @return LocalizedStringBundle
   */
  public function getTwo()
  {
    return $this->two;
  }
  /**
   * When the language requires special treatment of the number 0 (as in
   * Arabic).
   *
   * @param LocalizedStringBundle $zero
   */
  public function setZero(LocalizedStringBundle $zero)
  {
    $this->zero = $zero;
  }
  /**
   * @return LocalizedStringBundle
   */
  public function getZero()
  {
    return $this->zero;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GamesNumberAffixConfiguration::class, 'Google_Service_GamesConfiguration_GamesNumberAffixConfiguration');
