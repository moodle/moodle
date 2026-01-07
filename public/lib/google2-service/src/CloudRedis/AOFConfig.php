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

namespace Google\Service\CloudRedis;

class AOFConfig extends \Google\Model
{
  /**
   * Not set. Default: EVERYSEC
   */
  public const APPEND_FSYNC_APPEND_FSYNC_UNSPECIFIED = 'APPEND_FSYNC_UNSPECIFIED';
  /**
   * Never fsync. Normally Linux will flush data every 30 seconds with this
   * configuration, but it's up to the kernel's exact tuning.
   */
  public const APPEND_FSYNC_NO = 'NO';
  /**
   * fsync every second. Fast enough, and you may lose 1 second of data if there
   * is a disaster
   */
  public const APPEND_FSYNC_EVERYSEC = 'EVERYSEC';
  /**
   * fsync every time new write commands are appended to the AOF. It has the
   * best data loss protection at the cost of performance
   */
  public const APPEND_FSYNC_ALWAYS = 'ALWAYS';
  /**
   * Optional. fsync configuration.
   *
   * @var string
   */
  public $appendFsync;

  /**
   * Optional. fsync configuration.
   *
   * Accepted values: APPEND_FSYNC_UNSPECIFIED, NO, EVERYSEC, ALWAYS
   *
   * @param self::APPEND_FSYNC_* $appendFsync
   */
  public function setAppendFsync($appendFsync)
  {
    $this->appendFsync = $appendFsync;
  }
  /**
   * @return self::APPEND_FSYNC_*
   */
  public function getAppendFsync()
  {
    return $this->appendFsync;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AOFConfig::class, 'Google_Service_CloudRedis_AOFConfig');
