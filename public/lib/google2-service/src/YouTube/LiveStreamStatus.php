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

namespace Google\Service\YouTube;

class LiveStreamStatus extends \Google\Model
{
  public const STREAM_STATUS_created = 'created';
  public const STREAM_STATUS_ready = 'ready';
  public const STREAM_STATUS_active = 'active';
  public const STREAM_STATUS_inactive = 'inactive';
  public const STREAM_STATUS_error = 'error';
  protected $healthStatusType = LiveStreamHealthStatus::class;
  protected $healthStatusDataType = '';
  /**
   * @var string
   */
  public $streamStatus;

  /**
   * The health status of the stream.
   *
   * @param LiveStreamHealthStatus $healthStatus
   */
  public function setHealthStatus(LiveStreamHealthStatus $healthStatus)
  {
    $this->healthStatus = $healthStatus;
  }
  /**
   * @return LiveStreamHealthStatus
   */
  public function getHealthStatus()
  {
    return $this->healthStatus;
  }
  /**
   * @param self::STREAM_STATUS_* $streamStatus
   */
  public function setStreamStatus($streamStatus)
  {
    $this->streamStatus = $streamStatus;
  }
  /**
   * @return self::STREAM_STATUS_*
   */
  public function getStreamStatus()
  {
    return $this->streamStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LiveStreamStatus::class, 'Google_Service_YouTube_LiveStreamStatus');
