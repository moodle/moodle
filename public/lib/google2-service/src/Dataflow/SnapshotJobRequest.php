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

namespace Google\Service\Dataflow;

class SnapshotJobRequest extends \Google\Model
{
  /**
   * User specified description of the snapshot. Maybe empty.
   *
   * @var string
   */
  public $description;
  /**
   * The location that contains this job.
   *
   * @var string
   */
  public $location;
  /**
   * If true, perform snapshots for sources which support this.
   *
   * @var bool
   */
  public $snapshotSources;
  /**
   * TTL for the snapshot.
   *
   * @var string
   */
  public $ttl;

  /**
   * User specified description of the snapshot. Maybe empty.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The location that contains this job.
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
   * If true, perform snapshots for sources which support this.
   *
   * @param bool $snapshotSources
   */
  public function setSnapshotSources($snapshotSources)
  {
    $this->snapshotSources = $snapshotSources;
  }
  /**
   * @return bool
   */
  public function getSnapshotSources()
  {
    return $this->snapshotSources;
  }
  /**
   * TTL for the snapshot.
   *
   * @param string $ttl
   */
  public function setTtl($ttl)
  {
    $this->ttl = $ttl;
  }
  /**
   * @return string
   */
  public function getTtl()
  {
    return $this->ttl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SnapshotJobRequest::class, 'Google_Service_Dataflow_SnapshotJobRequest');
