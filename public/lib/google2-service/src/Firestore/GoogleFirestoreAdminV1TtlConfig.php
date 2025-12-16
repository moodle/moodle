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

namespace Google\Service\Firestore;

class GoogleFirestoreAdminV1TtlConfig extends \Google\Model
{
  /**
   * The state is unspecified or unknown.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The TTL is being applied. There is an active long-running operation to
   * track the change. Newly written documents will have TTLs applied as
   * requested. Requested TTLs on existing documents are still being processed.
   * When TTLs on all existing documents have been processed, the state will
   * move to 'ACTIVE'.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The TTL is active for all documents.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The TTL configuration could not be enabled for all existing documents.
   * Newly written documents will continue to have their TTL applied. The LRO
   * returned when last attempting to enable TTL for this `Field` has failed,
   * and may have more details.
   */
  public const STATE_NEEDS_REPAIR = 'NEEDS_REPAIR';
  /**
   * Output only. The state of the TTL configuration.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The state of the TTL configuration.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, NEEDS_REPAIR
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
class_alias(GoogleFirestoreAdminV1TtlConfig::class, 'Google_Service_Firestore_GoogleFirestoreAdminV1TtlConfig');
