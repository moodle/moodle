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

class Summary extends \Google\Model
{
  /**
   * Output only. Number of failures
   *
   * @var string
   */
  public $failures;
  /**
   * Output only. Number of new failures compared to the previous execution
   *
   * @var string
   */
  public $newFailures;
  /**
   * Output only. Number of new fixes compared to the previous execution
   *
   * @var string
   */
  public $newFixes;

  /**
   * Output only. Number of failures
   *
   * @param string $failures
   */
  public function setFailures($failures)
  {
    $this->failures = $failures;
  }
  /**
   * @return string
   */
  public function getFailures()
  {
    return $this->failures;
  }
  /**
   * Output only. Number of new failures compared to the previous execution
   *
   * @param string $newFailures
   */
  public function setNewFailures($newFailures)
  {
    $this->newFailures = $newFailures;
  }
  /**
   * @return string
   */
  public function getNewFailures()
  {
    return $this->newFailures;
  }
  /**
   * Output only. Number of new fixes compared to the previous execution
   *
   * @param string $newFixes
   */
  public function setNewFixes($newFixes)
  {
    $this->newFixes = $newFixes;
  }
  /**
   * @return string
   */
  public function getNewFixes()
  {
    return $this->newFixes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Summary::class, 'Google_Service_WorkloadManager_Summary');
