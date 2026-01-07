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

namespace Google\Service\WorkloadManager;

class ComponentHealth extends \Google\Collection
{
  protected $collection_key = 'subComponentHealthes';
  /**
   * @var string
   */
  public $component;
  protected $componentHealthChecksType = HealthCheck::class;
  protected $componentHealthChecksDataType = 'array';
  /**
   * @var string
   */
  public $componentHealthType;
  /**
   * @var bool
   */
  public $isRequired;
  /**
   * @var string
   */
  public $state;
  protected $subComponentHealthesType = ComponentHealth::class;
  protected $subComponentHealthesDataType = 'array';

  /**
   * @param string
   */
  public function setComponent($component)
  {
    $this->component = $component;
  }
  /**
   * @return string
   */
  public function getComponent()
  {
    return $this->component;
  }
  /**
   * @param HealthCheck[]
   */
  public function setComponentHealthChecks($componentHealthChecks)
  {
    $this->componentHealthChecks = $componentHealthChecks;
  }
  /**
   * @return HealthCheck[]
   */
  public function getComponentHealthChecks()
  {
    return $this->componentHealthChecks;
  }
  /**
   * @param string
   */
  public function setComponentHealthType($componentHealthType)
  {
    $this->componentHealthType = $componentHealthType;
  }
  /**
   * @return string
   */
  public function getComponentHealthType()
  {
    return $this->componentHealthType;
  }
  /**
   * @param bool
   */
  public function setIsRequired($isRequired)
  {
    $this->isRequired = $isRequired;
  }
  /**
   * @return bool
   */
  public function getIsRequired()
  {
    return $this->isRequired;
  }
  /**
   * @param string
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * @param ComponentHealth[]
   */
  public function setSubComponentHealthes($subComponentHealthes)
  {
    $this->subComponentHealthes = $subComponentHealthes;
  }
  /**
   * @return ComponentHealth[]
   */
  public function getSubComponentHealthes()
  {
    return $this->subComponentHealthes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ComponentHealth::class, 'Google_Service_WorkloadManager_ComponentHealth');
