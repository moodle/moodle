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

namespace Google\Service\Config;

class LockInfo extends \Google\Model
{
  /**
   * Time that the lock was taken.
   *
   * @var string
   */
  public $createTime;
  /**
   * Extra information to store with the lock, provided by the caller.
   *
   * @var string
   */
  public $info;
  /**
   * Unique ID for the lock to be overridden with generation ID in the backend.
   *
   * @var string
   */
  public $lockId;
  /**
   * Terraform operation, provided by the caller.
   *
   * @var string
   */
  public $operation;
  /**
   * Terraform version
   *
   * @var string
   */
  public $version;
  /**
   * user@hostname when available
   *
   * @var string
   */
  public $who;

  /**
   * Time that the lock was taken.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Extra information to store with the lock, provided by the caller.
   *
   * @param string $info
   */
  public function setInfo($info)
  {
    $this->info = $info;
  }
  /**
   * @return string
   */
  public function getInfo()
  {
    return $this->info;
  }
  /**
   * Unique ID for the lock to be overridden with generation ID in the backend.
   *
   * @param string $lockId
   */
  public function setLockId($lockId)
  {
    $this->lockId = $lockId;
  }
  /**
   * @return string
   */
  public function getLockId()
  {
    return $this->lockId;
  }
  /**
   * Terraform operation, provided by the caller.
   *
   * @param string $operation
   */
  public function setOperation($operation)
  {
    $this->operation = $operation;
  }
  /**
   * @return string
   */
  public function getOperation()
  {
    return $this->operation;
  }
  /**
   * Terraform version
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
  /**
   * user@hostname when available
   *
   * @param string $who
   */
  public function setWho($who)
  {
    $this->who = $who;
  }
  /**
   * @return string
   */
  public function getWho()
  {
    return $this->who;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LockInfo::class, 'Google_Service_Config_LockInfo');
