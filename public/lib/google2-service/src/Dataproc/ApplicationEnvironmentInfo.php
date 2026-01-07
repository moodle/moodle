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

class ApplicationEnvironmentInfo extends \Google\Collection
{
  protected $collection_key = 'resourceProfiles';
  /**
   * @var string[]
   */
  public $classpathEntries;
  /**
   * @var string[]
   */
  public $hadoopProperties;
  /**
   * @var string[]
   */
  public $metricsProperties;
  protected $resourceProfilesType = ResourceProfileInfo::class;
  protected $resourceProfilesDataType = 'array';
  protected $runtimeType = SparkRuntimeInfo::class;
  protected $runtimeDataType = '';
  /**
   * @var string[]
   */
  public $sparkProperties;
  /**
   * @var string[]
   */
  public $systemProperties;

  /**
   * @param string[] $classpathEntries
   */
  public function setClasspathEntries($classpathEntries)
  {
    $this->classpathEntries = $classpathEntries;
  }
  /**
   * @return string[]
   */
  public function getClasspathEntries()
  {
    return $this->classpathEntries;
  }
  /**
   * @param string[] $hadoopProperties
   */
  public function setHadoopProperties($hadoopProperties)
  {
    $this->hadoopProperties = $hadoopProperties;
  }
  /**
   * @return string[]
   */
  public function getHadoopProperties()
  {
    return $this->hadoopProperties;
  }
  /**
   * @param string[] $metricsProperties
   */
  public function setMetricsProperties($metricsProperties)
  {
    $this->metricsProperties = $metricsProperties;
  }
  /**
   * @return string[]
   */
  public function getMetricsProperties()
  {
    return $this->metricsProperties;
  }
  /**
   * @param ResourceProfileInfo[] $resourceProfiles
   */
  public function setResourceProfiles($resourceProfiles)
  {
    $this->resourceProfiles = $resourceProfiles;
  }
  /**
   * @return ResourceProfileInfo[]
   */
  public function getResourceProfiles()
  {
    return $this->resourceProfiles;
  }
  /**
   * @param SparkRuntimeInfo $runtime
   */
  public function setRuntime(SparkRuntimeInfo $runtime)
  {
    $this->runtime = $runtime;
  }
  /**
   * @return SparkRuntimeInfo
   */
  public function getRuntime()
  {
    return $this->runtime;
  }
  /**
   * @param string[] $sparkProperties
   */
  public function setSparkProperties($sparkProperties)
  {
    $this->sparkProperties = $sparkProperties;
  }
  /**
   * @return string[]
   */
  public function getSparkProperties()
  {
    return $this->sparkProperties;
  }
  /**
   * @param string[] $systemProperties
   */
  public function setSystemProperties($systemProperties)
  {
    $this->systemProperties = $systemProperties;
  }
  /**
   * @return string[]
   */
  public function getSystemProperties()
  {
    return $this->systemProperties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApplicationEnvironmentInfo::class, 'Google_Service_Dataproc_ApplicationEnvironmentInfo');
