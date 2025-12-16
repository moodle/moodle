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

namespace Google\Service\SQLAdmin;

class DatabaseInstanceFailoverReplica extends \Google\Model
{
  /**
   * The availability status of the failover replica. A false status indicates
   * that the failover replica is out of sync. The primary instance can only
   * failover to the failover replica when the status is true.
   *
   * @var bool
   */
  public $available;
  /**
   * The name of the failover replica. If specified at instance creation, a
   * failover replica is created for the instance. The name doesn't include the
   * project ID.
   *
   * @var string
   */
  public $name;

  /**
   * The availability status of the failover replica. A false status indicates
   * that the failover replica is out of sync. The primary instance can only
   * failover to the failover replica when the status is true.
   *
   * @param bool $available
   */
  public function setAvailable($available)
  {
    $this->available = $available;
  }
  /**
   * @return bool
   */
  public function getAvailable()
  {
    return $this->available;
  }
  /**
   * The name of the failover replica. If specified at instance creation, a
   * failover replica is created for the instance. The name doesn't include the
   * project ID.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatabaseInstanceFailoverReplica::class, 'Google_Service_SQLAdmin_DatabaseInstanceFailoverReplica');
