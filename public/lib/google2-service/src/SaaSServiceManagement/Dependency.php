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

class Dependency extends \Google\Model
{
  /**
   * Required. An alias for the dependency. Used for input variable mapping.
   *
   * @var string
   */
  public $alias;
  /**
   * Required. Immutable. The unit kind of the dependency.
   *
   * @var string
   */
  public $unitKind;

  /**
   * Required. An alias for the dependency. Used for input variable mapping.
   *
   * @param string $alias
   */
  public function setAlias($alias)
  {
    $this->alias = $alias;
  }
  /**
   * @return string
   */
  public function getAlias()
  {
    return $this->alias;
  }
  /**
   * Required. Immutable. The unit kind of the dependency.
   *
   * @param string $unitKind
   */
  public function setUnitKind($unitKind)
  {
    $this->unitKind = $unitKind;
  }
  /**
   * @return string
   */
  public function getUnitKind()
  {
    return $this->unitKind;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Dependency::class, 'Google_Service_SaaSServiceManagement_Dependency');
