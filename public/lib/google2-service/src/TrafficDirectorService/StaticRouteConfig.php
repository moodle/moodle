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

class StaticRouteConfig extends \Google\Model
{
  /**
   * The timestamp when the Route was last updated.
   *
   * @var string
   */
  public $lastUpdated;
  /**
   * The route config.
   *
   * @var array[]
   */
  public $routeConfig;

  /**
   * The timestamp when the Route was last updated.
   *
   * @param string $lastUpdated
   */
  public function setLastUpdated($lastUpdated)
  {
    $this->lastUpdated = $lastUpdated;
  }
  /**
   * @return string
   */
  public function getLastUpdated()
  {
    return $this->lastUpdated;
  }
  /**
   * The route config.
   *
   * @param array[] $routeConfig
   */
  public function setRouteConfig($routeConfig)
  {
    $this->routeConfig = $routeConfig;
  }
  /**
   * @return array[]
   */
  public function getRouteConfig()
  {
    return $this->routeConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StaticRouteConfig::class, 'Google_Service_TrafficDirectorService_StaticRouteConfig');
