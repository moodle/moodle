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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1TaskNotebookTaskConfig extends \Google\Collection
{
  protected $collection_key = 'fileUris';
  /**
   * Optional. Cloud Storage URIs of archives to be extracted into the working
   * directory of each executor. Supported file types: .jar, .tar, .tar.gz,
   * .tgz, and .zip.
   *
   * @var string[]
   */
  public $archiveUris;
  /**
   * Optional. Cloud Storage URIs of files to be placed in the working directory
   * of each executor.
   *
   * @var string[]
   */
  public $fileUris;
  protected $infrastructureSpecType = GoogleCloudDataplexV1TaskInfrastructureSpec::class;
  protected $infrastructureSpecDataType = '';
  /**
   * Required. Path to input notebook. This can be the Cloud Storage URI of the
   * notebook file or the path to a Notebook Content. The execution args are
   * accessible as environment variables (TASK_key=value).
   *
   * @var string
   */
  public $notebook;

  /**
   * Optional. Cloud Storage URIs of archives to be extracted into the working
   * directory of each executor. Supported file types: .jar, .tar, .tar.gz,
   * .tgz, and .zip.
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
   * Optional. Cloud Storage URIs of files to be placed in the working directory
   * of each executor.
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
   * Optional. Infrastructure specification for the execution.
   *
   * @param GoogleCloudDataplexV1TaskInfrastructureSpec $infrastructureSpec
   */
  public function setInfrastructureSpec(GoogleCloudDataplexV1TaskInfrastructureSpec $infrastructureSpec)
  {
    $this->infrastructureSpec = $infrastructureSpec;
  }
  /**
   * @return GoogleCloudDataplexV1TaskInfrastructureSpec
   */
  public function getInfrastructureSpec()
  {
    return $this->infrastructureSpec;
  }
  /**
   * Required. Path to input notebook. This can be the Cloud Storage URI of the
   * notebook file or the path to a Notebook Content. The execution args are
   * accessible as environment variables (TASK_key=value).
   *
   * @param string $notebook
   */
  public function setNotebook($notebook)
  {
    $this->notebook = $notebook;
  }
  /**
   * @return string
   */
  public function getNotebook()
  {
    return $this->notebook;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1TaskNotebookTaskConfig::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1TaskNotebookTaskConfig');
