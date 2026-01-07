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

namespace Google\Service\SecurityCommandCenter;

class Process extends \Google\Collection
{
  protected $collection_key = 'libraries';
  /**
   * Process arguments as JSON encoded strings.
   *
   * @var string[]
   */
  public $args;
  /**
   * True if `args` is incomplete.
   *
   * @var bool
   */
  public $argumentsTruncated;
  protected $binaryType = SecuritycenterFile::class;
  protected $binaryDataType = '';
  protected $envVariablesType = EnvironmentVariable::class;
  protected $envVariablesDataType = 'array';
  /**
   * True if `env_variables` is incomplete.
   *
   * @var bool
   */
  public $envVariablesTruncated;
  protected $librariesType = SecuritycenterFile::class;
  protected $librariesDataType = 'array';
  /**
   * The process name, as displayed in utilities like `top` and `ps`. This name
   * can be accessed through `/proc/[pid]/comm` and changed with
   * `prctl(PR_SET_NAME)`.
   *
   * @var string
   */
  public $name;
  /**
   * The parent process ID.
   *
   * @var string
   */
  public $parentPid;
  /**
   * The process ID.
   *
   * @var string
   */
  public $pid;
  protected $scriptType = SecuritycenterFile::class;
  protected $scriptDataType = '';
  /**
   * The ID of the user that executed the process. E.g. If this is the root user
   * this will always be 0.
   *
   * @var string
   */
  public $userId;

  /**
   * Process arguments as JSON encoded strings.
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
   * True if `args` is incomplete.
   *
   * @param bool $argumentsTruncated
   */
  public function setArgumentsTruncated($argumentsTruncated)
  {
    $this->argumentsTruncated = $argumentsTruncated;
  }
  /**
   * @return bool
   */
  public function getArgumentsTruncated()
  {
    return $this->argumentsTruncated;
  }
  /**
   * File information for the process executable.
   *
   * @param SecuritycenterFile $binary
   */
  public function setBinary(SecuritycenterFile $binary)
  {
    $this->binary = $binary;
  }
  /**
   * @return SecuritycenterFile
   */
  public function getBinary()
  {
    return $this->binary;
  }
  /**
   * Process environment variables.
   *
   * @param EnvironmentVariable[] $envVariables
   */
  public function setEnvVariables($envVariables)
  {
    $this->envVariables = $envVariables;
  }
  /**
   * @return EnvironmentVariable[]
   */
  public function getEnvVariables()
  {
    return $this->envVariables;
  }
  /**
   * True if `env_variables` is incomplete.
   *
   * @param bool $envVariablesTruncated
   */
  public function setEnvVariablesTruncated($envVariablesTruncated)
  {
    $this->envVariablesTruncated = $envVariablesTruncated;
  }
  /**
   * @return bool
   */
  public function getEnvVariablesTruncated()
  {
    return $this->envVariablesTruncated;
  }
  /**
   * File information for libraries loaded by the process.
   *
   * @param SecuritycenterFile[] $libraries
   */
  public function setLibraries($libraries)
  {
    $this->libraries = $libraries;
  }
  /**
   * @return SecuritycenterFile[]
   */
  public function getLibraries()
  {
    return $this->libraries;
  }
  /**
   * The process name, as displayed in utilities like `top` and `ps`. This name
   * can be accessed through `/proc/[pid]/comm` and changed with
   * `prctl(PR_SET_NAME)`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The parent process ID.
   *
   * @param string $parentPid
   */
  public function setParentPid($parentPid)
  {
    $this->parentPid = $parentPid;
  }
  /**
   * @return string
   */
  public function getParentPid()
  {
    return $this->parentPid;
  }
  /**
   * The process ID.
   *
   * @param string $pid
   */
  public function setPid($pid)
  {
    $this->pid = $pid;
  }
  /**
   * @return string
   */
  public function getPid()
  {
    return $this->pid;
  }
  /**
   * When the process represents the invocation of a script, `binary` provides
   * information about the interpreter, while `script` provides information
   * about the script file provided to the interpreter.
   *
   * @param SecuritycenterFile $script
   */
  public function setScript(SecuritycenterFile $script)
  {
    $this->script = $script;
  }
  /**
   * @return SecuritycenterFile
   */
  public function getScript()
  {
    return $this->script;
  }
  /**
   * The ID of the user that executed the process. E.g. If this is the root user
   * this will always be 0.
   *
   * @param string $userId
   */
  public function setUserId($userId)
  {
    $this->userId = $userId;
  }
  /**
   * @return string
   */
  public function getUserId()
  {
    return $this->userId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Process::class, 'Google_Service_SecurityCommandCenter_Process');
