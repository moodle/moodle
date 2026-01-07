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

class AndroidRoboTest extends \Google\Model
{
  /**
   * The initial activity that should be used to start the app. Optional
   *
   * @var string
   */
  public $appInitialActivity;
  /**
   * The java package for the bootstrap. Optional
   *
   * @var string
   */
  public $bootstrapPackageId;
  /**
   * The runner class for the bootstrap. Optional
   *
   * @var string
   */
  public $bootstrapRunnerClass;
  /**
   * The max depth of the traversal stack Robo can explore. Optional
   *
   * @var int
   */
  public $maxDepth;
  /**
   * The max number of steps/actions Robo can execute. Default is no limit (0).
   * Optional
   *
   * @var int
   */
  public $maxSteps;

  /**
   * The initial activity that should be used to start the app. Optional
   *
   * @param string $appInitialActivity
   */
  public function setAppInitialActivity($appInitialActivity)
  {
    $this->appInitialActivity = $appInitialActivity;
  }
  /**
   * @return string
   */
  public function getAppInitialActivity()
  {
    return $this->appInitialActivity;
  }
  /**
   * The java package for the bootstrap. Optional
   *
   * @param string $bootstrapPackageId
   */
  public function setBootstrapPackageId($bootstrapPackageId)
  {
    $this->bootstrapPackageId = $bootstrapPackageId;
  }
  /**
   * @return string
   */
  public function getBootstrapPackageId()
  {
    return $this->bootstrapPackageId;
  }
  /**
   * The runner class for the bootstrap. Optional
   *
   * @param string $bootstrapRunnerClass
   */
  public function setBootstrapRunnerClass($bootstrapRunnerClass)
  {
    $this->bootstrapRunnerClass = $bootstrapRunnerClass;
  }
  /**
   * @return string
   */
  public function getBootstrapRunnerClass()
  {
    return $this->bootstrapRunnerClass;
  }
  /**
   * The max depth of the traversal stack Robo can explore. Optional
   *
   * @param int $maxDepth
   */
  public function setMaxDepth($maxDepth)
  {
    $this->maxDepth = $maxDepth;
  }
  /**
   * @return int
   */
  public function getMaxDepth()
  {
    return $this->maxDepth;
  }
  /**
   * The max number of steps/actions Robo can execute. Default is no limit (0).
   * Optional
   *
   * @param int $maxSteps
   */
  public function setMaxSteps($maxSteps)
  {
    $this->maxSteps = $maxSteps;
  }
  /**
   * @return int
   */
  public function getMaxSteps()
  {
    return $this->maxSteps;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AndroidRoboTest::class, 'Google_Service_ToolResults_AndroidRoboTest');
