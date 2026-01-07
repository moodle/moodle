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

class ReplicaSelection extends \Google\Model
{
  /**
   * Not specified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Read-write replicas support both reads and writes.
   */
  public const TYPE_READ_WRITE = 'READ_WRITE';
  /**
   * Read-only replicas only support reads (not writes).
   */
  public const TYPE_READ_ONLY = 'READ_ONLY';
  /**
   * The location or region of the serving requests, for example, "us-east1".
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
   * The location or region of the serving requests, for example, "us-east1".
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
   * Accepted values: TYPE_UNSPECIFIED, READ_WRITE, READ_ONLY
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
class_alias(ReplicaSelection::class, 'Google_Service_Spanner_ReplicaSelection');
