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

class SparkRBatch extends \Google\Collection
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
   * Optional. The arguments to pass to the Spark driver. Do not include
   * arguments that can be set as batch properties, such as --conf, since a
   * collision can occur that causes an incorrect batch submission.
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
   * Required. The HCFS URI of the main R file to use as the driver. Must be a
   * .R or .r file.
   *
   * @var string
   */
  public $mainRFileUri;

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
   * Optional. The arguments to pass to the Spark driver. Do not include
   * arguments that can be set as batch properties, such as --conf, since a
   * collision can occur that causes an incorrect batch submission.
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
   * Required. The HCFS URI of the main R file to use as the driver. Must be a
   * .R or .r file.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SparkRBatch::class, 'Google_Service_Dataproc_SparkRBatch');
