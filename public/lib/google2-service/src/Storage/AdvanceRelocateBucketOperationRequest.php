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

namespace Google\Service\Storage;

class AdvanceRelocateBucketOperationRequest extends \Google\Model
{
  /**
   * Specifies the time when the relocation will revert to the sync stage if the
   * relocation hasn't succeeded.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Specifies the duration after which the relocation will revert to the sync
   * stage if the relocation hasn't succeeded. Optional, if not supplied, a
   * default value of 12h will be used.
   *
   * @var string
   */
  public $ttl;

  /**
   * Specifies the time when the relocation will revert to the sync stage if the
   * relocation hasn't succeeded.
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
   * Specifies the duration after which the relocation will revert to the sync
   * stage if the relocation hasn't succeeded. Optional, if not supplied, a
   * default value of 12h will be used.
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
class_alias(AdvanceRelocateBucketOperationRequest::class, 'Google_Service_Storage_AdvanceRelocateBucketOperationRequest');
