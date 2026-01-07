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

class ToMapping extends \Google\Model
{
  /**
   * Required. Alias of the dependency that the inputVariable will pass its
   * value to
   *
   * @var string
   */
  public $dependency;
  /**
   * Optional. Tells SaaS Runtime if this mapping should be used during lookup
   * or not
   *
   * @var bool
   */
  public $ignoreForLookup;
  /**
   * Required. Name of the inputVariable on the dependency
   *
   * @var string
   */
  public $inputVariable;

  /**
   * Required. Alias of the dependency that the inputVariable will pass its
   * value to
   *
   * @param string $dependency
   */
  public function setDependency($dependency)
  {
    $this->dependency = $dependency;
  }
  /**
   * @return string
   */
  public function getDependency()
  {
    return $this->dependency;
  }
  /**
   * Optional. Tells SaaS Runtime if this mapping should be used during lookup
   * or not
   *
   * @param bool $ignoreForLookup
   */
  public function setIgnoreForLookup($ignoreForLookup)
  {
    $this->ignoreForLookup = $ignoreForLookup;
  }
  /**
   * @return bool
   */
  public function getIgnoreForLookup()
  {
    return $this->ignoreForLookup;
  }
  /**
   * Required. Name of the inputVariable on the dependency
   *
   * @param string $inputVariable
   */
  public function setInputVariable($inputVariable)
  {
    $this->inputVariable = $inputVariable;
  }
  /**
   * @return string
   */
  public function getInputVariable()
  {
    return $this->inputVariable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ToMapping::class, 'Google_Service_SaaSServiceManagement_ToMapping');
