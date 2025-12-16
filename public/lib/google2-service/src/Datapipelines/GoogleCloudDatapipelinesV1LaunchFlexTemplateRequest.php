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

namespace Google\Service\Datapipelines;

class GoogleCloudDatapipelinesV1LaunchFlexTemplateRequest extends \Google\Model
{
  protected $launchParameterType = GoogleCloudDatapipelinesV1LaunchFlexTemplateParameter::class;
  protected $launchParameterDataType = '';
  /**
   * Required. The [regional endpoint]
   * (https://cloud.google.com/dataflow/docs/concepts/regional-endpoints) to
   * which to direct the request. For example, `us-central1`, `us-west1`.
   *
   * @var string
   */
  public $location;
  /**
   * Required. The ID of the Cloud Platform project that the job belongs to.
   *
   * @var string
   */
  public $projectId;
  /**
   * If true, the request is validated but not actually executed. Defaults to
   * false.
   *
   * @var bool
   */
  public $validateOnly;

  /**
   * Required. Parameter to launch a job from a Flex Template.
   *
   * @param GoogleCloudDatapipelinesV1LaunchFlexTemplateParameter $launchParameter
   */
  public function setLaunchParameter(GoogleCloudDatapipelinesV1LaunchFlexTemplateParameter $launchParameter)
  {
    $this->launchParameter = $launchParameter;
  }
  /**
   * @return GoogleCloudDatapipelinesV1LaunchFlexTemplateParameter
   */
  public function getLaunchParameter()
  {
    return $this->launchParameter;
  }
  /**
   * Required. The [regional endpoint]
   * (https://cloud.google.com/dataflow/docs/concepts/regional-endpoints) to
   * which to direct the request. For example, `us-central1`, `us-west1`.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Required. The ID of the Cloud Platform project that the job belongs to.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * If true, the request is validated but not actually executed. Defaults to
   * false.
   *
   * @param bool $validateOnly
   */
  public function setValidateOnly($validateOnly)
  {
    $this->validateOnly = $validateOnly;
  }
  /**
   * @return bool
   */
  public function getValidateOnly()
  {
    return $this->validateOnly;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatapipelinesV1LaunchFlexTemplateRequest::class, 'Google_Service_Datapipelines_GoogleCloudDatapipelinesV1LaunchFlexTemplateRequest');
