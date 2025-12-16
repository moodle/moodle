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

class GoogleCloudDataplexV1SessionEvent extends \Google\Model
{
  /**
   * An unspecified event type.
   */
  public const TYPE_EVENT_TYPE_UNSPECIFIED = 'EVENT_TYPE_UNSPECIFIED';
  /**
   * Event when the session is assigned to a user.
   */
  public const TYPE_START = 'START';
  /**
   * Event for stop of a session.
   */
  public const TYPE_STOP = 'STOP';
  /**
   * Query events in the session.
   */
  public const TYPE_QUERY = 'QUERY';
  /**
   * Event for creation of a cluster. It is not yet assigned to a user. This
   * comes before START in the sequence
   */
  public const TYPE_CREATE = 'CREATE';
  /**
   * The status of the event.
   *
   * @var bool
   */
  public $eventSucceeded;
  /**
   * If the session is associated with an environment with fast startup enabled,
   * and was created before being assigned to a user.
   *
   * @var bool
   */
  public $fastStartupEnabled;
  /**
   * The log message.
   *
   * @var string
   */
  public $message;
  protected $queryType = GoogleCloudDataplexV1SessionEventQueryDetail::class;
  protected $queryDataType = '';
  /**
   * Unique identifier for the session.
   *
   * @var string
   */
  public $sessionId;
  /**
   * The type of the event.
   *
   * @var string
   */
  public $type;
  /**
   * The idle duration of a warm pooled session before it is assigned to user.
   *
   * @var string
   */
  public $unassignedDuration;
  /**
   * The information about the user that created the session. It will be the
   * email address of the user.
   *
   * @var string
   */
  public $userId;

  /**
   * The status of the event.
   *
   * @param bool $eventSucceeded
   */
  public function setEventSucceeded($eventSucceeded)
  {
    $this->eventSucceeded = $eventSucceeded;
  }
  /**
   * @return bool
   */
  public function getEventSucceeded()
  {
    return $this->eventSucceeded;
  }
  /**
   * If the session is associated with an environment with fast startup enabled,
   * and was created before being assigned to a user.
   *
   * @param bool $fastStartupEnabled
   */
  public function setFastStartupEnabled($fastStartupEnabled)
  {
    $this->fastStartupEnabled = $fastStartupEnabled;
  }
  /**
   * @return bool
   */
  public function getFastStartupEnabled()
  {
    return $this->fastStartupEnabled;
  }
  /**
   * The log message.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * The execution details of the query.
   *
   * @param GoogleCloudDataplexV1SessionEventQueryDetail $query
   */
  public function setQuery(GoogleCloudDataplexV1SessionEventQueryDetail $query)
  {
    $this->query = $query;
  }
  /**
   * @return GoogleCloudDataplexV1SessionEventQueryDetail
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * Unique identifier for the session.
   *
   * @param string $sessionId
   */
  public function setSessionId($sessionId)
  {
    $this->sessionId = $sessionId;
  }
  /**
   * @return string
   */
  public function getSessionId()
  {
    return $this->sessionId;
  }
  /**
   * The type of the event.
   *
   * Accepted values: EVENT_TYPE_UNSPECIFIED, START, STOP, QUERY, CREATE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * The idle duration of a warm pooled session before it is assigned to user.
   *
   * @param string $unassignedDuration
   */
  public function setUnassignedDuration($unassignedDuration)
  {
    $this->unassignedDuration = $unassignedDuration;
  }
  /**
   * @return string
   */
  public function getUnassignedDuration()
  {
    return $this->unassignedDuration;
  }
  /**
   * The information about the user that created the session. It will be the
   * email address of the user.
   *
   * @param string $userId
   */
  public function setUserId($userId)
  {
    $this->userId = $userId;
  }
  /**
   * @return string
   */
  public function getUserId()
  {
    return $this->userId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1SessionEvent::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1SessionEvent');
