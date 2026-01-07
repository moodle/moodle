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

namespace Google\Service\SaaSServiceManagement;

class VariableMapping extends \Google\Model
{
  protected $fromType = FromMapping::class;
  protected $fromDataType = '';
  protected $toType = ToMapping::class;
  protected $toDataType = '';
  /**
   * Required. name of the variable
   *
   * @var string
   */
  public $variable;

  /**
   * Optional. Output variables which will get their values from dependencies
   *
   * @param FromMapping $from
   */
  public function setFrom(FromMapping $from)
  {
    $this->from = $from;
  }
  /**
   * @return FromMapping
   */
  public function getFrom()
  {
    return $this->from;
  }
  /**
   * Optional. Input variables whose values will be passed on to dependencies.
   *
   * @param ToMapping $to
   */
  public function setTo(ToMapping $to)
  {
    $this->to = $to;
  }
  /**
   * @return ToMapping
   */
  public function getTo()
  {
    return $this->to;
  }
  /**
   * Required. name of the variable
   *
   * @param string $variable
   */
  public function setVariable($variable)
  {
    $this->variable = $variable;
  }
  /**
   * @return string
   */
  public function getVariable()
  {
    return $this->variable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VariableMapping::class, 'Google_Service_SaaSServiceManagement_VariableMapping');
