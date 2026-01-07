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

class GoogleCloudDatapipelinesV1LaunchTemplateParameters extends \Google\Model
{
  protected $environmentType = GoogleCloudDatapipelinesV1RuntimeEnvironment::class;
  protected $environmentDataType = '';
  /**
   * Required. The job name to use for the created job.
   *
   * @var string
   */
  public $jobName;
  /**
   * The runtime parameters to pass to the job.
   *
   * @var string[]
   */
  public $parameters;
  /**
   * Map of transform name prefixes of the job to be replaced to the
   * corresponding name prefixes of the new job. Only applicable when updating a
   * pipeline.
   *
   * @var string[]
   */
  public $transformNameMapping;
  /**
   * If set, replace the existing pipeline with the name specified by jobName
   * with this pipeline, preserving state.
   *
   * @var bool
   */
  public $update;

  /**
   * The runtime environment for the job.
   *
   * @param GoogleCloudDatapipelinesV1RuntimeEnvironment $environment
   */
  public function setEnvironment(GoogleCloudDatapipelinesV1RuntimeEnvironment $environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return GoogleCloudDatapipelinesV1RuntimeEnvironment
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * Required. The job name to use for the created job.
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
   * The runtime parameters to pass to the job.
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
   * Map of transform name prefixes of the job to be replaced to the
   * corresponding name prefixes of the new job. Only applicable when updating a
   * pipeline.
   *
   * @param string[] $transformNameMapping
   */
  public function setTransformNameMapping($transformNameMapping)
  {
    $this->transformNameMapping = $transformNameMapping;
  }
  /**
   * @return string[]
   */
  public function getTransformNameMapping()
  {
    return $this->transformNameMapping;
  }
  /**
   * If set, replace the existing pipeline with the name specified by jobName
   * with this pipeline, preserving state.
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
class_alias(GoogleCloudDatapipelinesV1LaunchTemplateParameters::class, 'Google_Service_Datapipelines_GoogleCloudDatapipelinesV1LaunchTemplateParameters');
