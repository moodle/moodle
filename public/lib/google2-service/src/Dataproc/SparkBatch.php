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

class SparkBatch extends \Google\Collection
{
  protected $collection_key = 'jarFileUris';
  /**
   * Optional. HCFS URIs of archives to be extracted into the working directory
   * of each executor. Supported file types: .jar, .tar, .tar.gz, .tgz, and
   * .zip.
   *
   * @var string[]
   */
  public $archiveUris;
  /**
   * Optional. The arguments to pass to the driver. Do not include arguments
   * that can be set as batch properties, such as --conf, since a collision can
   * occur that causes an incorrect batch submission.
   *
   * @var string[]
   */
  public $args;
  /**
   * Optional. HCFS URIs of files to be placed in the working directory of each
   * executor.
   *
   * @var string[]
   */
  public $fileUris;
  /**
   * Optional. HCFS URIs of jar files to add to the classpath of the Spark
   * driver and tasks.
   *
   * @var string[]
   */
  public $jarFileUris;
  /**
   * Optional. The name of the driver main class. The jar file that contains the
   * class must be in the classpath or specified in jar_file_uris.
   *
   * @var string
   */
  public $mainClass;
  /**
   * Optional. The HCFS URI of the jar file that contains the main class.
   *
   * @var string
   */
  public $mainJarFileUri;

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
   * Optional. The arguments to pass to the driver. Do not include arguments
   * that can be set as batch properties, such as --conf, since a collision can
   * occur that causes an incorrect batch submission.
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
   * executor.
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
   * Optional. HCFS URIs of jar files to add to the classpath of the Spark
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
   * Optional. The name of the driver main class. The jar file that contains the
   * class must be in the classpath or specified in jar_file_uris.
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
   * Optional. The HCFS URI of the jar file that contains the main class.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SparkBatch::class, 'Google_Service_Dataproc_SparkBatch');
