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

namespace Google\Service\Dataflow;

class TaskRunnerSettings extends \Google\Collection
{
  protected $collection_key = 'oauthScopes';
  /**
   * Whether to also send taskrunner log info to stderr.
   *
   * @var bool
   */
  public $alsologtostderr;
  /**
   * The location on the worker for task-specific subdirectories.
   *
   * @var string
   */
  public $baseTaskDir;
  /**
   * The base URL for the taskrunner to use when accessing Google Cloud APIs.
   * When workers access Google Cloud APIs, they logically do so via relative
   * URLs. If this field is specified, it supplies the base URL to use for
   * resolving these relative URLs. The normative algorithm used is defined by
   * RFC 1808, "Relative Uniform Resource Locators". If not specified, the
   * default value is "http://www.googleapis.com/"
   *
   * @var string
   */
  public $baseUrl;
  /**
   * The file to store preprocessing commands in.
   *
   * @var string
   */
  public $commandlinesFileName;
  /**
   * Whether to continue taskrunner if an exception is hit.
   *
   * @var bool
   */
  public $continueOnException;
  /**
   * The API version of endpoint, e.g. "v1b3"
   *
   * @var string
   */
  public $dataflowApiVersion;
  /**
   * The command to launch the worker harness.
   *
   * @var string
   */
  public $harnessCommand;
  /**
   * The suggested backend language.
   *
   * @var string
   */
  public $languageHint;
  /**
   * The directory on the VM to store logs.
   *
   * @var string
   */
  public $logDir;
  /**
   * Whether to send taskrunner log info to Google Compute Engine VM serial
   * console.
   *
   * @var bool
   */
  public $logToSerialconsole;
  /**
   * Indicates where to put logs. If this is not specified, the logs will not be
   * uploaded. The supported resource type is: Google Cloud Storage:
   * storage.googleapis.com/{bucket}/{object}
   * bucket.storage.googleapis.com/{object}
   *
   * @var string
   */
  public $logUploadLocation;
  /**
   * The OAuth2 scopes to be requested by the taskrunner in order to access the
   * Cloud Dataflow API.
   *
   * @var string[]
   */
  public $oauthScopes;
  protected $parallelWorkerSettingsType = WorkerSettings::class;
  protected $parallelWorkerSettingsDataType = '';
  /**
   * The streaming worker main class name.
   *
   * @var string
   */
  public $streamingWorkerMainClass;
  /**
   * The UNIX group ID on the worker VM to use for tasks launched by taskrunner;
   * e.g. "wheel".
   *
   * @var string
   */
  public $taskGroup;
  /**
   * The UNIX user ID on the worker VM to use for tasks launched by taskrunner;
   * e.g. "root".
   *
   * @var string
   */
  public $taskUser;
  /**
   * The prefix of the resources the taskrunner should use for temporary
   * storage. The supported resource type is: Google Cloud Storage:
   * storage.googleapis.com/{bucket}/{object}
   * bucket.storage.googleapis.com/{object}
   *
   * @var string
   */
  public $tempStoragePrefix;
  /**
   * The ID string of the VM.
   *
   * @var string
   */
  public $vmId;
  /**
   * The file to store the workflow in.
   *
   * @var string
   */
  public $workflowFileName;

  /**
   * Whether to also send taskrunner log info to stderr.
   *
   * @param bool $alsologtostderr
   */
  public function setAlsologtostderr($alsologtostderr)
  {
    $this->alsologtostderr = $alsologtostderr;
  }
  /**
   * @return bool
   */
  public function getAlsologtostderr()
  {
    return $this->alsologtostderr;
  }
  /**
   * The location on the worker for task-specific subdirectories.
   *
   * @param string $baseTaskDir
   */
  public function setBaseTaskDir($baseTaskDir)
  {
    $this->baseTaskDir = $baseTaskDir;
  }
  /**
   * @return string
   */
  public function getBaseTaskDir()
  {
    return $this->baseTaskDir;
  }
  /**
   * The base URL for the taskrunner to use when accessing Google Cloud APIs.
   * When workers access Google Cloud APIs, they logically do so via relative
   * URLs. If this field is specified, it supplies the base URL to use for
   * resolving these relative URLs. The normative algorithm used is defined by
   * RFC 1808, "Relative Uniform Resource Locators". If not specified, the
   * default value is "http://www.googleapis.com/"
   *
   * @param string $baseUrl
   */
  public function setBaseUrl($baseUrl)
  {
    $this->baseUrl = $baseUrl;
  }
  /**
   * @return string
   */
  public function getBaseUrl()
  {
    return $this->baseUrl;
  }
  /**
   * The file to store preprocessing commands in.
   *
   * @param string $commandlinesFileName
   */
  public function setCommandlinesFileName($commandlinesFileName)
  {
    $this->commandlinesFileName = $commandlinesFileName;
  }
  /**
   * @return string
   */
  public function getCommandlinesFileName()
  {
    return $this->commandlinesFileName;
  }
  /**
   * Whether to continue taskrunner if an exception is hit.
   *
   * @param bool $continueOnException
   */
  public function setContinueOnException($continueOnException)
  {
    $this->continueOnException = $continueOnException;
  }
  /**
   * @return bool
   */
  public function getContinueOnException()
  {
    return $this->continueOnException;
  }
  /**
   * The API version of endpoint, e.g. "v1b3"
   *
   * @param string $dataflowApiVersion
   */
  public function setDataflowApiVersion($dataflowApiVersion)
  {
    $this->dataflowApiVersion = $dataflowApiVersion;
  }
  /**
   * @return string
   */
  public function getDataflowApiVersion()
  {
    return $this->dataflowApiVersion;
  }
  /**
   * The command to launch the worker harness.
   *
   * @param string $harnessCommand
   */
  public function setHarnessCommand($harnessCommand)
  {
    $this->harnessCommand = $harnessCommand;
  }
  /**
   * @return string
   */
  public function getHarnessCommand()
  {
    return $this->harnessCommand;
  }
  /**
   * The suggested backend language.
   *
   * @param string $languageHint
   */
  public function setLanguageHint($languageHint)
  {
    $this->languageHint = $languageHint;
  }
  /**
   * @return string
   */
  public function getLanguageHint()
  {
    return $this->languageHint;
  }
  /**
   * The directory on the VM to store logs.
   *
   * @param string $logDir
   */
  public function setLogDir($logDir)
  {
    $this->logDir = $logDir;
  }
  /**
   * @return string
   */
  public function getLogDir()
  {
    return $this->logDir;
  }
  /**
   * Whether to send taskrunner log info to Google Compute Engine VM serial
   * console.
   *
   * @param bool $logToSerialconsole
   */
  public function setLogToSerialconsole($logToSerialconsole)
  {
    $this->logToSerialconsole = $logToSerialconsole;
  }
  /**
   * @return bool
   */
  public function getLogToSerialconsole()
  {
    return $this->logToSerialconsole;
  }
  /**
   * Indicates where to put logs. If this is not specified, the logs will not be
   * uploaded. The supported resource type is: Google Cloud Storage:
   * storage.googleapis.com/{bucket}/{object}
   * bucket.storage.googleapis.com/{object}
   *
   * @param string $logUploadLocation
   */
  public function setLogUploadLocation($logUploadLocation)
  {
    $this->logUploadLocation = $logUploadLocation;
  }
  /**
   * @return string
   */
  public function getLogUploadLocation()
  {
    return $this->logUploadLocation;
  }
  /**
   * The OAuth2 scopes to be requested by the taskrunner in order to access the
   * Cloud Dataflow API.
   *
   * @param string[] $oauthScopes
   */
  public function setOauthScopes($oauthScopes)
  {
    $this->oauthScopes = $oauthScopes;
  }
  /**
   * @return string[]
   */
  public function getOauthScopes()
  {
    return $this->oauthScopes;
  }
  /**
   * The settings to pass to the parallel worker harness.
   *
   * @param WorkerSettings $parallelWorkerSettings
   */
  public function setParallelWorkerSettings(WorkerSettings $parallelWorkerSettings)
  {
    $this->parallelWorkerSettings = $parallelWorkerSettings;
  }
  /**
   * @return WorkerSettings
   */
  public function getParallelWorkerSettings()
  {
    return $this->parallelWorkerSettings;
  }
  /**
   * The streaming worker main class name.
   *
   * @param string $streamingWorkerMainClass
   */
  public function setStreamingWorkerMainClass($streamingWorkerMainClass)
  {
    $this->streamingWorkerMainClass = $streamingWorkerMainClass;
  }
  /**
   * @return string
   */
  public function getStreamingWorkerMainClass()
  {
    return $this->streamingWorkerMainClass;
  }
  /**
   * The UNIX group ID on the worker VM to use for tasks launched by taskrunner;
   * e.g. "wheel".
   *
   * @param string $taskGroup
   */
  public function setTaskGroup($taskGroup)
  {
    $this->taskGroup = $taskGroup;
  }
  /**
   * @return string
   */
  public function getTaskGroup()
  {
    return $this->taskGroup;
  }
  /**
   * The UNIX user ID on the worker VM to use for tasks launched by taskrunner;
   * e.g. "root".
   *
   * @param string $taskUser
   */
  public function setTaskUser($taskUser)
  {
    $this->taskUser = $taskUser;
  }
  /**
   * @return string
   */
  public function getTaskUser()
  {
    return $this->taskUser;
  }
  /**
   * The prefix of the resources the taskrunner should use for temporary
   * storage. The supported resource type is: Google Cloud Storage:
   * storage.googleapis.com/{bucket}/{object}
   * bucket.storage.googleapis.com/{object}
   *
   * @param string $tempStoragePrefix
   */
  public function setTempStoragePrefix($tempStoragePrefix)
  {
    $this->tempStoragePrefix = $tempStoragePrefix;
  }
  /**
   * @return string
   */
  public function getTempStoragePrefix()
  {
    return $this->tempStoragePrefix;
  }
  /**
   * The ID string of the VM.
   *
   * @param string $vmId
   */
  public function setVmId($vmId)
  {
    $this->vmId = $vmId;
  }
  /**
   * @return string
   */
  public function getVmId()
  {
    return $this->vmId;
  }
  /**
   * The file to store the workflow in.
   *
   * @param string $workflowFileName
   */
  public function setWorkflowFileName($workflowFileName)
  {
    $this->workflowFileName = $workflowFileName;
  }
  /**
   * @return string
   */
  public function getWorkflowFileName()
  {
    return $this->workflowFileName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TaskRunnerSettings::class, 'Google_Service_Dataflow_TaskRunnerSettings');
