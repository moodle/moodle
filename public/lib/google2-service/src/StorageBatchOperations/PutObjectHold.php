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

namespace Google\Service\StorageBatchOperations;

class PutObjectHold extends \Google\Model
{
  /**
   * Default value, Object hold status will not be changed.
   */
  public const EVENT_BASED_HOLD_HOLD_STATUS_UNSPECIFIED = 'HOLD_STATUS_UNSPECIFIED';
  /**
   * Places the hold.
   */
  public const EVENT_BASED_HOLD_SET = 'SET';
  /**
   * Releases the hold.
   */
  public const EVENT_BASED_HOLD_UNSET = 'UNSET';
  /**
   * Default value, Object hold status will not be changed.
   */
  public const TEMPORARY_HOLD_HOLD_STATUS_UNSPECIFIED = 'HOLD_STATUS_UNSPECIFIED';
  /**
   * Places the hold.
   */
  public const TEMPORARY_HOLD_SET = 'SET';
  /**
   * Releases the hold.
   */
  public const TEMPORARY_HOLD_UNSET = 'UNSET';
  /**
   * Required. Updates object event based holds state. When object event based
   * hold is set, object cannot be deleted or replaced. Resets object's time in
   * the bucket for the purposes of the retention period.
   *
   * @var string
   */
  public $eventBasedHold;
  /**
   * Required. Updates object temporary holds state. When object temporary hold
   * is set, object cannot be deleted or replaced.
   *
   * @var string
   */
  public $temporaryHold;

  /**
   * Required. Updates object event based holds state. When object event based
   * hold is set, object cannot be deleted or replaced. Resets object's time in
   * the bucket for the purposes of the retention period.
   *
   * Accepted values: HOLD_STATUS_UNSPECIFIED, SET, UNSET
   *
   * @param self::EVENT_BASED_HOLD_* $eventBasedHold
   */
  public function setEventBasedHold($eventBasedHold)
  {
    $this->eventBasedHold = $eventBasedHold;
  }
  /**
   * @return self::EVENT_BASED_HOLD_*
   */
  public function getEventBasedHold()
  {
    return $this->eventBasedHold;
  }
  /**
   * Required. Updates object temporary holds state. When object temporary hold
   * is set, object cannot be deleted or replaced.
   *
   * Accepted values: HOLD_STATUS_UNSPECIFIED, SET, UNSET
   *
   * @param self::TEMPORARY_HOLD_* $temporaryHold
   */
  public function setTemporaryHold($temporaryHold)
  {
    $this->temporaryHold = $temporaryHold;
  }
  /**
   * @return self::TEMPORARY_HOLD_*
   */
  public function getTemporaryHold()
  {
    return $this->temporaryHold;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PutObjectHold::class, 'Google_Service_StorageBatchOperations_PutObjectHold');
