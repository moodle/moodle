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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1OperationMetadataProgress extends \Google\Model
{
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  public const STATE_NOT_STARTED = 'NOT_STARTED';
  public const STATE_IN_PROGRESS = 'IN_PROGRESS';
  public const STATE_FINISHED = 'FINISHED';
  /**
   * Description of the operation's progress.
   *
   * @var string
   */
  public $description;
  /**
   * The additional details of the progress.
   *
   * @var array[]
   */
  public $details;
  /**
   * The percentage of the operation progress.
   *
   * @var int
   */
  public $percentDone;
  /**
   * State of the operation.
   *
   * @var string
   */
  public $state;

  /**
   * Description of the operation's progress.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The additional details of the progress.
   *
   * @param array[] $details
   */
  public function setDetails($details)
  {
    $this->details = $details;
  }
  /**
   * @return array[]
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * The percentage of the operation progress.
   *
   * @param int $percentDone
   */
  public function setPercentDone($percentDone)
  {
    $this->percentDone = $percentDone;
  }
  /**
   * @return int
   */
  public function getPercentDone()
  {
    return $this->percentDone;
  }
  /**
   * State of the operation.
   *
   * Accepted values: STATE_UNSPECIFIED, NOT_STARTED, IN_PROGRESS, FINISHED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1OperationMetadataProgress::class, 'Google_Service_Apigee_GoogleCloudApigeeV1OperationMetadataProgress');
