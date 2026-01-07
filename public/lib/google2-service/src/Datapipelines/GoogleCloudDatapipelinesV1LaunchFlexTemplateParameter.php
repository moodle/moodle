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

class GoogleCloudDatapipelinesV1LaunchFlexTemplateParameter extends \Google\Model
{
  /**
   * Cloud Storage path to a file with a JSON-serialized ContainerSpec as
   * content.
   *
   * @var string
   */
  public $containerSpecGcsPath;
  protected $environmentType = GoogleCloudDatapipelinesV1FlexTemplateRuntimeEnvironment::class;
  protected $environmentDataType = '';
  /**
   * Required. The job name to use for the created job. For an update job
   * request, the job name should be the same as the existing running job.
   *
   * @var string
   */
  public $jobName;
  /**
   * Launch options for this Flex Template job. This is a common set of options
   * across languages and templates. This should not be used to pass job
   * parameters.
   *
   * @var string[]
   */
  public $launchOptions;
  /**
   * The parameters for the Flex Template. Example: `{"num_workers":"5"}`
   *
   * @var string[]
   */
  public $parameters;
  /**
   * Use this to pass transform name mappings for streaming update jobs.
   * Example: `{"oldTransformName":"newTransformName",...}`
   *
   * @var string[]
   */
  public $transformNameMappings;
  /**
   * Set this to true if you are sending a request to update a running streaming
   * job. When set, the job name should be the same as the running job.
   *
   * @var bool
   */
  public $update;

  /**
   * Cloud Storage path to a file with a JSON-serialized ContainerSpec as
   * content.
   *
   * @param string $containerSpecGcsPath
   */
  public function setContainerSpecGcsPath($containerSpecGcsPath)
  {
    $this->containerSpecGcsPath = $containerSpecGcsPath;
  }
  /**
   * @return string
   */
  public function getContainerSpecGcsPath()
  {
    return $this->containerSpecGcsPath;
  }
  /**
   * The runtime environment for the Flex Template job.
   *
   * @param GoogleCloudDatapipelinesV1FlexTemplateRuntimeEnvironment $environment
   */
  public function setEnvironment(GoogleCloudDatapipelinesV1FlexTemplateRuntimeEnvironment $environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return GoogleCloudDatapipelinesV1FlexTemplateRuntimeEnvironment
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * Required. The job name to use for the created job. For an update job
   * request, the job name should be the same as the existing running job.
   *
   * @param string $jobName
   */
  public function setJobName($jobName)
  {
    $this->jobName = $jobName;
  }
  /**
   * @return string
   */
  public function getJobName()
  {
    return $this->jobName;
  }
  /**
   * Launch options for this Flex Template job. This is a common set of options
   * across languages and templates. This should not be used to pass job
   * parameters.
   *
   * @param string[] $launchOptions
   */
  public function setLaunchOptions($launchOptions)
  {
    $this->launchOptions = $launchOptions;
  }
  /**
   * @return string[]
   */
  public function getLaunchOptions()
  {
    return $this->launchOptions;
  }
  /**
   * The parameters for the Flex Template. Example: `{"num_workers":"5"}`
   *
   * @param string[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return string[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Use this to pass transform name mappings for streaming update jobs.
   * Example: `{"oldTransformName":"newTransformName",...}`
   *
   * @param string[] $transformNameMappings
   */
  public function setTransformNameMappings($transformNameMappings)
  {
    $this->transformNameMappings = $transformNameMappings;
  }
  /**
   * @return string[]
   */
  public function getTransformNameMappings()
  {
    return $this->transformNameMappings;
  }
  /**
   * Set this to true if you are sending a request to update a running streaming
   * job. When set, the job name should be the same as the running job.
   *
   * @param bool $update
   */
  public function setUpdate($update)
  {
    $this->update = $update;
  }
  /**
   * @return bool
   */
  public function getUpdate()
  {
    return $this->update;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatapipelinesV1LaunchFlexTemplateParameter::class, 'Google_Service_Datapipelines_GoogleCloudDatapipelinesV1LaunchFlexTemplateParameter');
