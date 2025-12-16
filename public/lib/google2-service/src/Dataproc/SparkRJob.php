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

class SparkRJob extends \Google\Collection
{
  protected $collection_key = 'fileUris';
  /**
   * Optional. HCFS URIs of archives to be extracted into the working directory
   * of each executor. Supported file types: .jar, .tar, .tar.gz, .tgz, and
   * .zip.
   *
   * @var string[]
   */
  public $archiveUris;
  /**
   * Optional. The arguments to pass to the driver. Do not include arguments,
   * such as --conf, that can be set as job properties, since a collision may
   * occur that causes an incorrect job submission.
   *
   * @var string[]
   */
  public $args;
  /**
   * Optional. HCFS URIs of files to be placed in the working directory of each
   * executor. Useful for naively parallel tasks.
   *
   * @var string[]
   */
  public $fileUris;
  protected $loggingConfigType = LoggingConfig::class;
  protected $loggingConfigDataType = '';
  /**
   * Required. The HCFS URI of the main R file to use as the driver. Must be a
   * .R file.
   *
   * @var string
   */
  public $mainRFileUri;
  /**
   * Optional. A mapping of property names to values, used to configure SparkR.
   * Properties that conflict with values set by the Dataproc API might be
   * overwritten. Can include properties set in /etc/spark/conf/spark-
   * defaults.conf and classes in user code.
   *
   * @var string[]
   */
  public $properties;

  /**
   * Optional. HCFS URIs of archives to be extracted into the working directory
   * of each executor. Supported file types: .jar, .tar, .tar.gz, .tgz, and
   * .zip.
   *
   * @param string[] $archiveUris
   */
  public function setArchiveUris($archiveUris)
  {
    $this->archiveUris = $archiveUris;
  }
  /**
   * @return string[]
   */
  public function getArchiveUris()
  {
    return $this->archiveUris;
  }
  /**
   * Optional. The arguments to pass to the driver. Do not include arguments,
   * such as --conf, that can be set as job properties, since a collision may
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
   * Optional. HCFS URIs of files to be placed in the working directory of each
   * executor. Useful for naively parallel tasks.
   *
   * @param string[] $fileUris
   */
  public function setFileUris($fileUris)
  {
    $this->fileUris = $fileUris;
  }
  /**
   * @return string[]
   */
  public function getFileUris()
  {
    return $this->fileUris;
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
   * Required. The HCFS URI of the main R file to use as the driver. Must be a
   * .R file.
   *
   * @param string $mainRFileUri
   */
  public function setMainRFileUri($mainRFileUri)
  {
    $this->mainRFileUri = $mainRFileUri;
  }
  /**
   * @return string
   */
  public function getMainRFileUri()
  {
    return $this->mainRFileUri;
  }
  /**
   * Optional. A mapping of property names to values, used to configure SparkR.
   * Properties that conflict with values set by the Dataproc API might be
   * overwritten. Can include properties set in /etc/spark/conf/spark-
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SparkRJob::class, 'Google_Service_Dataproc_SparkRJob');
