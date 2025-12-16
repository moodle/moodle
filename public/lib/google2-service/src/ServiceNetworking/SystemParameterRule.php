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

namespace Google\Service\ServiceNetworking;

class SystemParameterRule extends \Google\Collection
{
  protected $collection_key = 'parameters';
  protected $parametersType = SystemParameter::class;
  protected $parametersDataType = 'array';
  /**
   * Selects the methods to which this rule applies. Use '*' to indicate all
   * methods in all APIs. Refer to selector for syntax details.
   *
   * @var string
   */
  public $selector;

  /**
   * Define parameters. Multiple names may be defined for a parameter. For a
   * given method call, only one of them should be used. If multiple names are
   * used the behavior is implementation-dependent. If none of the specified
   * names are present the behavior is parameter-dependent.
   *
   * @param SystemParameter[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return SystemParameter[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Selects the methods to which this rule applies. Use '*' to indicate all
   * methods in all APIs. Refer to selector for syntax details.
   *
   * @param string $selector
   */
  public function setSelector($selector)
  {
    $this->selector = $selector;
  }
  /**
   * @return string
   */
  public function getSelector()
  {
    return $this->selector;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SystemParameterRule::class, 'Google_Service_ServiceNetworking_SystemParameterRule');
