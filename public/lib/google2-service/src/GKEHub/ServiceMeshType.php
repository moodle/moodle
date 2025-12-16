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

namespace Google\Service\GKEHub;

class ServiceMeshType extends \Google\Model
{
  /**
   * A 7 character code matching `^IST[0-9]{4}$` or `^ASM[0-9]{4}$`, intended to
   * uniquely identify the message type. (e.g. "IST0001" is mapped to the
   * "InternalError" message type.)
   *
   * @var string
   */
  public $code;
  /**
   * A human-readable name for the message type. e.g. "InternalError",
   * "PodMissingProxy". This should be the same for all messages of the same
   * type. (This corresponds to the `name` field in open-source Istio.)
   *
   * @var string
   */
  public $displayName;

  /**
   * A 7 character code matching `^IST[0-9]{4}$` or `^ASM[0-9]{4}$`, intended to
   * uniquely identify the message type. (e.g. "IST0001" is mapped to the
   * "InternalError" message type.)
   *
   * @param string $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * A human-readable name for the message type. e.g. "InternalError",
   * "PodMissingProxy". This should be the same for all messages of the same
   * type. (This corresponds to the `name` field in open-source Istio.)
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceMeshType::class, 'Google_Service_GKEHub_ServiceMeshType');
