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

namespace Google\Service\Storagetransfer;

class EventStream extends \Google\Model
{
  /**
   * Specifies the data and time at which Storage Transfer Service stops
   * listening for events from this stream. After this time, any transfers in
   * progress will complete, but no new transfers are initiated.
   *
   * @var string
   */
  public $eventStreamExpirationTime;
  /**
   * Specifies the date and time that Storage Transfer Service starts listening
   * for events from this stream. If no start time is specified or start time is
   * in the past, Storage Transfer Service starts listening immediately.
   *
   * @var string
   */
  public $eventStreamStartTime;
  /**
   * Required. Specifies a unique name of the resource such as AWS SQS ARN in
   * the form 'arn:aws:sqs:region:account_id:queue_name', or Pub/Sub
   * subscription resource name in the form
   * 'projects/{project}/subscriptions/{sub}'.
   *
   * @var string
   */
  public $name;

  /**
   * Specifies the data and time at which Storage Transfer Service stops
   * listening for events from this stream. After this time, any transfers in
   * progress will complete, but no new transfers are initiated.
   *
   * @param string $eventStreamExpirationTime
   */
  public function setEventStreamExpirationTime($eventStreamExpirationTime)
  {
    $this->eventStreamExpirationTime = $eventStreamExpirationTime;
  }
  /**
   * @return string
   */
  public function getEventStreamExpirationTime()
  {
    return $this->eventStreamExpirationTime;
  }
  /**
   * Specifies the date and time that Storage Transfer Service starts listening
   * for events from this stream. If no start time is specified or start time is
   * in the past, Storage Transfer Service starts listening immediately.
   *
   * @param string $eventStreamStartTime
   */
  public function setEventStreamStartTime($eventStreamStartTime)
  {
    $this->eventStreamStartTime = $eventStreamStartTime;
  }
  /**
   * @return string
   */
  public function getEventStreamStartTime()
  {
    return $this->eventStreamStartTime;
  }
  /**
   * Required. Specifies a unique name of the resource such as AWS SQS ARN in
   * the form 'arn:aws:sqs:region:account_id:queue_name', or Pub/Sub
   * subscription resource name in the form
   * 'projects/{project}/subscriptions/{sub}'.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EventStream::class, 'Google_Service_Storagetransfer_EventStream');
