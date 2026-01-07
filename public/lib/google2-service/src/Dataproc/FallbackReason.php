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

class FallbackReason extends \Google\Model
{
  /**
   * Optional. Fallback node information.
   *
   * @var string
   */
  public $fallbackNode;
  /**
   * Optional. Fallback to Spark reason.
   *
   * @var string
   */
  public $fallbackReason;

  /**
   * Optional. Fallback node information.
   *
   * @param string $fallbackNode
   */
  public function setFallbackNode($fallbackNode)
  {
    $this->fallbackNode = $fallbackNode;
  }
  /**
   * @return string
   */
  public function getFallbackNode()
  {
    return $this->fallbackNode;
  }
  /**
   * Optional. Fallback to Spark reason.
   *
   * @param string $fallbackReason
   */
  public function setFallbackReason($fallbackReason)
  {
    $this->fallbackReason = $fallbackReason;
  }
  /**
   * @return string
   */
  public function getFallbackReason()
  {
    return $this->fallbackReason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FallbackReason::class, 'Google_Service_Dataproc_FallbackReason');
