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

class GoogleCloudDatapipelinesV1LaunchTemplateRequest extends \Google\Model
{
  /**
   * A Cloud Storage path to the template from which to create the job. Must be
   * a valid Cloud Storage URL, beginning with 'gs://'.
   *
   * @var string
   */
  public $gcsPath;
  protected $launchParametersType = GoogleCloudDatapipelinesV1LaunchTemplateParameters::class;
  protected $launchParametersDataType = '';
  /**
   * The [regional endpoint]
   * (https://cloud.google.com/dataflow/docs/concepts/regional-endpoints) to
   * which to direct the request.
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
   * A Cloud Storage path to the template from which to create the job. Must be
   * a valid Cloud Storage URL, beginning with 'gs://'.
   *
   * @param string $gcsPath
   */
  public function setGcsPath($gcsPath)
  {
    $this->gcsPath = $gcsPath;
  }
  /**
   * @return string
   */
  public function getGcsPath()
  {
    return $this->gcsPath;
  }
  /**
   * The parameters of the template to launch. This should be part of the body
   * of the POST request.
   *
   * @param GoogleCloudDatapipelinesV1LaunchTemplateParameters $launchParameters
   */
  public function setLaunchParameters(GoogleCloudDatapipelinesV1LaunchTemplateParameters $launchParameters)
  {
    $this->launchParameters = $launchParameters;
  }
  /**
   * @return GoogleCloudDatapipelinesV1LaunchTemplateParameters
   */
  public function getLaunchParameters()
  {
    return $this->launchParameters;
  }
  /**
   * The [regional endpoint]
   * (https://cloud.google.com/dataflow/docs/concepts/regional-endpoints) to
   * which to direct the request.
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
class_alias(GoogleCloudDatapipelinesV1LaunchTemplateRequest::class, 'Google_Service_Datapipelines_GoogleCloudDatapipelinesV1LaunchTemplateRequest');
