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

namespace Google\Service\Dataproc;

class AppSummary extends \Google\Model
{
  /**
   * @var int
   */
  public $numCompletedJobs;
  /**
   * @var int
   */
  public $numCompletedStages;

  /**
   * @param int $numCompletedJobs
   */
  public function setNumCompletedJobs($numCompletedJobs)
  {
    $this->numCompletedJobs = $numCompletedJobs;
  }
  /**
   * @return int
   */
  public function getNumCompletedJobs()
  {
    return $this->numCompletedJobs;
  }
  /**
   * @param int $numCompletedStages
   */
  public function setNumCompletedStages($numCompletedStages)
  {
    $this->numCompletedStages = $numCompletedStages;
  }
  /**
   * @return int
   */
  public function getNumCompletedStages()
  {
    return $this->numCompletedStages;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppSummary::class, 'Google_Service_Dataproc_AppSummary');
