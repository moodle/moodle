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

class ShellCommand extends \Google\Model
{
  /**
   * args is a string of arguments to be passed to the command.
   *
   * @var string
   */
  public $args;
  /**
   * command is the name of the command to be executed.
   *
   * @var string
   */
  public $command;
  /**
   * Optional. If not specified, the default timeout is 60 seconds.
   *
   * @var int
   */
  public $timeoutSeconds;

  /**
   * args is a string of arguments to be passed to the command.
   *
   * @param string $args
   */
  public function setArgs($args)
  {
    $this->args = $args;
  }
  /**
   * @return string
   */
  public function getArgs()
  {
    return $this->args;
  }
  /**
   * command is the name of the command to be executed.
   *
   * @param string $command
   */
  public function setCommand($command)
  {
    $this->command = $command;
  }
  /**
   * @return string
   */
  public function getCommand()
  {
    return $this->command;
  }
  /**
   * Optional. If not specified, the default timeout is 60 seconds.
   *
   * @param int $timeoutSeconds
   */
  public function setTimeoutSeconds($timeoutSeconds)
  {
    $this->timeoutSeconds = $timeoutSeconds;
  }
  /**
   * @return int
   */
  public function getTimeoutSeconds()
  {
    return $this->timeoutSeconds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ShellCommand::class, 'Google_Service_WorkloadManager_ShellCommand');
