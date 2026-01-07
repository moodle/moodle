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

namespace Google\Service\Recommender;

class GoogleCloudRecommenderV1InsightStateInfo extends \Google\Model
{
  /**
   * Unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Insight is active. Content for ACTIVE insights can be updated by Google.
   * ACTIVE insights can be marked DISMISSED OR ACCEPTED.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Some action has been taken based on this insight. Insights become accepted
   * when a recommendation derived from the insight has been marked CLAIMED,
   * SUCCEEDED, or FAILED. ACTIVE insights can also be marked ACCEPTED
   * explicitly. Content for ACCEPTED insights is immutable. ACCEPTED insights
   * can only be marked ACCEPTED (which may update state metadata).
   */
  public const STATE_ACCEPTED = 'ACCEPTED';
  /**
   * Insight is dismissed. Content for DISMISSED insights can be updated by
   * Google. DISMISSED insights can be marked as ACTIVE.
   */
  public const STATE_DISMISSED = 'DISMISSED';
  /**
   * Insight state.
   *
   * @var string
   */
  public $state;
  /**
   * A map of metadata for the state, provided by user or automations systems.
   *
   * @var string[]
   */
  public $stateMetadata;

  /**
   * Insight state.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, ACCEPTED, DISMISSED
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
  /**
   * A map of metadata for the state, provided by user or automations systems.
   *
   * @param string[] $stateMetadata
   */
  public function setStateMetadata($stateMetadata)
  {
    $this->stateMetadata = $stateMetadata;
  }
  /**
   * @return string[]
   */
  public function getStateMetadata()
  {
    return $this->stateMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecommenderV1InsightStateInfo::class, 'Google_Service_Recommender_GoogleCloudRecommenderV1InsightStateInfo');
