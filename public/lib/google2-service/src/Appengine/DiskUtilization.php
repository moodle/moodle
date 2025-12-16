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

namespace Google\Service\Appengine;

class DiskUtilization extends \Google\Model
{
  /**
   * Target bytes read per second.
   *
   * @var int
   */
  public $targetReadBytesPerSecond;
  /**
   * Target ops read per seconds.
   *
   * @var int
   */
  public $targetReadOpsPerSecond;
  /**
   * Target bytes written per second.
   *
   * @var int
   */
  public $targetWriteBytesPerSecond;
  /**
   * Target ops written per second.
   *
   * @var int
   */
  public $targetWriteOpsPerSecond;

  /**
   * Target bytes read per second.
   *
   * @param int $targetReadBytesPerSecond
   */
  public function setTargetReadBytesPerSecond($targetReadBytesPerSecond)
  {
    $this->targetReadBytesPerSecond = $targetReadBytesPerSecond;
  }
  /**
   * @return int
   */
  public function getTargetReadBytesPerSecond()
  {
    return $this->targetReadBytesPerSecond;
  }
  /**
   * Target ops read per seconds.
   *
   * @param int $targetReadOpsPerSecond
   */
  public function setTargetReadOpsPerSecond($targetReadOpsPerSecond)
  {
    $this->targetReadOpsPerSecond = $targetReadOpsPerSecond;
  }
  /**
   * @return int
   */
  public function getTargetReadOpsPerSecond()
  {
    return $this->targetReadOpsPerSecond;
  }
  /**
   * Target bytes written per second.
   *
   * @param int $targetWriteBytesPerSecond
   */
  public function setTargetWriteBytesPerSecond($targetWriteBytesPerSecond)
  {
    $this->targetWriteBytesPerSecond = $targetWriteBytesPerSecond;
  }
  /**
   * @return int
   */
  public function getTargetWriteBytesPerSecond()
  {
    return $this->targetWriteBytesPerSecond;
  }
  /**
   * Target ops written per second.
   *
   * @param int $targetWriteOpsPerSecond
   */
  public function setTargetWriteOpsPerSecond($targetWriteOpsPerSecond)
  {
    $this->targetWriteOpsPerSecond = $targetWriteOpsPerSecond;
  }
  /**
   * @return int
   */
  public function getTargetWriteOpsPerSecond()
  {
    return $this->targetWriteOpsPerSecond;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiskUtilization::class, 'Google_Service_Appengine_DiskUtilization');
