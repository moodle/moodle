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

namespace Google\Service\WorkloadManager;

class Command extends \Google\Model
{
  protected $agentCommandType = AgentCommand::class;
  protected $agentCommandDataType = '';
  protected $shellCommandType = ShellCommand::class;
  protected $shellCommandDataType = '';

  /**
   * AgentCommand specifies a one-time executable program for the agent to run.
   *
   * @param AgentCommand $agentCommand
   */
  public function setAgentCommand(AgentCommand $agentCommand)
  {
    $this->agentCommand = $agentCommand;
  }
  /**
   * @return AgentCommand
   */
  public function getAgentCommand()
  {
    return $this->agentCommand;
  }
  /**
   * ShellCommand is invoked via the agent's command line executor.
   *
   * @param ShellCommand $shellCommand
   */
  public function setShellCommand(ShellCommand $shellCommand)
  {
    $this->shellCommand = $shellCommand;
  }
  /**
   * @return ShellCommand
   */
  public function getShellCommand()
  {
    return $this->shellCommand;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Command::class, 'Google_Service_WorkloadManager_Command');
