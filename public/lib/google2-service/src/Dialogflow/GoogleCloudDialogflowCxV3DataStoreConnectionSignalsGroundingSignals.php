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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3DataStoreConnectionSignalsGroundingSignals extends \Google\Model
{
  /**
   * Decision not specified.
   */
  public const DECISION_GROUNDING_DECISION_UNSPECIFIED = 'GROUNDING_DECISION_UNSPECIFIED';
  /**
   * Grounding have accepted the answer.
   */
  public const DECISION_ACCEPTED_BY_GROUNDING = 'ACCEPTED_BY_GROUNDING';
  /**
   * Grounding have rejected the answer.
   */
  public const DECISION_REJECTED_BY_GROUNDING = 'REJECTED_BY_GROUNDING';
  /**
   * Score not specified.
   */
  public const SCORE_GROUNDING_SCORE_BUCKET_UNSPECIFIED = 'GROUNDING_SCORE_BUCKET_UNSPECIFIED';
  /**
   * We have very low confidence that the answer is grounded.
   */
  public const SCORE_VERY_LOW = 'VERY_LOW';
  /**
   * We have low confidence that the answer is grounded.
   */
  public const SCORE_LOW = 'LOW';
  /**
   * We have medium confidence that the answer is grounded.
   */
  public const SCORE_MEDIUM = 'MEDIUM';
  /**
   * We have high confidence that the answer is grounded.
   */
  public const SCORE_HIGH = 'HIGH';
  /**
   * We have very high confidence that the answer is grounded.
   */
  public const SCORE_VERY_HIGH = 'VERY_HIGH';
  /**
   * Represents the decision of the grounding check.
   *
   * @var string
   */
  public $decision;
  /**
   * Grounding score bucket setting.
   *
   * @var string
   */
  public $score;

  /**
   * Represents the decision of the grounding check.
   *
   * Accepted values: GROUNDING_DECISION_UNSPECIFIED, ACCEPTED_BY_GROUNDING,
   * REJECTED_BY_GROUNDING
   *
   * @param self::DECISION_* $decision
   */
  public function setDecision($decision)
  {
    $this->decision = $decision;
  }
  /**
   * @return self::DECISION_*
   */
  public function getDecision()
  {
    return $this->decision;
  }
  /**
   * Grounding score bucket setting.
   *
   * Accepted values: GROUNDING_SCORE_BUCKET_UNSPECIFIED, VERY_LOW, LOW, MEDIUM,
   * HIGH, VERY_HIGH
   *
   * @param self::SCORE_* $score
   */
  public function setScore($score)
  {
    $this->score = $score;
  }
  /**
   * @return self::SCORE_*
   */
  public function getScore()
  {
    return $this->score;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3DataStoreConnectionSignalsGroundingSignals::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3DataStoreConnectionSignalsGroundingSignals');
