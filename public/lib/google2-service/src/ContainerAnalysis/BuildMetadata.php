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

namespace Google\Service\ContainerAnalysis;

class BuildMetadata extends \Google\Model
{
  /**
   * @var string
   */
  public $finishedOn;
  /**
   * @var string
   */
  public $invocationId;
  /**
   * @var string
   */
  public $startedOn;

  /**
   * @param string $finishedOn
   */
  public function setFinishedOn($finishedOn)
  {
    $this->finishedOn = $finishedOn;
  }
  /**
   * @return string
   */
  public function getFinishedOn()
  {
    return $this->finishedOn;
  }
  /**
   * @param string $invocationId
   */
  public function setInvocationId($invocationId)
  {
    $this->invocationId = $invocationId;
  }
  /**
   * @return string
   */
  public function getInvocationId()
  {
    return $this->invocationId;
  }
  /**
   * @param string $startedOn
   */
  public function setStartedOn($startedOn)
  {
    $this->startedOn = $startedOn;
  }
  /**
   * @return string
   */
  public function getStartedOn()
  {
    return $this->startedOn;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BuildMetadata::class, 'Google_Service_ContainerAnalysis_BuildMetadata');
