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

namespace Google\Service\DataFusion;

class Accelerator extends \Google\Model
{
  /**
   * Default value, if unspecified.
   */
  public const ACCELERATOR_TYPE_ACCELERATOR_TYPE_UNSPECIFIED = 'ACCELERATOR_TYPE_UNSPECIFIED';
  /**
   * Change Data Capture accelerator for Cloud Data Fusion.
   */
  public const ACCELERATOR_TYPE_CDC = 'CDC';
  /**
   * Reserved for internal use.
   */
  public const ACCELERATOR_TYPE_HEALTHCARE = 'HEALTHCARE';
  /**
   * Contact Center AI Insights This accelerator is used to enable import and
   * export pipelines custom built to streamline CCAI Insights processing.
   */
  public const ACCELERATOR_TYPE_CCAI_INSIGHTS = 'CCAI_INSIGHTS';
  /**
   * Reserved for internal use.
   */
  public const ACCELERATOR_TYPE_CLOUDSEARCH = 'CLOUDSEARCH';
  /**
   * Default value, do not use.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Indicates that the accelerator is enabled and available to use.
   */
  public const STATE_ENABLED = 'ENABLED';
  /**
   * Indicates that the accelerator is disabled and not available to use.
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * Indicates that accelerator state is currently unknown. Requests for enable,
   * disable could be retried while in this state.
   */
  public const STATE_UNKNOWN = 'UNKNOWN';
  /**
   * Optional. The type of an accelator for a Cloud Data Fusion instance.
   *
   * @var string
   */
  public $acceleratorType;
  /**
   * Output only. The state of the accelerator.
   *
   * @var string
   */
  public $state;

  /**
   * Optional. The type of an accelator for a Cloud Data Fusion instance.
   *
   * Accepted values: ACCELERATOR_TYPE_UNSPECIFIED, CDC, HEALTHCARE,
   * CCAI_INSIGHTS, CLOUDSEARCH
   *
   * @param self::ACCELERATOR_TYPE_* $acceleratorType
   */
  public function setAcceleratorType($acceleratorType)
  {
    $this->acceleratorType = $acceleratorType;
  }
  /**
   * @return self::ACCELERATOR_TYPE_*
   */
  public function getAcceleratorType()
  {
    return $this->acceleratorType;
  }
  /**
   * Output only. The state of the accelerator.
   *
   * Accepted values: STATE_UNSPECIFIED, ENABLED, DISABLED, UNKNOWN
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Accelerator::class, 'Google_Service_DataFusion_Accelerator');
