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

namespace Google\Service\Datastore;

class GoogleDatastoreAdminV1beta1Progress extends \Google\Model
{
  /**
   * The amount of work that has been completed. Note that this may be greater
   * than work_estimated.
   *
   * @var string
   */
  public $workCompleted;
  /**
   * An estimate of how much work needs to be performed. May be zero if the work
   * estimate is unavailable.
   *
   * @var string
   */
  public $workEstimated;

  /**
   * The amount of work that has been completed. Note that this may be greater
   * than work_estimated.
   *
   * @param string $workCompleted
   */
  public function setWorkCompleted($workCompleted)
  {
    $this->workCompleted = $workCompleted;
  }
  /**
   * @return string
   */
  public function getWorkCompleted()
  {
    return $this->workCompleted;
  }
  /**
   * An estimate of how much work needs to be performed. May be zero if the work
   * estimate is unavailable.
   *
   * @param string $workEstimated
   */
  public function setWorkEstimated($workEstimated)
  {
    $this->workEstimated = $workEstimated;
  }
  /**
   * @return string
   */
  public function getWorkEstimated()
  {
    return $this->workEstimated;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDatastoreAdminV1beta1Progress::class, 'Google_Service_Datastore_GoogleDatastoreAdminV1beta1Progress');
