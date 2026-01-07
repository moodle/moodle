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

class StorageObjectRetention extends \Google\Model
{
  /**
   * The bucket's object retention mode, can only be Unlocked or Locked.
   *
   * @var string
   */
  public $mode;
  /**
   * A time in RFC 3339 format until which object retention protects this
   * object.
   *
   * @var string
   */
  public $retainUntilTime;

  /**
   * The bucket's object retention mode, can only be Unlocked or Locked.
   *
   * @param string $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return string
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * A time in RFC 3339 format until which object retention protects this
   * object.
   *
   * @param string $retainUntilTime
   */
  public function setRetainUntilTime($retainUntilTime)
  {
    $this->retainUntilTime = $retainUntilTime;
  }
  /**
   * @return string
   */
  public function getRetainUntilTime()
  {
    return $this->retainUntilTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StorageObjectRetention::class, 'Google_Service_Storage_StorageObjectRetention');
