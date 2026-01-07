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

namespace Google\Service\VMMigrationService;

class Expiration extends \Google\Model
{
  /**
   * Output only. Timestamp of when this resource is considered expired.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Output only. Describes whether the expiration can be extended.
   *
   * @var bool
   */
  public $extendable;
  /**
   * Output only. The number of times expiration was extended.
   *
   * @var int
   */
  public $extensionCount;

  /**
   * Output only. Timestamp of when this resource is considered expired.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Output only. Describes whether the expiration can be extended.
   *
   * @param bool $extendable
   */
  public function setExtendable($extendable)
  {
    $this->extendable = $extendable;
  }
  /**
   * @return bool
   */
  public function getExtendable()
  {
    return $this->extendable;
  }
  /**
   * Output only. The number of times expiration was extended.
   *
   * @param int $extensionCount
   */
  public function setExtensionCount($extensionCount)
  {
    $this->extensionCount = $extensionCount;
  }
  /**
   * @return int
   */
  public function getExtensionCount()
  {
    return $this->extensionCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Expiration::class, 'Google_Service_VMMigrationService_Expiration');
