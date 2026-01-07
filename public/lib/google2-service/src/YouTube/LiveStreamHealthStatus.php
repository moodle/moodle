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

class LiveStreamHealthStatus extends \Google\Collection
{
  public const STATUS_good = 'good';
  public const STATUS_ok = 'ok';
  public const STATUS_bad = 'bad';
  public const STATUS_noData = 'noData';
  public const STATUS_revoked = 'revoked';
  protected $collection_key = 'configurationIssues';
  protected $configurationIssuesType = LiveStreamConfigurationIssue::class;
  protected $configurationIssuesDataType = 'array';
  /**
   * The last time this status was updated (in seconds)
   *
   * @var string
   */
  public $lastUpdateTimeSeconds;
  /**
   * The status code of this stream
   *
   * @var string
   */
  public $status;

  /**
   * The configurations issues on this stream
   *
   * @param LiveStreamConfigurationIssue[] $configurationIssues
   */
  public function setConfigurationIssues($configurationIssues)
  {
    $this->configurationIssues = $configurationIssues;
  }
  /**
   * @return LiveStreamConfigurationIssue[]
   */
  public function getConfigurationIssues()
  {
    return $this->configurationIssues;
  }
  /**
   * The last time this status was updated (in seconds)
   *
   * @param string $lastUpdateTimeSeconds
   */
  public function setLastUpdateTimeSeconds($lastUpdateTimeSeconds)
  {
    $this->lastUpdateTimeSeconds = $lastUpdateTimeSeconds;
  }
  /**
   * @return string
   */
  public function getLastUpdateTimeSeconds()
  {
    return $this->lastUpdateTimeSeconds;
  }
  /**
   * The status code of this stream
   *
   * Accepted values: good, ok, bad, noData, revoked
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LiveStreamHealthStatus::class, 'Google_Service_YouTube_LiveStreamHealthStatus');
