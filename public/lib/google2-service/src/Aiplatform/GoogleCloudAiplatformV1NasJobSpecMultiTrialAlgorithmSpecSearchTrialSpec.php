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

class GoogleCloudAiplatformV1NasJobSpecMultiTrialAlgorithmSpecSearchTrialSpec extends \Google\Model
{
  /**
   * The number of failed trials that need to be seen before failing the NasJob.
   * If set to 0, Vertex AI decides how many trials must fail before the whole
   * job fails.
   *
   * @var int
   */
  public $maxFailedTrialCount;
  /**
   * Required. The maximum number of trials to run in parallel.
   *
   * @var int
   */
  public $maxParallelTrialCount;
  /**
   * Required. The maximum number of Neural Architecture Search (NAS) trials to
   * run.
   *
   * @var int
   */
  public $maxTrialCount;
  protected $searchTrialJobSpecType = GoogleCloudAiplatformV1CustomJobSpec::class;
  protected $searchTrialJobSpecDataType = '';

  /**
   * The number of failed trials that need to be seen before failing the NasJob.
   * If set to 0, Vertex AI decides how many trials must fail before the whole
   * job fails.
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
   * Required. The maximum number of trials to run in parallel.
   *
   * @param int $maxParallelTrialCount
   */
  public function setMaxParallelTrialCount($maxParallelTrialCount)
  {
    $this->maxParallelTrialCount = $maxParallelTrialCount;
  }
  /**
   * @return int
   */
  public function getMaxParallelTrialCount()
  {
    return $this->maxParallelTrialCount;
  }
  /**
   * Required. The maximum number of Neural Architecture Search (NAS) trials to
   * run.
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
   * Required. The spec of a search trial job. The same spec applies to all
   * search trials.
   *
   * @param GoogleCloudAiplatformV1CustomJobSpec $searchTrialJobSpec
   */
  public function setSearchTrialJobSpec(GoogleCloudAiplatformV1CustomJobSpec $searchTrialJobSpec)
  {
    $this->searchTrialJobSpec = $searchTrialJobSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1CustomJobSpec
   */
  public function getSearchTrialJobSpec()
  {
    return $this->searchTrialJobSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1NasJobSpecMultiTrialAlgorithmSpecSearchTrialSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1NasJobSpecMultiTrialAlgorithmSpecSearchTrialSpec');
