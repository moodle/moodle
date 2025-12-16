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

namespace Google\Service\Spanner;

class MoveOutEvent extends \Google\Model
{
  /**
   * An unique partition identifier describing the destination change stream
   * partition that will record changes for the key range that is moving out of
   * this partition.
   *
   * @var string
   */
  public $destinationPartitionToken;

  /**
   * An unique partition identifier describing the destination change stream
   * partition that will record changes for the key range that is moving out of
   * this partition.
   *
   * @param string $destinationPartitionToken
   */
  public function setDestinationPartitionToken($destinationPartitionToken)
  {
    $this->destinationPartitionToken = $destinationPartitionToken;
  }
  /**
   * @return string
   */
  public function getDestinationPartitionToken()
  {
    return $this->destinationPartitionToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MoveOutEvent::class, 'Google_Service_Spanner_MoveOutEvent');
