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

namespace Google\Service\Script;

class GoogleAppsScriptTypeProcess extends \Google\Model
{
  /**
   * Unspecified status.
   */
  public const PROCESS_STATUS_PROCESS_STATUS_UNSPECIFIED = 'PROCESS_STATUS_UNSPECIFIED';
  /**
   * The process is currently running.
   */
  public const PROCESS_STATUS_RUNNING = 'RUNNING';
  /**
   * The process has paused.
   */
  public const PROCESS_STATUS_PAUSED = 'PAUSED';
  /**
   * The process has completed.
   */
  public const PROCESS_STATUS_COMPLETED = 'COMPLETED';
  /**
   * The process was cancelled.
   */
  public const PROCESS_STATUS_CANCELED = 'CANCELED';
  /**
   * The process failed.
   */
  public const PROCESS_STATUS_FAILED = 'FAILED';
  /**
   * The process timed out.
   */
  public const PROCESS_STATUS_TIMED_OUT = 'TIMED_OUT';
  /**
   * Process status unknown.
   */
  public const PROCESS_STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * The process is delayed, waiting for quota.
   */
  public const PROCESS_STATUS_DELAYED = 'DELAYED';
  /**
   * AppsScript executions are disabled by Admin.
   */
  public const PROCESS_STATUS_EXECUTION_DISABLED = 'EXECUTION_DISABLED';
  /**
   * Unspecified type.
   */
  public const PROCESS_TYPE_PROCESS_TYPE_UNSPECIFIED = 'PROCESS_TYPE_UNSPECIFIED';
  /**
   * The process was started from an add-on entry point.
   */
  public const PROCESS_TYPE_ADD_ON = 'ADD_ON';
  /**
   * The process was started using the Apps Script API.
   */
  public const PROCESS_TYPE_EXECUTION_API = 'EXECUTION_API';
  /**
   * The process was started from a time-based trigger.
   */
  public const PROCESS_TYPE_TIME_DRIVEN = 'TIME_DRIVEN';
  /**
   * The process was started from an event-based trigger.
   */
  public const PROCESS_TYPE_TRIGGER = 'TRIGGER';
  /**
   * The process was started from a web app entry point.
   */
  public const PROCESS_TYPE_WEBAPP = 'WEBAPP';
  /**
   * The process was started using the Apps Script IDE.
   */
  public const PROCESS_TYPE_EDITOR = 'EDITOR';
  /**
   * The process was started from a G Suite simple trigger.
   */
  public const PROCESS_TYPE_SIMPLE_TRIGGER = 'SIMPLE_TRIGGER';
  /**
   * The process was started from a G Suite menu item.
   */
  public const PROCESS_TYPE_MENU = 'MENU';
  /**
   * The process was started as a task in a batch job.
   */
  public const PROCESS_TYPE_BATCH_TASK = 'BATCH_TASK';
  /**
   * Runtime version unset / unknown.
   */
  public const RUNTIME_VERSION_RUNTIME_VERSION_UNSPECIFIED = 'RUNTIME_VERSION_UNSPECIFIED';
  /**
   * Legacy rhino version of the Apps script runtime
   */
  public const RUNTIME_VERSION_DEPRECATED_ES5 = 'DEPRECATED_ES5';
  /**
   * Current default V8 version of the apps script runtime.
   */
  public const RUNTIME_VERSION_V8 = 'V8';
  /**
   * User access level unspecified
   */
  public const USER_ACCESS_LEVEL_USER_ACCESS_LEVEL_UNSPECIFIED = 'USER_ACCESS_LEVEL_UNSPECIFIED';
  /**
   * The user has no access.
   */
  public const USER_ACCESS_LEVEL_NONE = 'NONE';
  /**
   * The user has read-only access.
   */
  public const USER_ACCESS_LEVEL_READ = 'READ';
  /**
   * The user has write access.
   */
  public const USER_ACCESS_LEVEL_WRITE = 'WRITE';
  /**
   * The user is an owner.
   */
  public const USER_ACCESS_LEVEL_OWNER = 'OWNER';
  /**
   * Duration the execution spent executing.
   *
   * @var string
   */
  public $duration;
  /**
   * Name of the function the started the execution.
   *
   * @var string
   */
  public $functionName;
  /**
   * The executions status.
   *
   * @var string
   */
  public $processStatus;
  /**
   * The executions type.
   *
   * @var string
   */
  public $processType;
  /**
   * Name of the script being executed.
   *
   * @var string
   */
  public $projectName;
  /**
   * Which version of maestro to use to execute the script.
   *
   * @var string
   */
  public $runtimeVersion;
  /**
   * Time the execution started.
   *
   * @var string
   */
  public $startTime;
  /**
   * The executing users access level to the script.
   *
   * @var string
   */
  public $userAccessLevel;

  /**
   * Duration the execution spent executing.
   *
   * @param string $duration
   */
  public function setDuration($duration)
  {
    $this->duration = $duration;
  }
  /**
   * @return string
   */
  public function getDuration()
  {
    return $this->duration;
  }
  /**
   * Name of the function the started the execution.
   *
   * @param string $functionName
   */
  public function setFunctionName($functionName)
  {
    $this->functionName = $functionName;
  }
  /**
   * @return string
   */
  public function getFunctionName()
  {
    return $this->functionName;
  }
  /**
   * The executions status.
   *
   * Accepted values: PROCESS_STATUS_UNSPECIFIED, RUNNING, PAUSED, COMPLETED,
   * CANCELED, FAILED, TIMED_OUT, UNKNOWN, DELAYED, EXECUTION_DISABLED
   *
   * @param self::PROCESS_STATUS_* $processStatus
   */
  public function setProcessStatus($processStatus)
  {
    $this->processStatus = $processStatus;
  }
  /**
   * @return self::PROCESS_STATUS_*
   */
  public function getProcessStatus()
  {
    return $this->processStatus;
  }
  /**
   * The executions type.
   *
   * Accepted values: PROCESS_TYPE_UNSPECIFIED, ADD_ON, EXECUTION_API,
   * TIME_DRIVEN, TRIGGER, WEBAPP, EDITOR, SIMPLE_TRIGGER, MENU, BATCH_TASK
   *
   * @param self::PROCESS_TYPE_* $processType
   */
  public function setProcessType($processType)
  {
    $this->processType = $processType;
  }
  /**
   * @return self::PROCESS_TYPE_*
   */
  public function getProcessType()
  {
    return $this->processType;
  }
  /**
   * Name of the script being executed.
   *
   * @param string $projectName
   */
  public function setProjectName($projectName)
  {
    $this->projectName = $projectName;
  }
  /**
   * @return string
   */
  public function getProjectName()
  {
    return $this->projectName;
  }
  /**
   * Which version of maestro to use to execute the script.
   *
   * Accepted values: RUNTIME_VERSION_UNSPECIFIED, DEPRECATED_ES5, V8
   *
   * @param self::RUNTIME_VERSION_* $runtimeVersion
   */
  public function setRuntimeVersion($runtimeVersion)
  {
    $this->runtimeVersion = $runtimeVersion;
  }
  /**
   * @return self::RUNTIME_VERSION_*
   */
  public function getRuntimeVersion()
  {
    return $this->runtimeVersion;
  }
  /**
   * Time the execution started.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * The executing users access level to the script.
   *
   * Accepted values: USER_ACCESS_LEVEL_UNSPECIFIED, NONE, READ, WRITE, OWNER
   *
   * @param self::USER_ACCESS_LEVEL_* $userAccessLevel
   */
  public function setUserAccessLevel($userAccessLevel)
  {
    $this->userAccessLevel = $userAccessLevel;
  }
  /**
   * @return self::USER_ACCESS_LEVEL_*
   */
  public function getUserAccessLevel()
  {
    return $this->userAccessLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsScriptTypeProcess::class, 'Google_Service_Script_GoogleAppsScriptTypeProcess');
