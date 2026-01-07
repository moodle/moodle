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

namespace Google\Service\Batch;

class AgentTimingInfo extends \Google\Model
{
  /**
   * Agent startup time
   *
   * @var string
   */
  public $agentStartupTime;
  /**
   * Boot timestamp of the VM OS
   *
   * @var string
   */
  public $bootTime;
  /**
   * Startup time of the Batch VM script.
   *
   * @var string
   */
  public $scriptStartupTime;

  /**
   * Agent startup time
   *
   * @param string $agentStartupTime
   */
  public function setAgentStartupTime($agentStartupTime)
  {
    $this->agentStartupTime = $agentStartupTime;
  }
  /**
   * @return string
   */
  public function getAgentStartupTime()
  {
    return $this->agentStartupTime;
  }
  /**
   * Boot timestamp of the VM OS
   *
   * @param string $bootTime
   */
  public function setBootTime($bootTime)
  {
    $this->bootTime = $bootTime;
  }
  /**
   * @return string
   */
  public function getBootTime()
  {
    return $this->bootTime;
  }
  /**
   * Startup time of the Batch VM script.
   *
   * @param string $scriptStartupTime
   */
  public function setScriptStartupTime($scriptStartupTime)
  {
    $this->scriptStartupTime = $scriptStartupTime;
  }
  /**
   * @return string
   */
  public function getScriptStartupTime()
  {
    return $this->scriptStartupTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AgentTimingInfo::class, 'Google_Service_Batch_AgentTimingInfo');
