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

class AgentTaskRunnable extends \Google\Model
{
  /**
   * By default, after a Runnable fails, no further Runnable are executed. This
   * flag indicates that this Runnable must be run even if the Task has already
   * failed. This is useful for Runnables that copy output files off of the VM
   * or for debugging. The always_run flag does not override the Task's overall
   * max_run_duration. If the max_run_duration has expired then no further
   * Runnables will execute, not even always_run Runnables.
   *
   * @var bool
   */
  public $alwaysRun;
  /**
   * This flag allows a Runnable to continue running in the background while the
   * Task executes subsequent Runnables. This is useful to provide services to
   * other Runnables (or to provide debugging support tools like SSH servers).
   *
   * @var bool
   */
  public $background;
  protected $containerType = AgentContainer::class;
  protected $containerDataType = '';
  protected $environmentType = AgentEnvironment::class;
  protected $environmentDataType = '';
  /**
   * Normally, a non-zero exit status causes the Task to fail. This flag allows
   * execution of other Runnables to continue instead.
   *
   * @var bool
   */
  public $ignoreExitStatus;
  protected $scriptType = AgentScript::class;
  protected $scriptDataType = '';
  /**
   * Timeout for this Runnable.
   *
   * @var string
   */
  public $timeout;

  /**
   * By default, after a Runnable fails, no further Runnable are executed. This
   * flag indicates that this Runnable must be run even if the Task has already
   * failed. This is useful for Runnables that copy output files off of the VM
   * or for debugging. The always_run flag does not override the Task's overall
   * max_run_duration. If the max_run_duration has expired then no further
   * Runnables will execute, not even always_run Runnables.
   *
   * @param bool $alwaysRun
   */
  public function setAlwaysRun($alwaysRun)
  {
    $this->alwaysRun = $alwaysRun;
  }
  /**
   * @return bool
   */
  public function getAlwaysRun()
  {
    return $this->alwaysRun;
  }
  /**
   * This flag allows a Runnable to continue running in the background while the
   * Task executes subsequent Runnables. This is useful to provide services to
   * other Runnables (or to provide debugging support tools like SSH servers).
   *
   * @param bool $background
   */
  public function setBackground($background)
  {
    $this->background = $background;
  }
  /**
   * @return bool
   */
  public function getBackground()
  {
    return $this->background;
  }
  /**
   * Container runnable.
   *
   * @param AgentContainer $container
   */
  public function setContainer(AgentContainer $container)
  {
    $this->container = $container;
  }
  /**
   * @return AgentContainer
   */
  public function getContainer()
  {
    return $this->container;
  }
  /**
   * Environment variables for this Runnable (overrides variables set for the
   * whole Task or TaskGroup).
   *
   * @param AgentEnvironment $environment
   */
  public function setEnvironment(AgentEnvironment $environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return AgentEnvironment
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * Normally, a non-zero exit status causes the Task to fail. This flag allows
   * execution of other Runnables to continue instead.
   *
   * @param bool $ignoreExitStatus
   */
  public function setIgnoreExitStatus($ignoreExitStatus)
  {
    $this->ignoreExitStatus = $ignoreExitStatus;
  }
  /**
   * @return bool
   */
  public function getIgnoreExitStatus()
  {
    return $this->ignoreExitStatus;
  }
  /**
   * Script runnable.
   *
   * @param AgentScript $script
   */
  public function setScript(AgentScript $script)
  {
    $this->script = $script;
  }
  /**
   * @return AgentScript
   */
  public function getScript()
  {
    return $this->script;
  }
  /**
   * Timeout for this Runnable.
   *
   * @param string $timeout
   */
  public function setTimeout($timeout)
  {
    $this->timeout = $timeout;
  }
  /**
   * @return string
   */
  public function getTimeout()
  {
    return $this->timeout;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AgentTaskRunnable::class, 'Google_Service_Batch_AgentTaskRunnable');
