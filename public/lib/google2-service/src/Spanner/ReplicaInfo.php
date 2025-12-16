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

namespace Google\Service\Spanner;

class ReplicaInfo extends \Google\Model
{
  /**
   * Not specified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Read-write replicas support both reads and writes. These replicas: *
   * Maintain a full copy of your data. * Serve reads. * Can vote whether to
   * commit a write. * Participate in leadership election. * Are eligible to
   * become a leader.
   */
  public const TYPE_READ_WRITE = 'READ_WRITE';
  /**
   * Read-only replicas only support reads (not writes). Read-only replicas: *
   * Maintain a full copy of your data. * Serve reads. * Do not participate in
   * voting to commit writes. * Are not eligible to become a leader.
   */
  public const TYPE_READ_ONLY = 'READ_ONLY';
  /**
   * Witness replicas don't support reads but do participate in voting to commit
   * writes. Witness replicas: * Do not maintain a full copy of data. * Do not
   * serve reads. * Vote whether to commit writes. * Participate in leader
   * election but are not eligible to become leader.
   */
  public const TYPE_WITNESS = 'WITNESS';
  /**
   * If true, this location is designated as the default leader location where
   * leader replicas are placed. See the [region types documentation](https://cl
   * oud.google.com/spanner/docs/instances#region_types) for more details.
   *
   * @var bool
   */
  public $defaultLeaderLocation;
  /**
   * The location of the serving resources, e.g., "us-central1".
   *
   * @var string
   */
  public $location;
  /**
   * The type of replica.
   *
   * @var string
   */
  public $type;

  /**
   * If true, this location is designated as the default leader location where
   * leader replicas are placed. See the [region types documentation](https://cl
   * oud.google.com/spanner/docs/instances#region_types) for more details.
   *
   * @param bool $defaultLeaderLocation
   */
  public function setDefaultLeaderLocation($defaultLeaderLocation)
  {
    $this->defaultLeaderLocation = $defaultLeaderLocation;
  }
  /**
   * @return bool
   */
  public function getDefaultLeaderLocation()
  {
    return $this->defaultLeaderLocation;
  }
  /**
   * The location of the serving resources, e.g., "us-central1".
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The type of replica.
   *
   * Accepted values: TYPE_UNSPECIFIED, READ_WRITE, READ_ONLY, WITNESS
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReplicaInfo::class, 'Google_Service_Spanner_ReplicaInfo');
