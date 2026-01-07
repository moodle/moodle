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

namespace Google\Service\ToolResults;

class ToolExecution extends \Google\Collection
{
  protected $collection_key = 'toolOutputs';
  /**
   * The full tokenized command line including the program name (equivalent to
   * argv in a C program). - In response: present if set by create request - In
   * create request: optional - In update request: never set
   *
   * @var string[]
   */
  public $commandLineArguments;
  protected $exitCodeType = ToolExitCode::class;
  protected $exitCodeDataType = '';
  protected $toolLogsType = FileReference::class;
  protected $toolLogsDataType = 'array';
  protected $toolOutputsType = ToolOutputReference::class;
  protected $toolOutputsDataType = 'array';

  /**
   * The full tokenized command line including the program name (equivalent to
   * argv in a C program). - In response: present if set by create request - In
   * create request: optional - In update request: never set
   *
   * @param string[] $commandLineArguments
   */
  public function setCommandLineArguments($commandLineArguments)
  {
    $this->commandLineArguments = $commandLineArguments;
  }
  /**
   * @return string[]
   */
  public function getCommandLineArguments()
  {
    return $this->commandLineArguments;
  }
  /**
   * Tool execution exit code. This field will be set once the tool has exited.
   * - In response: present if set by create/update request - In create request:
   * optional - In update request: optional, a FAILED_PRECONDITION error will be
   * returned if an exit_code is already set.
   *
   * @param ToolExitCode $exitCode
   */
  public function setExitCode(ToolExitCode $exitCode)
  {
    $this->exitCode = $exitCode;
  }
  /**
   * @return ToolExitCode
   */
  public function getExitCode()
  {
    return $this->exitCode;
  }
  /**
   * References to any plain text logs output the tool execution. This field can
   * be set before the tool has exited in order to be able to have access to a
   * live view of the logs while the tool is running. The maximum allowed number
   * of tool logs per step is 1000. - In response: present if set by
   * create/update request - In create request: optional - In update request:
   * optional, any value provided will be appended to the existing list
   *
   * @param FileReference[] $toolLogs
   */
  public function setToolLogs($toolLogs)
  {
    $this->toolLogs = $toolLogs;
  }
  /**
   * @return FileReference[]
   */
  public function getToolLogs()
  {
    return $this->toolLogs;
  }
  /**
   * References to opaque files of any format output by the tool execution. The
   * maximum allowed number of tool outputs per step is 1000. - In response:
   * present if set by create/update request - In create request: optional - In
   * update request: optional, any value provided will be appended to the
   * existing list
   *
   * @param ToolOutputReference[] $toolOutputs
   */
  public function setToolOutputs($toolOutputs)
  {
    $this->toolOutputs = $toolOutputs;
  }
  /**
   * @return ToolOutputReference[]
   */
  public function getToolOutputs()
  {
    return $this->toolOutputs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ToolExecution::class, 'Google_Service_ToolResults_ToolExecution');
