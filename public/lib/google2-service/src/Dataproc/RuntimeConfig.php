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

namespace Google\Service\Dataproc;

class RuntimeConfig extends \Google\Model
{
  protected $autotuningConfigType = AutotuningConfig::class;
  protected $autotuningConfigDataType = '';
  /**
   * Optional. Cohort identifier. Identifies families of the workloads having
   * the same shape, e.g. daily ETL jobs.
   *
   * @var string
   */
  public $cohort;
  /**
   * Optional. Optional custom container image for the job runtime environment.
   * If not specified, a default container image will be used.
   *
   * @var string
   */
  public $containerImage;
  /**
   * Optional. A mapping of property names to values, which are used to
   * configure workload execution.
   *
   * @var string[]
   */
  public $properties;
  protected $repositoryConfigType = RepositoryConfig::class;
  protected $repositoryConfigDataType = '';
  /**
   * Optional. Version of the batch runtime.
   *
   * @var string
   */
  public $version;

  /**
   * Optional. Autotuning configuration of the workload.
   *
   * @param AutotuningConfig $autotuningConfig
   */
  public function setAutotuningConfig(AutotuningConfig $autotuningConfig)
  {
    $this->autotuningConfig = $autotuningConfig;
  }
  /**
   * @return AutotuningConfig
   */
  public function getAutotuningConfig()
  {
    return $this->autotuningConfig;
  }
  /**
   * Optional. Cohort identifier. Identifies families of the workloads having
   * the same shape, e.g. daily ETL jobs.
   *
   * @param string $cohort
   */
  public function setCohort($cohort)
  {
    $this->cohort = $cohort;
  }
  /**
   * @return string
   */
  public function getCohort()
  {
    return $this->cohort;
  }
  /**
   * Optional. Optional custom container image for the job runtime environment.
   * If not specified, a default container image will be used.
   *
   * @param string $containerImage
   */
  public function setContainerImage($containerImage)
  {
    $this->containerImage = $containerImage;
  }
  /**
   * @return string
   */
  public function getContainerImage()
  {
    return $this->containerImage;
  }
  /**
   * Optional. A mapping of property names to values, which are used to
   * configure workload execution.
   *
   * @param string[] $properties
   */
  public function setProperties($properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return string[]
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * Optional. Dependency repository configuration.
   *
   * @param RepositoryConfig $repositoryConfig
   */
  public function setRepositoryConfig(RepositoryConfig $repositoryConfig)
  {
    $this->repositoryConfig = $repositoryConfig;
  }
  /**
   * @return RepositoryConfig
   */
  public function getRepositoryConfig()
  {
    return $this->repositoryConfig;
  }
  /**
   * Optional. Version of the batch runtime.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RuntimeConfig::class, 'Google_Service_Dataproc_RuntimeConfig');
