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

namespace Google\Service\Backupdr;

class InitializeParams extends \Google\Collection
{
  protected $collection_key = 'replicaZones';
  /**
   * Optional. Specifies the disk name. If not specified, the default is to use
   * the name of the instance.
   *
   * @var string
   */
  public $diskName;
  /**
   * Optional. URL of the zone where the disk should be created. Required for
   * each regional disk associated with the instance.
   *
   * @var string[]
   */
  public $replicaZones;

  /**
   * Optional. Specifies the disk name. If not specified, the default is to use
   * the name of the instance.
   *
   * @param string $diskName
   */
  public function setDiskName($diskName)
  {
    $this->diskName = $diskName;
  }
  /**
   * @return string
   */
  public function getDiskName()
  {
    return $this->diskName;
  }
  /**
   * Optional. URL of the zone where the disk should be created. Required for
   * each regional disk associated with the instance.
   *
   * @param string[] $replicaZones
   */
  public function setReplicaZones($replicaZones)
  {
    $this->replicaZones = $replicaZones;
  }
  /**
   * @return string[]
   */
  public function getReplicaZones()
  {
    return $this->replicaZones;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InitializeParams::class, 'Google_Service_Backupdr_InitializeParams');
