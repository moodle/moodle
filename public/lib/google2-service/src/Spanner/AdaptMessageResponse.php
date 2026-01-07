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

class AdaptMessageResponse extends \Google\Model
{
  /**
   * Optional. Indicates whether this is the last AdaptMessageResponse in the
   * stream. This field may be optionally set by the server. Clients should not
   * rely on this field being set in all cases.
   *
   * @var bool
   */
  public $last;
  /**
   * Optional. Uninterpreted bytes from the underlying wire protocol.
   *
   * @var string
   */
  public $payload;
  /**
   * Optional. Opaque state updates to be applied by the client.
   *
   * @var string[]
   */
  public $stateUpdates;

  /**
   * Optional. Indicates whether this is the last AdaptMessageResponse in the
   * stream. This field may be optionally set by the server. Clients should not
   * rely on this field being set in all cases.
   *
   * @param bool $last
   */
  public function setLast($last)
  {
    $this->last = $last;
  }
  /**
   * @return bool
   */
  public function getLast()
  {
    return $this->last;
  }
  /**
   * Optional. Uninterpreted bytes from the underlying wire protocol.
   *
   * @param string $payload
   */
  public function setPayload($payload)
  {
    $this->payload = $payload;
  }
  /**
   * @return string
   */
  public function getPayload()
  {
    return $this->payload;
  }
  /**
   * Optional. Opaque state updates to be applied by the client.
   *
   * @param string[] $stateUpdates
   */
  public function setStateUpdates($stateUpdates)
  {
    $this->stateUpdates = $stateUpdates;
  }
  /**
   * @return string[]
   */
  public function getStateUpdates()
  {
    return $this->stateUpdates;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdaptMessageResponse::class, 'Google_Service_Spanner_AdaptMessageResponse');
