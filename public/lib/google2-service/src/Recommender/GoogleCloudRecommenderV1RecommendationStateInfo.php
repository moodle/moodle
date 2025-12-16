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

class GoogleCloudRecommenderV1RecommendationStateInfo extends \Google\Model
{
  /**
   * Default state. Don't use directly.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Recommendation is active and can be applied. Recommendations content can be
   * updated by Google. ACTIVE recommendations can be marked as CLAIMED,
   * SUCCEEDED, or FAILED.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Recommendation is in claimed state. Recommendations content is immutable
   * and cannot be updated by Google. CLAIMED recommendations can be marked as
   * CLAIMED, SUCCEEDED, or FAILED.
   */
  public const STATE_CLAIMED = 'CLAIMED';
  /**
   * Recommendation is in succeeded state. Recommendations content is immutable
   * and cannot be updated by Google. SUCCEEDED recommendations can be marked as
   * SUCCEEDED, or FAILED.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Recommendation is in failed state. Recommendations content is immutable and
   * cannot be updated by Google. FAILED recommendations can be marked as
   * SUCCEEDED, or FAILED.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Recommendation is in dismissed state. Recommendation content can be updated
   * by Google. DISMISSED recommendations can be marked as ACTIVE.
   */
  public const STATE_DISMISSED = 'DISMISSED';
  /**
   * The state of the recommendation, Eg ACTIVE, SUCCEEDED, FAILED.
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
   * The state of the recommendation, Eg ACTIVE, SUCCEEDED, FAILED.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, CLAIMED, SUCCEEDED, FAILED,
   * DISMISSED
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
class_alias(GoogleCloudRecommenderV1RecommendationStateInfo::class, 'Google_Service_Recommender_GoogleCloudRecommenderV1RecommendationStateInfo');
