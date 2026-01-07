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

namespace Google\Service\CloudComposer;

class AirflowMetadataRetentionPolicyConfig extends \Google\Model
{
  /**
   * Default mode doesn't change environment parameters.
   */
  public const RETENTION_MODE_RETENTION_MODE_UNSPECIFIED = 'RETENTION_MODE_UNSPECIFIED';
  /**
   * Retention policy is enabled.
   */
  public const RETENTION_MODE_RETENTION_MODE_ENABLED = 'RETENTION_MODE_ENABLED';
  /**
   * Retention policy is disabled.
   */
  public const RETENTION_MODE_RETENTION_MODE_DISABLED = 'RETENTION_MODE_DISABLED';
  /**
   * Optional. How many days data should be retained for.
   *
   * @var int
   */
  public $retentionDays;
  /**
   * Optional. Retention can be either enabled or disabled.
   *
   * @var string
   */
  public $retentionMode;

  /**
   * Optional. How many days data should be retained for.
   *
   * @param int $retentionDays
   */
  public function setRetentionDays($retentionDays)
  {
    $this->retentionDays = $retentionDays;
  }
  /**
   * @return int
   */
  public function getRetentionDays()
  {
    return $this->retentionDays;
  }
  /**
   * Optional. Retention can be either enabled or disabled.
   *
   * Accepted values: RETENTION_MODE_UNSPECIFIED, RETENTION_MODE_ENABLED,
   * RETENTION_MODE_DISABLED
   *
   * @param self::RETENTION_MODE_* $retentionMode
   */
  public function setRetentionMode($retentionMode)
  {
    $this->retentionMode = $retentionMode;
  }
  /**
   * @return self::RETENTION_MODE_*
   */
  public function getRetentionMode()
  {
    return $this->retentionMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AirflowMetadataRetentionPolicyConfig::class, 'Google_Service_CloudComposer_AirflowMetadataRetentionPolicyConfig');
