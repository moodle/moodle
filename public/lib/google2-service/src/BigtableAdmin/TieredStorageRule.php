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

namespace Google\Service\BigtableAdmin;

class TieredStorageRule extends \Google\Model
{
  /**
   * Include cells older than the given age. For the infrequent access tier,
   * this value must be at least 30 days.
   *
   * @var string
   */
  public $includeIfOlderThan;

  /**
   * Include cells older than the given age. For the infrequent access tier,
   * this value must be at least 30 days.
   *
   * @param string $includeIfOlderThan
   */
  public function setIncludeIfOlderThan($includeIfOlderThan)
  {
    $this->includeIfOlderThan = $includeIfOlderThan;
  }
  /**
   * @return string
   */
  public function getIncludeIfOlderThan()
  {
    return $this->includeIfOlderThan;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TieredStorageRule::class, 'Google_Service_BigtableAdmin_TieredStorageRule');
