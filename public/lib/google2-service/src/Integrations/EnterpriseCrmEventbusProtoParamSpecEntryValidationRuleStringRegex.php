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

namespace Google\Service\Integrations;

class EnterpriseCrmEventbusProtoParamSpecEntryValidationRuleStringRegex extends \Google\Model
{
  /**
   * Whether the regex matcher is applied exclusively (if true, matching values
   * will be rejected).
   *
   * @var bool
   */
  public $exclusive;
  /**
   * The regex applied to the input value(s).
   *
   * @var string
   */
  public $regex;

  /**
   * Whether the regex matcher is applied exclusively (if true, matching values
   * will be rejected).
   *
   * @param bool $exclusive
   */
  public function setExclusive($exclusive)
  {
    $this->exclusive = $exclusive;
  }
  /**
   * @return bool
   */
  public function getExclusive()
  {
    return $this->exclusive;
  }
  /**
   * The regex applied to the input value(s).
   *
   * @param string $regex
   */
  public function setRegex($regex)
  {
    $this->regex = $regex;
  }
  /**
   * @return string
   */
  public function getRegex()
  {
    return $this->regex;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoParamSpecEntryValidationRuleStringRegex::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoParamSpecEntryValidationRuleStringRegex');
