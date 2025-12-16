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

namespace Google\Service\PolicySimulator;

class GoogleCloudPolicysimulatorV1ReplayConfig extends \Google\Model
{
  /**
   * An unspecified log source. If the log source is unspecified, the Replay
   * defaults to using `RECENT_ACCESSES`.
   */
  public const LOG_SOURCE_LOG_SOURCE_UNSPECIFIED = 'LOG_SOURCE_UNSPECIFIED';
  /**
   * All access logs from the last 90 days. These logs may not include logs from
   * the most recent 7 days.
   */
  public const LOG_SOURCE_RECENT_ACCESSES = 'RECENT_ACCESSES';
  /**
   * The logs to use as input for the Replay.
   *
   * @var string
   */
  public $logSource;
  protected $policyOverlayType = GoogleIamV1Policy::class;
  protected $policyOverlayDataType = 'map';

  /**
   * The logs to use as input for the Replay.
   *
   * Accepted values: LOG_SOURCE_UNSPECIFIED, RECENT_ACCESSES
   *
   * @param self::LOG_SOURCE_* $logSource
   */
  public function setLogSource($logSource)
  {
    $this->logSource = $logSource;
  }
  /**
   * @return self::LOG_SOURCE_*
   */
  public function getLogSource()
  {
    return $this->logSource;
  }
  /**
   * A mapping of the resources that you want to simulate policies for and the
   * policies that you want to simulate. Keys are the full resource names for
   * the resources. For example,
   * `//cloudresourcemanager.googleapis.com/projects/my-project`. For examples
   * of full resource names for Google Cloud services, see
   * https://cloud.google.com/iam/help/troubleshooter/full-resource-names.
   * Values are Policy objects representing the policies that you want to
   * simulate. Replays automatically take into account any IAM policies
   * inherited through the resource hierarchy, and any policies set on
   * descendant resources. You do not need to include these policies in the
   * policy overlay.
   *
   * @param GoogleIamV1Policy[] $policyOverlay
   */
  public function setPolicyOverlay($policyOverlay)
  {
    $this->policyOverlay = $policyOverlay;
  }
  /**
   * @return GoogleIamV1Policy[]
   */
  public function getPolicyOverlay()
  {
    return $this->policyOverlay;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudPolicysimulatorV1ReplayConfig::class, 'Google_Service_PolicySimulator_GoogleCloudPolicysimulatorV1ReplayConfig');
