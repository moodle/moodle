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

class GoogleCloudAiplatformV1RebaseTunedModelRequest extends \Google\Model
{
  protected $artifactDestinationType = GoogleCloudAiplatformV1GcsDestination::class;
  protected $artifactDestinationDataType = '';
  /**
   * Optional. By default, bison to gemini migration will always create new
   * model/endpoint, but for gemini-1.0 to gemini-1.5 migration, we default
   * deploy to the same endpoint. See details in this Section.
   *
   * @var bool
   */
  public $deployToSameEndpoint;
  protected $tunedModelRefType = GoogleCloudAiplatformV1TunedModelRef::class;
  protected $tunedModelRefDataType = '';
  protected $tuningJobType = GoogleCloudAiplatformV1TuningJob::class;
  protected $tuningJobDataType = '';

  /**
   * Optional. The Google Cloud Storage location to write the artifacts.
   *
   * @param GoogleCloudAiplatformV1GcsDestination $artifactDestination
   */
  public function setArtifactDestination(GoogleCloudAiplatformV1GcsDestination $artifactDestination)
  {
    $this->artifactDestination = $artifactDestination;
  }
  /**
   * @return GoogleCloudAiplatformV1GcsDestination
   */
  public function getArtifactDestination()
  {
    return $this->artifactDestination;
  }
  /**
   * Optional. By default, bison to gemini migration will always create new
   * model/endpoint, but for gemini-1.0 to gemini-1.5 migration, we default
   * deploy to the same endpoint. See details in this Section.
   *
   * @param bool $deployToSameEndpoint
   */
  public function setDeployToSameEndpoint($deployToSameEndpoint)
  {
    $this->deployToSameEndpoint = $deployToSameEndpoint;
  }
  /**
   * @return bool
   */
  public function getDeployToSameEndpoint()
  {
    return $this->deployToSameEndpoint;
  }
  /**
   * Required. TunedModel reference to retrieve the legacy model information.
   *
   * @param GoogleCloudAiplatformV1TunedModelRef $tunedModelRef
   */
  public function setTunedModelRef(GoogleCloudAiplatformV1TunedModelRef $tunedModelRef)
  {
    $this->tunedModelRef = $tunedModelRef;
  }
  /**
   * @return GoogleCloudAiplatformV1TunedModelRef
   */
  public function getTunedModelRef()
  {
    return $this->tunedModelRef;
  }
  /**
   * Optional. The TuningJob to be updated. Users can use this TuningJob field
   * to overwrite tuning configs.
   *
   * @param GoogleCloudAiplatformV1TuningJob $tuningJob
   */
  public function setTuningJob(GoogleCloudAiplatformV1TuningJob $tuningJob)
  {
    $this->tuningJob = $tuningJob;
  }
  /**
   * @return GoogleCloudAiplatformV1TuningJob
   */
  public function getTuningJob()
  {
    return $this->tuningJob;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RebaseTunedModelRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RebaseTunedModelRequest');
