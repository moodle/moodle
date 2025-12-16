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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1ProbeExecAction extends \Google\Collection
{
  protected $collection_key = 'command';
  /**
   * Command is the command line to execute inside the container, the working
   * directory for the command is root ('/') in the container's filesystem. The
   * command is simply exec'd, it is not run inside a shell, so traditional
   * shell instructions ('|', etc) won't work. To use a shell, you need to
   * explicitly call out to that shell. Exit status of 0 is treated as
   * live/healthy and non-zero is unhealthy.
   *
   * @var string[]
   */
  public $command;

  /**
   * Command is the command line to execute inside the container, the working
   * directory for the command is root ('/') in the container's filesystem. The
   * command is simply exec'd, it is not run inside a shell, so traditional
   * shell instructions ('|', etc) won't work. To use a shell, you need to
   * explicitly call out to that shell. Exit status of 0 is treated as
   * live/healthy and non-zero is unhealthy.
   *
   * @param string[] $command
   */
  public function setCommand($command)
  {
    $this->command = $command;
  }
  /**
   * @return string[]
   */
  public function getCommand()
  {
    return $this->command;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ProbeExecAction::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ProbeExecAction');
