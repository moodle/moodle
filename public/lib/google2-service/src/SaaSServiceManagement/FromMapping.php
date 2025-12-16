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

class FromMapping extends \Google\Model
{
  /**
   * Required. Alias of the dependency that the outputVariable will pass its
   * value to
   *
   * @var string
   */
  public $dependency;
  /**
   * Required. Name of the outputVariable on the dependency
   *
   * @var string
   */
  public $outputVariable;

  /**
   * Required. Alias of the dependency that the outputVariable will pass its
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
   * Required. Name of the outputVariable on the dependency
   *
   * @param string $outputVariable
   */
  public function setOutputVariable($outputVariable)
  {
    $this->outputVariable = $outputVariable;
  }
  /**
   * @return string
   */
  public function getOutputVariable()
  {
    return $this->outputVariable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FromMapping::class, 'Google_Service_SaaSServiceManagement_FromMapping');
