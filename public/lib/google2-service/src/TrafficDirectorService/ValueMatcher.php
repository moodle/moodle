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

namespace Google\Service\TrafficDirectorService;

class ValueMatcher extends \Google\Model
{
  /**
   * If specified, a match occurs if and only if the target value is a bool
   * value and is equal to this field.
   *
   * @var bool
   */
  public $boolMatch;
  protected $doubleMatchType = DoubleMatcher::class;
  protected $doubleMatchDataType = '';
  protected $listMatchType = ListMatcher::class;
  protected $listMatchDataType = '';
  protected $nullMatchType = NullMatch::class;
  protected $nullMatchDataType = '';
  protected $orMatchType = OrMatcher::class;
  protected $orMatchDataType = '';
  /**
   * If specified, value match will be performed based on whether the path is
   * referring to a valid primitive value in the metadata. If the path is
   * referring to a non-primitive value, the result is always not matched.
   *
   * @var bool
   */
  public $presentMatch;
  protected $stringMatchType = StringMatcher::class;
  protected $stringMatchDataType = '';

  /**
   * If specified, a match occurs if and only if the target value is a bool
   * value and is equal to this field.
   *
   * @param bool $boolMatch
   */
  public function setBoolMatch($boolMatch)
  {
    $this->boolMatch = $boolMatch;
  }
  /**
   * @return bool
   */
  public function getBoolMatch()
  {
    return $this->boolMatch;
  }
  /**
   * If specified, a match occurs if and only if the target value is a double
   * value and is matched to this field.
   *
   * @param DoubleMatcher $doubleMatch
   */
  public function setDoubleMatch(DoubleMatcher $doubleMatch)
  {
    $this->doubleMatch = $doubleMatch;
  }
  /**
   * @return DoubleMatcher
   */
  public function getDoubleMatch()
  {
    return $this->doubleMatch;
  }
  /**
   * If specified, a match occurs if and only if the target value is a list
   * value and is matched to this field.
   *
   * @param ListMatcher $listMatch
   */
  public function setListMatch(ListMatcher $listMatch)
  {
    $this->listMatch = $listMatch;
  }
  /**
   * @return ListMatcher
   */
  public function getListMatch()
  {
    return $this->listMatch;
  }
  /**
   * If specified, a match occurs if and only if the target value is a
   * NullValue.
   *
   * @param NullMatch $nullMatch
   */
  public function setNullMatch(NullMatch $nullMatch)
  {
    $this->nullMatch = $nullMatch;
  }
  /**
   * @return NullMatch
   */
  public function getNullMatch()
  {
    return $this->nullMatch;
  }
  /**
   * If specified, a match occurs if and only if any of the alternatives in the
   * match accept the value.
   *
   * @param OrMatcher $orMatch
   */
  public function setOrMatch(OrMatcher $orMatch)
  {
    $this->orMatch = $orMatch;
  }
  /**
   * @return OrMatcher
   */
  public function getOrMatch()
  {
    return $this->orMatch;
  }
  /**
   * If specified, value match will be performed based on whether the path is
   * referring to a valid primitive value in the metadata. If the path is
   * referring to a non-primitive value, the result is always not matched.
   *
   * @param bool $presentMatch
   */
  public function setPresentMatch($presentMatch)
  {
    $this->presentMatch = $presentMatch;
  }
  /**
   * @return bool
   */
  public function getPresentMatch()
  {
    return $this->presentMatch;
  }
  /**
   * If specified, a match occurs if and only if the target value is a string
   * value and is matched to this field.
   *
   * @param StringMatcher $stringMatch
   */
  public function setStringMatch(StringMatcher $stringMatch)
  {
    $this->stringMatch = $stringMatch;
  }
  /**
   * @return StringMatcher
   */
  public function getStringMatch()
  {
    return $this->stringMatch;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ValueMatcher::class, 'Google_Service_TrafficDirectorService_ValueMatcher');
