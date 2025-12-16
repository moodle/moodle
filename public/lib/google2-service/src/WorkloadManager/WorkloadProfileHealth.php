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

class WorkloadProfileHealth extends \Google\Collection
{
  protected $collection_key = 'componentHealthes';
  /**
   * @var string
   */
  public $checkTime;
  protected $componentHealthesType = ComponentHealth::class;
  protected $componentHealthesDataType = 'array';
  /**
   * @var string
   */
  public $state;

  /**
   * @param string
   */
  public function setCheckTime($checkTime)
  {
    $this->checkTime = $checkTime;
  }
  /**
   * @return string
   */
  public function getCheckTime()
  {
    return $this->checkTime;
  }
  /**
   * @param ComponentHealth[]
   */
  public function setComponentHealthes($componentHealthes)
  {
    $this->componentHealthes = $componentHealthes;
  }
  /**
   * @return ComponentHealth[]
   */
  public function getComponentHealthes()
  {
    return $this->componentHealthes;
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkloadProfileHealth::class, 'Google_Service_WorkloadManager_WorkloadProfileHealth');
