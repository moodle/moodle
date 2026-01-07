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

class RegionDiskTargetEnvironment extends \Google\Collection
{
  protected $collection_key = 'replicaZones';
  /**
   * Required. Target project for the disk.
   *
   * @var string
   */
  public $project;
  /**
   * Required. Target region for the disk.
   *
   * @var string
   */
  public $region;
  /**
   * Required. Target URLs of the replica zones for the disk.
   *
   * @var string[]
   */
  public $replicaZones;

  /**
   * Required. Target project for the disk.
   *
   * @param string $project
   */
  public function setProject($project)
  {
    $this->project = $project;
  }
  /**
   * @return string
   */
  public function getProject()
  {
    return $this->project;
  }
  /**
   * Required. Target region for the disk.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * Required. Target URLs of the replica zones for the disk.
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
class_alias(RegionDiskTargetEnvironment::class, 'Google_Service_Backupdr_RegionDiskTargetEnvironment');
