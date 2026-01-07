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

class VideoAdSequenceStep extends \Google\Model
{
  /**
   * Unspecified or unknown
   */
  public const INTERACTION_TYPE_INTERACTION_TYPE_UNSPECIFIED = 'INTERACTION_TYPE_UNSPECIFIED';
  /**
   * A paid view.
   */
  public const INTERACTION_TYPE_INTERACTION_TYPE_PAID_VIEW = 'INTERACTION_TYPE_PAID_VIEW';
  /**
   * Skipped by the viewer.
   */
  public const INTERACTION_TYPE_INTERACTION_TYPE_SKIP = 'INTERACTION_TYPE_SKIP';
  /**
   * A (viewed) ad impression.
   */
  public const INTERACTION_TYPE_INTERACTION_TYPE_IMPRESSION = 'INTERACTION_TYPE_IMPRESSION';
  /**
   * An ad impression that was not immediately skipped by the viewer, but didn't
   * reach the billable event either.
   */
  public const INTERACTION_TYPE_INTERACTION_TYPE_ENGAGED_IMPRESSION = 'INTERACTION_TYPE_ENGAGED_IMPRESSION';
  /**
   * The ID of the corresponding ad group of the step.
   *
   * @var string
   */
  public $adGroupId;
  /**
   * The interaction on the previous step that will lead the viewer to this
   * step. The first step does not have interaction_type.
   *
   * @var string
   */
  public $interactionType;
  /**
   * The ID of the previous step. The first step does not have previous step.
   *
   * @var string
   */
  public $previousStepId;
  /**
   * The ID of the step.
   *
   * @var string
   */
  public $stepId;

  /**
   * The ID of the corresponding ad group of the step.
   *
   * @param string $adGroupId
   */
  public function setAdGroupId($adGroupId)
  {
    $this->adGroupId = $adGroupId;
  }
  /**
   * @return string
   */
  public function getAdGroupId()
  {
    return $this->adGroupId;
  }
  /**
   * The interaction on the previous step that will lead the viewer to this
   * step. The first step does not have interaction_type.
   *
   * Accepted values: INTERACTION_TYPE_UNSPECIFIED, INTERACTION_TYPE_PAID_VIEW,
   * INTERACTION_TYPE_SKIP, INTERACTION_TYPE_IMPRESSION,
   * INTERACTION_TYPE_ENGAGED_IMPRESSION
   *
   * @param self::INTERACTION_TYPE_* $interactionType
   */
  public function setInteractionType($interactionType)
  {
    $this->interactionType = $interactionType;
  }
  /**
   * @return self::INTERACTION_TYPE_*
   */
  public function getInteractionType()
  {
    return $this->interactionType;
  }
  /**
   * The ID of the previous step. The first step does not have previous step.
   *
   * @param string $previousStepId
   */
  public function setPreviousStepId($previousStepId)
  {
    $this->previousStepId = $previousStepId;
  }
  /**
   * @return string
   */
  public function getPreviousStepId()
  {
    return $this->previousStepId;
  }
  /**
   * The ID of the step.
   *
   * @param string $stepId
   */
  public function setStepId($stepId)
  {
    $this->stepId = $stepId;
  }
  /**
   * @return string
   */
  public function getStepId()
  {
    return $this->stepId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VideoAdSequenceStep::class, 'Google_Service_DisplayVideo_VideoAdSequenceStep');
