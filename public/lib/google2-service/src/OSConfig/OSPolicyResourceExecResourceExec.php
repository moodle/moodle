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

namespace Google\Service\OSConfig;

class OSPolicyResourceExecResourceExec extends \Google\Collection
{
  /**
   * Invalid value, the request will return validation error.
   */
  public const INTERPRETER_INTERPRETER_UNSPECIFIED = 'INTERPRETER_UNSPECIFIED';
  /**
   * If an interpreter is not specified, the source is executed directly. This
   * execution, without an interpreter, only succeeds for executables and
   * scripts that have shebang lines.
   */
  public const INTERPRETER_NONE = 'NONE';
  /**
   * Indicates that the script runs with `/bin/sh` on Linux and `cmd.exe` on
   * Windows.
   */
  public const INTERPRETER_SHELL = 'SHELL';
  /**
   * Indicates that the script runs with PowerShell.
   */
  public const INTERPRETER_POWERSHELL = 'POWERSHELL';
  protected $collection_key = 'args';
  /**
   * Optional arguments to pass to the source during execution.
   *
   * @var string[]
   */
  public $args;
  protected $fileType = OSPolicyResourceFile::class;
  protected $fileDataType = '';
  /**
   * Required. The script interpreter to use.
   *
   * @var string
   */
  public $interpreter;
  /**
   * Only recorded for enforce Exec. Path to an output file (that is created by
   * this Exec) whose content will be recorded in OSPolicyResourceCompliance
   * after a successful run. Absence or failure to read this file will result in
   * this ExecResource being non-compliant. Output file size is limited to 500K
   * bytes.
   *
   * @var string
   */
  public $outputFilePath;
  /**
   * An inline script. The size of the script is limited to 32KiB.
   *
   * @var string
   */
  public $script;

  /**
   * Optional arguments to pass to the source during execution.
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
   * A remote or local file.
   *
   * @param OSPolicyResourceFile $file
   */
  public function setFile(OSPolicyResourceFile $file)
  {
    $this->file = $file;
  }
  /**
   * @return OSPolicyResourceFile
   */
  public function getFile()
  {
    return $this->file;
  }
  /**
   * Required. The script interpreter to use.
   *
   * Accepted values: INTERPRETER_UNSPECIFIED, NONE, SHELL, POWERSHELL
   *
   * @param self::INTERPRETER_* $interpreter
   */
  public function setInterpreter($interpreter)
  {
    $this->interpreter = $interpreter;
  }
  /**
   * @return self::INTERPRETER_*
   */
  public function getInterpreter()
  {
    return $this->interpreter;
  }
  /**
   * Only recorded for enforce Exec. Path to an output file (that is created by
   * this Exec) whose content will be recorded in OSPolicyResourceCompliance
   * after a successful run. Absence or failure to read this file will result in
   * this ExecResource being non-compliant. Output file size is limited to 500K
   * bytes.
   *
   * @param string $outputFilePath
   */
  public function setOutputFilePath($outputFilePath)
  {
    $this->outputFilePath = $outputFilePath;
  }
  /**
   * @return string
   */
  public function getOutputFilePath()
  {
    return $this->outputFilePath;
  }
  /**
   * An inline script. The size of the script is limited to 32KiB.
   *
   * @param string $script
   */
  public function setScript($script)
  {
    $this->script = $script;
  }
  /**
   * @return string
   */
  public function getScript()
  {
    return $this->script;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OSPolicyResourceExecResourceExec::class, 'Google_Service_OSConfig_OSPolicyResourceExecResourceExec');
