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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1EntityCompatibilityStatusCompatibility extends \Google\Model
{
  /**
   * Output only. Whether the entity is compatible and can be represented in the
   * metadata store.
   *
   * @var bool
   */
  public $compatible;
  /**
   * Output only. Provides additional detail if the entity is incompatible with
   * the metadata store.
   *
   * @var string
   */
  public $reason;

  /**
   * Output only. Whether the entity is compatible and can be represented in the
   * metadata store.
   *
   * @param bool $compatible
   */
  public function setCompatible($compatible)
  {
    $this->compatible = $compatible;
  }
  /**
   * @return bool
   */
  public function getCompatible()
  {
    return $this->compatible;
  }
  /**
   * Output only. Provides additional detail if the entity is incompatible with
   * the metadata store.
   *
   * @param string $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return string
   */
  public function getReason()
  {
    return $this->reason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1EntityCompatibilityStatusCompatibility::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1EntityCompatibilityStatusCompatibility');
