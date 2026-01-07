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

namespace Google\Service\DisplayVideo;

class CustomBiddingModelDetails extends \Google\Model
{
  /**
   * State is not specified or is unknown in this version.
   */
  public const READINESS_STATE_READINESS_STATE_UNSPECIFIED = 'READINESS_STATE_UNSPECIFIED';
  /**
   * The model is trained and ready for serving.
   */
  public const READINESS_STATE_READINESS_STATE_ACTIVE = 'READINESS_STATE_ACTIVE';
  /**
   * There is not enough data to train the serving model.
   */
  public const READINESS_STATE_READINESS_STATE_INSUFFICIENT_DATA = 'READINESS_STATE_INSUFFICIENT_DATA';
  /**
   * The model is training and not ready for serving.
   */
  public const READINESS_STATE_READINESS_STATE_TRAINING = 'READINESS_STATE_TRAINING';
  /**
   * A valid custom bidding script has not been provided with which to train the
   * model. This state will only be applied to algorithms whose
   * `custom_bidding_algorithm_type` is `SCRIPT_BASED`.
   */
  public const READINESS_STATE_READINESS_STATE_NO_VALID_SCRIPT = 'READINESS_STATE_NO_VALID_SCRIPT';
  /**
   * A valid script was provided but failed evaluation. This is applicable for
   * scripts that could not be evaluated in the alloted time.
   */
  public const READINESS_STATE_READINESS_STATE_EVALUATION_FAILURE = 'READINESS_STATE_EVALUATION_FAILURE';
  /**
   * State is not specified or is unknown in this version.
   */
  public const SUSPENSION_STATE_SUSPENSION_STATE_UNSPECIFIED = 'SUSPENSION_STATE_UNSPECIFIED';
  /**
   * Model is enabled, either recently used, currently used or scheduled to be
   * used. The algorithm is actively scoring impressions for this advertiser.
   */
  public const SUSPENSION_STATE_SUSPENSION_STATE_ENABLED = 'SUSPENSION_STATE_ENABLED';
  /**
   * Model has not been used recently. Although the model still acts as
   * `ENABLED`, it will eventually be suspended if not used.
   */
  public const SUSPENSION_STATE_SUSPENSION_STATE_DORMANT = 'SUSPENSION_STATE_DORMANT';
  /**
   * Model is suspended from scoring impressions and cannot serve. If the
   * algorithm is assigned to a line item under this advertiser or otherwise
   * updated, it will switch back to the `ENABLED` state and require time to
   * prepare the serving model again.
   */
  public const SUSPENSION_STATE_SUSPENSION_STATE_SUSPENDED = 'SUSPENSION_STATE_SUSPENDED';
  /**
   * The unique ID of the relevant advertiser.
   *
   * @var string
   */
  public $advertiserId;
  /**
   * The readiness state of custom bidding model.
   *
   * @var string
   */
  public $readinessState;
  /**
   * Output only. The suspension state of custom bidding model.
   *
   * @var string
   */
  public $suspensionState;

  /**
   * The unique ID of the relevant advertiser.
   *
   * @param string $advertiserId
   */
  public function setAdvertiserId($advertiserId)
  {
    $this->advertiserId = $advertiserId;
  }
  /**
   * @return string
   */
  public function getAdvertiserId()
  {
    return $this->advertiserId;
  }
  /**
   * The readiness state of custom bidding model.
   *
   * Accepted values: READINESS_STATE_UNSPECIFIED, READINESS_STATE_ACTIVE,
   * READINESS_STATE_INSUFFICIENT_DATA, READINESS_STATE_TRAINING,
   * READINESS_STATE_NO_VALID_SCRIPT, READINESS_STATE_EVALUATION_FAILURE
   *
   * @param self::READINESS_STATE_* $readinessState
   */
  public function setReadinessState($readinessState)
  {
    $this->readinessState = $readinessState;
  }
  /**
   * @return self::READINESS_STATE_*
   */
  public function getReadinessState()
  {
    return $this->readinessState;
  }
  /**
   * Output only. The suspension state of custom bidding model.
   *
   * Accepted values: SUSPENSION_STATE_UNSPECIFIED, SUSPENSION_STATE_ENABLED,
   * SUSPENSION_STATE_DORMANT, SUSPENSION_STATE_SUSPENDED
   *
   * @param self::SUSPENSION_STATE_* $suspensionState
   */
  public function setSuspensionState($suspensionState)
  {
    $this->suspensionState = $suspensionState;
  }
  /**
   * @return self::SUSPENSION_STATE_*
   */
  public function getSuspensionState()
  {
    return $this->suspensionState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomBiddingModelDetails::class, 'Google_Service_DisplayVideo_CustomBiddingModelDetails');
