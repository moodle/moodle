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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1StudySpecStudyStoppingConfig extends \Google\Model
{
  /**
   * If the objective value has not improved for this much time, stop the study.
   * WARNING: Effective only for single-objective studies.
   *
   * @var string
   */
  public $maxDurationNoProgress;
  /**
   * If there are more than this many trials, stop the study.
   *
   * @var int
   */
  public $maxNumTrials;
  /**
   * If the objective value has not improved for this many consecutive trials,
   * stop the study. WARNING: Effective only for single-objective studies.
   *
   * @var int
   */
  public $maxNumTrialsNoProgress;
  protected $maximumRuntimeConstraintType = GoogleCloudAiplatformV1StudyTimeConstraint::class;
  protected $maximumRuntimeConstraintDataType = '';
  /**
   * If there are fewer than this many COMPLETED trials, do not stop the study.
   *
   * @var int
   */
  public $minNumTrials;
  protected $minimumRuntimeConstraintType = GoogleCloudAiplatformV1StudyTimeConstraint::class;
  protected $minimumRuntimeConstraintDataType = '';
  /**
   * If true, a Study enters STOPPING_ASAP whenever it would normally enters
   * STOPPING state. The bottom line is: set to true if you want to interrupt
   * on-going evaluations of Trials as soon as the study stopping condition is
   * met. (Please see Study.State documentation for the source of truth).
   *
   * @var bool
   */
  public $shouldStopAsap;

  /**
   * If the objective value has not improved for this much time, stop the study.
   * WARNING: Effective only for single-objective studies.
   *
   * @param string $maxDurationNoProgress
   */
  public function setMaxDurationNoProgress($maxDurationNoProgress)
  {
    $this->maxDurationNoProgress = $maxDurationNoProgress;
  }
  /**
   * @return string
   */
  public function getMaxDurationNoProgress()
  {
    return $this->maxDurationNoProgress;
  }
  /**
   * If there are more than this many trials, stop the study.
   *
   * @param int $maxNumTrials
   */
  public function setMaxNumTrials($maxNumTrials)
  {
    $this->maxNumTrials = $maxNumTrials;
  }
  /**
   * @return int
   */
  public function getMaxNumTrials()
  {
    return $this->maxNumTrials;
  }
  /**
   * If the objective value has not improved for this many consecutive trials,
   * stop the study. WARNING: Effective only for single-objective studies.
   *
   * @param int $maxNumTrialsNoProgress
   */
  public function setMaxNumTrialsNoProgress($maxNumTrialsNoProgress)
  {
    $this->maxNumTrialsNoProgress = $maxNumTrialsNoProgress;
  }
  /**
   * @return int
   */
  public function getMaxNumTrialsNoProgress()
  {
    return $this->maxNumTrialsNoProgress;
  }
  /**
   * If the specified time or duration has passed, stop the study.
   *
   * @param GoogleCloudAiplatformV1StudyTimeConstraint $maximumRuntimeConstraint
   */
  public function setMaximumRuntimeConstraint(GoogleCloudAiplatformV1StudyTimeConstraint $maximumRuntimeConstraint)
  {
    $this->maximumRuntimeConstraint = $maximumRuntimeConstraint;
  }
  /**
   * @return GoogleCloudAiplatformV1StudyTimeConstraint
   */
  public function getMaximumRuntimeConstraint()
  {
    return $this->maximumRuntimeConstraint;
  }
  /**
   * If there are fewer than this many COMPLETED trials, do not stop the study.
   *
   * @param int $minNumTrials
   */
  public function setMinNumTrials($minNumTrials)
  {
    $this->minNumTrials = $minNumTrials;
  }
  /**
   * @return int
   */
  public function getMinNumTrials()
  {
    return $this->minNumTrials;
  }
  /**
   * Each "stopping rule" in this proto specifies an "if" condition. Before
   * Vizier would generate a new suggestion, it first checks each specified
   * stopping rule, from top to bottom in this list. Note that the first few
   * rules (e.g. minimum_runtime_constraint, min_num_trials) will prevent other
   * stopping rules from being evaluated until they are met. For example,
   * setting `min_num_trials=5` and `always_stop_after= 1 hour` means that the
   * Study will ONLY stop after it has 5 COMPLETED trials, even if more than an
   * hour has passed since its creation. It follows the first applicable rule
   * (whose "if" condition is satisfied) to make a stopping decision. If none of
   * the specified rules are applicable, then Vizier decides that the study
   * should not stop. If Vizier decides that the study should stop, the study
   * enters STOPPING state (or STOPPING_ASAP if should_stop_asap = true).
   * IMPORTANT: The automatic study state transition happens precisely as
   * described above; that is, deleting trials or updating StudyConfig NEVER
   * automatically moves the study state back to ACTIVE. If you want to _resume_
   * a Study that was stopped, 1) change the stopping conditions if necessary,
   * 2) activate the study, and then 3) ask for suggestions. If the specified
   * time or duration has not passed, do not stop the study.
   *
   * @param GoogleCloudAiplatformV1StudyTimeConstraint $minimumRuntimeConstraint
   */
  public function setMinimumRuntimeConstraint(GoogleCloudAiplatformV1StudyTimeConstraint $minimumRuntimeConstraint)
  {
    $this->minimumRuntimeConstraint = $minimumRuntimeConstraint;
  }
  /**
   * @return GoogleCloudAiplatformV1StudyTimeConstraint
   */
  public function getMinimumRuntimeConstraint()
  {
    return $this->minimumRuntimeConstraint;
  }
  /**
   * If true, a Study enters STOPPING_ASAP whenever it would normally enters
   * STOPPING state. The bottom line is: set to true if you want to interrupt
   * on-going evaluations of Trials as soon as the study stopping condition is
   * met. (Please see Study.State documentation for the source of truth).
   *
   * @param bool $shouldStopAsap
   */
  public function setShouldStopAsap($shouldStopAsap)
  {
    $this->shouldStopAsap = $shouldStopAsap;
  }
  /**
   * @return bool
   */
  public function getShouldStopAsap()
  {
    return $this->shouldStopAsap;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1StudySpecStudyStoppingConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1StudySpecStudyStoppingConfig');
