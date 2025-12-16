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

namespace Google\Service\CloudBuild;

class Probe extends \Google\Model
{
  protected $execType = ExecAction::class;
  protected $execDataType = '';
  /**
   * Optional. How often (in seconds) to perform the probe. Default to 10
   * seconds. Minimum value is 1. +optional
   *
   * @var int
   */
  public $periodSeconds;

  /**
   * Optional. Exec specifies the action to take. +optional
   *
   * @param ExecAction $exec
   */
  public function setExec(ExecAction $exec)
  {
    $this->exec = $exec;
  }
  /**
   * @return ExecAction
   */
  public function getExec()
  {
    return $this->exec;
  }
  /**
   * Optional. How often (in seconds) to perform the probe. Default to 10
   * seconds. Minimum value is 1. +optional
   *
   * @param int $periodSeconds
   */
  public function setPeriodSeconds($periodSeconds)
  {
    $this->periodSeconds = $periodSeconds;
  }
  /**
   * @return int
   */
  public function getPeriodSeconds()
  {
    return $this->periodSeconds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Probe::class, 'Google_Service_CloudBuild_Probe');
