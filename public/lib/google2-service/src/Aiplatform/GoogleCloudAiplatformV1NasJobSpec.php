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

class GoogleCloudAiplatformV1NasJobSpec extends \Google\Model
{
  protected $multiTrialAlgorithmSpecType = GoogleCloudAiplatformV1NasJobSpecMultiTrialAlgorithmSpec::class;
  protected $multiTrialAlgorithmSpecDataType = '';
  /**
   * The ID of the existing NasJob in the same Project and Location which will
   * be used to resume search. search_space_spec and nas_algorithm_spec are
   * obtained from previous NasJob hence should not provide them again for this
   * NasJob.
   *
   * @var string
   */
  public $resumeNasJobId;
  /**
   * It defines the search space for Neural Architecture Search (NAS).
   *
   * @var string
   */
  public $searchSpaceSpec;

  /**
   * The spec of multi-trial algorithms.
   *
   * @param GoogleCloudAiplatformV1NasJobSpecMultiTrialAlgorithmSpec $multiTrialAlgorithmSpec
   */
  public function setMultiTrialAlgorithmSpec(GoogleCloudAiplatformV1NasJobSpecMultiTrialAlgorithmSpec $multiTrialAlgorithmSpec)
  {
    $this->multiTrialAlgorithmSpec = $multiTrialAlgorithmSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1NasJobSpecMultiTrialAlgorithmSpec
   */
  public function getMultiTrialAlgorithmSpec()
  {
    return $this->multiTrialAlgorithmSpec;
  }
  /**
   * The ID of the existing NasJob in the same Project and Location which will
   * be used to resume search. search_space_spec and nas_algorithm_spec are
   * obtained from previous NasJob hence should not provide them again for this
   * NasJob.
   *
   * @param string $resumeNasJobId
   */
  public function setResumeNasJobId($resumeNasJobId)
  {
    $this->resumeNasJobId = $resumeNasJobId;
  }
  /**
   * @return string
   */
  public function getResumeNasJobId()
  {
    return $this->resumeNasJobId;
  }
  /**
   * It defines the search space for Neural Architecture Search (NAS).
   *
   * @param string $searchSpaceSpec
   */
  public function setSearchSpaceSpec($searchSpaceSpec)
  {
    $this->searchSpaceSpec = $searchSpaceSpec;
  }
  /**
   * @return string
   */
  public function getSearchSpaceSpec()
  {
    return $this->searchSpaceSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1NasJobSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1NasJobSpec');
