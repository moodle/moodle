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

namespace Google\Service\Firestore;

class Precondition extends \Google\Model
{
  /**
   * When set to `true`, the target document must exist. When set to `false`,
   * the target document must not exist.
   *
   * @var bool
   */
  public $exists;
  /**
   * When set, the target document must exist and have been last updated at that
   * time. Timestamp must be microsecond aligned.
   *
   * @var string
   */
  public $updateTime;

  /**
   * When set to `true`, the target document must exist. When set to `false`,
   * the target document must not exist.
   *
   * @param bool $exists
   */
  public function setExists($exists)
  {
    $this->exists = $exists;
  }
  /**
   * @return bool
   */
  public function getExists()
  {
    return $this->exists;
  }
  /**
   * When set, the target document must exist and have been last updated at that
   * time. Timestamp must be microsecond aligned.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Precondition::class, 'Google_Service_Firestore_Precondition');
