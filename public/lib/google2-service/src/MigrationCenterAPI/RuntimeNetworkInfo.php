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

namespace Google\Service\MigrationCenterAPI;

class RuntimeNetworkInfo extends \Google\Model
{
  protected $connectionsType = NetworkConnectionList::class;
  protected $connectionsDataType = '';
  /**
   * Time of the last network scan.
   *
   * @var string
   */
  public $scanTime;

  /**
   * Network connections.
   *
   * @param NetworkConnectionList $connections
   */
  public function setConnections(NetworkConnectionList $connections)
  {
    $this->connections = $connections;
  }
  /**
   * @return NetworkConnectionList
   */
  public function getConnections()
  {
    return $this->connections;
  }
  /**
   * Time of the last network scan.
   *
   * @param string $scanTime
   */
  public function setScanTime($scanTime)
  {
    $this->scanTime = $scanTime;
  }
  /**
   * @return string
   */
  public function getScanTime()
  {
    return $this->scanTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RuntimeNetworkInfo::class, 'Google_Service_MigrationCenterAPI_RuntimeNetworkInfo');
