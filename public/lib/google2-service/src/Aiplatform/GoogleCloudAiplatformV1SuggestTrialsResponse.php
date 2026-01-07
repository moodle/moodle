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

class GoogleCloudAiplatformV1SuggestTrialsResponse extends \Google\Collection
{
  /**
   * The study state is unspecified.
   */
  public const STUDY_STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The study is active.
   */
  public const STUDY_STATE_ACTIVE = 'ACTIVE';
  /**
   * The study is stopped due to an internal error.
   */
  public const STUDY_STATE_INACTIVE = 'INACTIVE';
  /**
   * The study is done when the service exhausts the parameter search space or
   * max_trial_count is reached.
   */
  public const STUDY_STATE_COMPLETED = 'COMPLETED';
  protected $collection_key = 'trials';
  /**
   * The time at which operation processing completed.
   *
   * @var string
   */
  public $endTime;
  /**
   * The time at which the operation was started.
   *
   * @var string
   */
  public $startTime;
  /**
   * The state of the Study.
   *
   * @var string
   */
  public $studyState;
  protected $trialsType = GoogleCloudAiplatformV1Trial::class;
  protected $trialsDataType = 'array';

  /**
   * The time at which operation processing completed.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * The time at which the operation was started.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * The state of the Study.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, INACTIVE, COMPLETED
   *
   * @param self::STUDY_STATE_* $studyState
   */
  public function setStudyState($studyState)
  {
    $this->studyState = $studyState;
  }
  /**
   * @return self::STUDY_STATE_*
   */
  public function getStudyState()
  {
    return $this->studyState;
  }
  /**
   * A list of Trials.
   *
   * @param GoogleCloudAiplatformV1Trial[] $trials
   */
  public function setTrials($trials)
  {
    $this->trials = $trials;
  }
  /**
   * @return GoogleCloudAiplatformV1Trial[]
   */
  public function getTrials()
  {
    return $this->trials;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SuggestTrialsResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SuggestTrialsResponse');
