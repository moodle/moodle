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

class SqlInstancesGetLatestRecoveryTimeResponse extends \Google\Model
{
  /**
   * Timestamp, identifies the earliest recovery time of the source instance.
   *
   * @var string
   */
  public $earliestRecoveryTime;
  /**
   * This is always `sql#getLatestRecoveryTime`.
   *
   * @var string
   */
  public $kind;
  /**
   * Timestamp, identifies the latest recovery time of the source instance.
   *
   * @var string
   */
  public $latestRecoveryTime;

  /**
   * Timestamp, identifies the earliest recovery time of the source instance.
   *
   * @param string $earliestRecoveryTime
   */
  public function setEarliestRecoveryTime($earliestRecoveryTime)
  {
    $this->earliestRecoveryTime = $earliestRecoveryTime;
  }
  /**
   * @return string
   */
  public function getEarliestRecoveryTime()
  {
    return $this->earliestRecoveryTime;
  }
  /**
   * This is always `sql#getLatestRecoveryTime`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Timestamp, identifies the latest recovery time of the source instance.
   *
   * @param string $latestRecoveryTime
   */
  public function setLatestRecoveryTime($latestRecoveryTime)
  {
    $this->latestRecoveryTime = $latestRecoveryTime;
  }
  /**
   * @return string
   */
  public function getLatestRecoveryTime()
  {
    return $this->latestRecoveryTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SqlInstancesGetLatestRecoveryTimeResponse::class, 'Google_Service_SQLAdmin_SqlInstancesGetLatestRecoveryTimeResponse');
