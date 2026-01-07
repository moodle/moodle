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

class GoogleCloudAiplatformV1SchemaTrainingjobDefinitionHyperparameterTuningJobSpec extends \Google\Model
{
  /**
   * The number of failed Trials that need to be seen before failing the
   * HyperparameterTuningJob. If set to 0, Vertex AI decides how many Trials
   * must fail before the whole job fails.
   *
   * @var int
   */
  public $maxFailedTrialCount;
  /**
   * The desired total number of Trials.
   *
   * @var int
   */
  public $maxTrialCount;
  /**
   * The desired number of Trials to run in parallel.
   *
   * @var int
   */
  public $parallelTrialCount;
  protected $studySpecType = GoogleCloudAiplatformV1StudySpec::class;
  protected $studySpecDataType = '';
  protected $trialJobSpecType = GoogleCloudAiplatformV1CustomJobSpec::class;
  protected $trialJobSpecDataType = '';

  /**
   * The number of failed Trials that need to be seen before failing the
   * HyperparameterTuningJob. If set to 0, Vertex AI decides how many Trials
   * must fail before the whole job fails.
   *
   * @param int $maxFailedTrialCount
   */
  public function setMaxFailedTrialCount($maxFailedTrialCount)
  {
    $this->maxFailedTrialCount = $maxFailedTrialCount;
  }
  /**
   * @return int
   */
  public function getMaxFailedTrialCount()
  {
    return $this->maxFailedTrialCount;
  }
  /**
   * The desired total number of Trials.
   *
   * @param int $maxTrialCount
   */
  public function setMaxTrialCount($maxTrialCount)
  {
    $this->maxTrialCount = $maxTrialCount;
  }
  /**
   * @return int
   */
  public function getMaxTrialCount()
  {
    return $this->maxTrialCount;
  }
  /**
   * The desired number of Trials to run in parallel.
   *
   * @param int $parallelTrialCount
   */
  public function setParallelTrialCount($parallelTrialCount)
  {
    $this->parallelTrialCount = $parallelTrialCount;
  }
  /**
   * @return int
   */
  public function getParallelTrialCount()
  {
    return $this->parallelTrialCount;
  }
  /**
   * Study configuration of the HyperparameterTuningJob.
   *
   * @param GoogleCloudAiplatformV1StudySpec $studySpec
   */
  public function setStudySpec(GoogleCloudAiplatformV1StudySpec $studySpec)
  {
    $this->studySpec = $studySpec;
  }
  /**
   * @return GoogleCloudAiplatformV1StudySpec
   */
  public function getStudySpec()
  {
    return $this->studySpec;
  }
  /**
   * The spec of a trial job. The same spec applies to the CustomJobs created in
   * all the trials.
   *
   * @param GoogleCloudAiplatformV1CustomJobSpec $trialJobSpec
   */
  public function setTrialJobSpec(GoogleCloudAiplatformV1CustomJobSpec $trialJobSpec)
  {
    $this->trialJobSpec = $trialJobSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1CustomJobSpec
   */
  public function getTrialJobSpec()
  {
    return $this->trialJobSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaTrainingjobDefinitionHyperparameterTuningJobSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaTrainingjobDefinitionHyperparameterTuningJobSpec');
