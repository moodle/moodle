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

class Runnable extends \Google\Model
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
   * Normally, a runnable that doesn't exit causes its task to fail. However,
   * you can set this field to `true` to configure a background runnable.
   * Background runnables are allowed continue running in the background while
   * the task executes subsequent runnables. For example, background runnables
   * are useful for providing services to other runnables or providing
   * debugging-support tools like SSH servers. Specifically, background
   * runnables are killed automatically (if they have not already exited) a
   * short time after all foreground runnables have completed. Even though this
   * is likely to result in a non-zero exit status for the background runnable,
   * these automatic kills are not treated as task failures.
   *
   * @var bool
   */
  public $background;
  protected $barrierType = Barrier::class;
  protected $barrierDataType = '';
  protected $containerType = Container::class;
  protected $containerDataType = '';
  /**
   * Optional. DisplayName is an optional field that can be provided by the
   * caller. If provided, it will be used in logs and other outputs to identify
   * the script, making it easier for users to understand the logs. If not
   * provided the index of the runnable will be used for outputs.
   *
   * @var string
   */
  public $displayName;
  protected $environmentType = Environment::class;
  protected $environmentDataType = '';
  /**
   * Normally, a runnable that returns a non-zero exit status fails and causes
   * the task to fail. However, you can set this field to `true` to allow the
   * task to continue executing its other runnables even if this runnable fails.
   *
   * @var bool
   */
  public $ignoreExitStatus;
  /**
   * Labels for this Runnable.
   *
   * @var string[]
   */
  public $labels;
  protected $scriptType = Script::class;
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
   * Normally, a runnable that doesn't exit causes its task to fail. However,
   * you can set this field to `true` to configure a background runnable.
   * Background runnables are allowed continue running in the background while
   * the task executes subsequent runnables. For example, background runnables
   * are useful for providing services to other runnables or providing
   * debugging-support tools like SSH servers. Specifically, background
   * runnables are killed automatically (if they have not already exited) a
   * short time after all foreground runnables have completed. Even though this
   * is likely to result in a non-zero exit status for the background runnable,
   * these automatic kills are not treated as task failures.
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
   * Barrier runnable.
   *
   * @param Barrier $barrier
   */
  public function setBarrier(Barrier $barrier)
  {
    $this->barrier = $barrier;
  }
  /**
   * @return Barrier
   */
  public function getBarrier()
  {
    return $this->barrier;
  }
  /**
   * Container runnable.
   *
   * @param Container $container
   */
  public function setContainer(Container $container)
  {
    $this->container = $container;
  }
  /**
   * @return Container
   */
  public function getContainer()
  {
    return $this->container;
  }
  /**
   * Optional. DisplayName is an optional field that can be provided by the
   * caller. If provided, it will be used in logs and other outputs to identify
   * the script, making it easier for users to understand the logs. If not
   * provided the index of the runnable will be used for outputs.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Environment variables for this Runnable (overrides variables set for the
   * whole Task or TaskGroup).
   *
   * @param Environment $environment
   */
  public function setEnvironment(Environment $environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return Environment
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * Normally, a runnable that returns a non-zero exit status fails and causes
   * the task to fail. However, you can set this field to `true` to allow the
   * task to continue executing its other runnables even if this runnable fails.
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
   * Labels for this Runnable.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Script runnable.
   *
   * @param Script $script
   */
  public function setScript(Script $script)
  {
    $this->script = $script;
  }
  /**
   * @return Script
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
class_alias(Runnable::class, 'Google_Service_Batch_Runnable');
