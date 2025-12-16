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

class GoogleCloudDataplexV1TaskSparkTaskConfig extends \Google\Collection
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
   * The name of the driver's main class. The jar file that contains the class
   * must be in the default CLASSPATH or specified in jar_file_uris. The
   * execution args are passed in as a sequence of named process arguments
   * (--key=value).
   *
   * @var string
   */
  public $mainClass;
  /**
   * The Cloud Storage URI of the jar file that contains the main class. The
   * execution args are passed in as a sequence of named process arguments
   * (--key=value).
   *
   * @var string
   */
  public $mainJarFileUri;
  /**
   * The Gcloud Storage URI of the main Python file to use as the driver. Must
   * be a .py file. The execution args are passed in as a sequence of named
   * process arguments (--key=value).
   *
   * @var string
   */
  public $pythonScriptFile;
  /**
   * The query text. The execution args are used to declare a set of script
   * variables (set key="value";).
   *
   * @var string
   */
  public $sqlScript;
  /**
   * A reference to a query file. This should be the Cloud Storage URI of the
   * query file. The execution args are used to declare a set of script
   * variables (set key="value";).
   *
   * @var string
   */
  public $sqlScriptFile;

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
   * The name of the driver's main class. The jar file that contains the class
   * must be in the default CLASSPATH or specified in jar_file_uris. The
   * execution args are passed in as a sequence of named process arguments
   * (--key=value).
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
   * The Cloud Storage URI of the jar file that contains the main class. The
   * execution args are passed in as a sequence of named process arguments
   * (--key=value).
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
   * The Gcloud Storage URI of the main Python file to use as the driver. Must
   * be a .py file. The execution args are passed in as a sequence of named
   * process arguments (--key=value).
   *
   * @param string $pythonScriptFile
   */
  public function setPythonScriptFile($pythonScriptFile)
  {
    $this->pythonScriptFile = $pythonScriptFile;
  }
  /**
   * @return string
   */
  public function getPythonScriptFile()
  {
    return $this->pythonScriptFile;
  }
  /**
   * The query text. The execution args are used to declare a set of script
   * variables (set key="value";).
   *
   * @param string $sqlScript
   */
  public function setSqlScript($sqlScript)
  {
    $this->sqlScript = $sqlScript;
  }
  /**
   * @return string
   */
  public function getSqlScript()
  {
    return $this->sqlScript;
  }
  /**
   * A reference to a query file. This should be the Cloud Storage URI of the
   * query file. The execution args are used to declare a set of script
   * variables (set key="value";).
   *
   * @param string $sqlScriptFile
   */
  public function setSqlScriptFile($sqlScriptFile)
  {
    $this->sqlScriptFile = $sqlScriptFile;
  }
  /**
   * @return string
   */
  public function getSqlScriptFile()
  {
    return $this->sqlScriptFile;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1TaskSparkTaskConfig::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1TaskSparkTaskConfig');
