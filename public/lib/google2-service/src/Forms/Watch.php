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

namespace Google\Service\Forms;

class Watch extends \Google\Model
{
  /**
   * Unspecified error type.
   */
  public const ERROR_TYPE_ERROR_TYPE_UNSPECIFIED = 'ERROR_TYPE_UNSPECIFIED';
  /**
   * The cloud project does not have access to the form being watched. This
   * occurs if the user has revoked the authorization for your project to access
   * their form(s). Watches with this error will not be retried. To attempt to
   * begin watching the form again a call can be made to watches.renew
   */
  public const ERROR_TYPE_PROJECT_NOT_AUTHORIZED = 'PROJECT_NOT_AUTHORIZED';
  /**
   * The user that granted access no longer has access to the form being
   * watched. Watches with this error will not be retried. To attempt to begin
   * watching the form again a call can be made to watches.renew
   */
  public const ERROR_TYPE_NO_USER_ACCESS = 'NO_USER_ACCESS';
  /**
   * Another type of error has occurred. Whether notifications will continue
   * depends on the watch state.
   */
  public const ERROR_TYPE_OTHER_ERRORS = 'OTHER_ERRORS';
  /**
   * Unspecified event type. This value should not be used.
   */
  public const EVENT_TYPE_EVENT_TYPE_UNSPECIFIED = 'EVENT_TYPE_UNSPECIFIED';
  /**
   * The schema event type. A watch with this event type will be notified about
   * changes to form content and settings.
   */
  public const EVENT_TYPE_SCHEMA = 'SCHEMA';
  /**
   * The responses event type. A watch with this event type will be notified
   * when form responses are submitted.
   */
  public const EVENT_TYPE_RESPONSES = 'RESPONSES';
  /**
   * Unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Watch is active.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The watch is suspended due to an error that may be resolved. The watch will
   * continue to exist until it expires. To attempt to reactivate the watch a
   * call can be made to watches.renew
   */
  public const STATE_SUSPENDED = 'SUSPENDED';
  /**
   * Output only. Timestamp of when this was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The most recent error type for an attempted delivery. To begin
   * watching the form again a call can be made to watches.renew which also
   * clears this error information.
   *
   * @var string
   */
  public $errorType;
  /**
   * Required. Which event type to watch for.
   *
   * @var string
   */
  public $eventType;
  /**
   * Output only. Timestamp for when this will expire. Each watches.renew call
   * resets this to seven days in the future.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Output only. The ID of this watch. See notes on
   * CreateWatchRequest.watch_id.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. The current state of the watch. Additional details about
   * suspended watches can be found by checking the `error_type`.
   *
   * @var string
   */
  public $state;
  protected $targetType = WatchTarget::class;
  protected $targetDataType = '';

  /**
   * Output only. Timestamp of when this was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. The most recent error type for an attempted delivery. To begin
   * watching the form again a call can be made to watches.renew which also
   * clears this error information.
   *
   * Accepted values: ERROR_TYPE_UNSPECIFIED, PROJECT_NOT_AUTHORIZED,
   * NO_USER_ACCESS, OTHER_ERRORS
   *
   * @param self::ERROR_TYPE_* $errorType
   */
  public function setErrorType($errorType)
  {
    $this->errorType = $errorType;
  }
  /**
   * @return self::ERROR_TYPE_*
   */
  public function getErrorType()
  {
    return $this->errorType;
  }
  /**
   * Required. Which event type to watch for.
   *
   * Accepted values: EVENT_TYPE_UNSPECIFIED, SCHEMA, RESPONSES
   *
   * @param self::EVENT_TYPE_* $eventType
   */
  public function setEventType($eventType)
  {
    $this->eventType = $eventType;
  }
  /**
   * @return self::EVENT_TYPE_*
   */
  public function getEventType()
  {
    return $this->eventType;
  }
  /**
   * Output only. Timestamp for when this will expire. Each watches.renew call
   * resets this to seven days in the future.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Output only. The ID of this watch. See notes on
   * CreateWatchRequest.watch_id.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. The current state of the watch. Additional details about
   * suspended watches can be found by checking the `error_type`.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, SUSPENDED
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
  /**
   * Required. Where to send the notification.
   *
   * @param WatchTarget $target
   */
  public function setTarget(WatchTarget $target)
  {
    $this->target = $target;
  }
  /**
   * @return WatchTarget
   */
  public function getTarget()
  {
    return $this->target;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Watch::class, 'Google_Service_Forms_Watch');
