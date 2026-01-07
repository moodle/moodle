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

namespace Google\Service\CloudDeploy;

class Targets extends \Google\Model
{
  /**
   * Optional. The destination target ID.
   *
   * @var string
   */
  public $destinationTargetId;
  /**
   * Optional. The source target ID.
   *
   * @var string
   */
  public $sourceTargetId;

  /**
   * Optional. The destination target ID.
   *
   * @param string $destinationTargetId
   */
  public function setDestinationTargetId($destinationTargetId)
  {
    $this->destinationTargetId = $destinationTargetId;
  }
  /**
   * @return string
   */
  public function getDestinationTargetId()
  {
    return $this->destinationTargetId;
  }
  /**
   * Optional. The source target ID.
   *
   * @param string $sourceTargetId
   */
  public function setSourceTargetId($sourceTargetId)
  {
    $this->sourceTargetId = $sourceTargetId;
  }
  /**
   * @return string
   */
  public function getSourceTargetId()
  {
    return $this->sourceTargetId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Targets::class, 'Google_Service_CloudDeploy_Targets');
