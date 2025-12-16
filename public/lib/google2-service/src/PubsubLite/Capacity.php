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

namespace Google\Service\PubsubLite;

class Capacity extends \Google\Model
{
  /**
   * Publish throughput capacity per partition in MiB/s. Must be >= 4 and <= 16.
   *
   * @var int
   */
  public $publishMibPerSec;
  /**
   * Subscribe throughput capacity per partition in MiB/s. Must be >= 4 and <=
   * 32.
   *
   * @var int
   */
  public $subscribeMibPerSec;

  /**
   * Publish throughput capacity per partition in MiB/s. Must be >= 4 and <= 16.
   *
   * @param int $publishMibPerSec
   */
  public function setPublishMibPerSec($publishMibPerSec)
  {
    $this->publishMibPerSec = $publishMibPerSec;
  }
  /**
   * @return int
   */
  public function getPublishMibPerSec()
  {
    return $this->publishMibPerSec;
  }
  /**
   * Subscribe throughput capacity per partition in MiB/s. Must be >= 4 and <=
   * 32.
   *
   * @param int $subscribeMibPerSec
   */
  public function setSubscribeMibPerSec($subscribeMibPerSec)
  {
    $this->subscribeMibPerSec = $subscribeMibPerSec;
  }
  /**
   * @return int
   */
  public function getSubscribeMibPerSec()
  {
    return $this->subscribeMibPerSec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Capacity::class, 'Google_Service_PubsubLite_Capacity');
