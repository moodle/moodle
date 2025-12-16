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

class FlinkJob extends \Google\Collection
{
  protected $collection_key = 'jarFileUris';
  /**
   * Optional. The arguments to pass to the driver. Do not include arguments,
   * such as --conf, that can be set as job properties, since a collision might
   * occur that causes an incorrect job submission.
   *
   * @var string[]
   */
  public $args;
  /**
   * Optional. HCFS URIs of jar files to add to the CLASSPATHs of the Flink
   * driver and tasks.
   *
   * @var string[]
   */
  public $jarFileUris;
  protected $loggingConfigType = LoggingConfig::class;
  protected $loggingConfigDataType = '';
  /**
   * The name of the driver's main class. The jar file that contains the class
   * must be in the default CLASSPATH or specified in jarFileUris.
   *
   * @var string
   */
  public $mainClass;
  /**
   * The HCFS URI of the jar file that contains the main class.
   *
   * @var string
   */
  public $mainJarFileUri;
  /**
   * Optional. A mapping of property names to values, used to configure Flink.
   * Properties that conflict with values set by the Dataproc API might be
   * overwritten. Can include properties set in /etc/flink/conf/flink-
   * defaults.conf and classes in user code.
   *
   * @var string[]
   */
  public $properties;
  /**
   * Optional. HCFS URI of the savepoint, which contains the last saved progress
   * for starting the current job.
   *
   * @var string
   */
  public $savepointUri;

  /**
   * Optional. The arguments to pass to the driver. Do not include arguments,
   * such as --conf, that can be set as job properties, since a collision might
   * occur that causes an incorrect job submission.
   *
   * @param string[] $args
   */
  public function setArgs($args)
  {
    $this->args = $args;
  }
  /**
   * @return string[]
   */
  public function getArgs()
  {
    return $this->args;
  }
  /**
   * Optional. HCFS URIs of jar files to add to the CLASSPATHs of the Flink
   * driver and tasks.
   *
   * @param string[] $jarFileUris
   */
  public function setJarFileUris($jarFileUris)
  {
    $this->jarFileUris = $jarFileUris;
  }
  /**
   * @return string[]
   */
  public function getJarFileUris()
  {
    return $this->jarFileUris;
  }
  /**
   * Optional. The runtime log config for job execution.
   *
   * @param LoggingConfig $loggingConfig
   */
  public function setLoggingConfig(LoggingConfig $loggingConfig)
  {
    $this->loggingConfig = $loggingConfig;
  }
  /**
   * @return LoggingConfig
   */
  public function getLoggingConfig()
  {
    return $this->loggingConfig;
  }
  /**
   * The name of the driver's main class. The jar file that contains the class
   * must be in the default CLASSPATH or specified in jarFileUris.
   *
   * @param string $mainClass
   */
  public function setMainClass($mainClass)
  {
    $this->mainClass = $mainClass;
  }
  /**
   * @return string
   */
  public function getMainClass()
  {
    return $this->mainClass;
  }
  /**
   * The HCFS URI of the jar file that contains the main class.
   *
   * @param string $mainJarFileUri
   */
  public function setMainJarFileUri($mainJarFileUri)
  {
    $this->mainJarFileUri = $mainJarFileUri;
  }
  /**
   * @return string
   */
  public function getMainJarFileUri()
  {
    return $this->mainJarFileUri;
  }
  /**
   * Optional. A mapping of property names to values, used to configure Flink.
   * Properties that conflict with values set by the Dataproc API might be
   * overwritten. Can include properties set in /etc/flink/conf/flink-
   * defaults.conf and classes in user code.
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
   * Optional. HCFS URI of the savepoint, which contains the last saved progress
   * for starting the current job.
   *
   * @param string $savepointUri
   */
  public function setSavepointUri($savepointUri)
  {
    $this->savepointUri = $savepointUri;
  }
  /**
   * @return string
   */
  public function getSavepointUri()
  {
    return $this->savepointUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FlinkJob::class, 'Google_Service_Dataproc_FlinkJob');
