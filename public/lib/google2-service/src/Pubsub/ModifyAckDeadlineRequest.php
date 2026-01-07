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

namespace Google\Service\Pubsub;

class ModifyAckDeadlineRequest extends \Google\Collection
{
  protected $collection_key = 'ackIds';
  /**
   * Required. The new ack deadline with respect to the time this request was
   * sent to the Pub/Sub system. For example, if the value is 10, the new ack
   * deadline will expire 10 seconds after the `ModifyAckDeadline` call was
   * made. Specifying zero might immediately make the message available for
   * delivery to another subscriber client. This typically results in an
   * increase in the rate of message redeliveries (that is, duplicates). The
   * minimum deadline you can specify is 0 seconds. The maximum deadline you can
   * specify in a single request is 600 seconds (10 minutes).
   *
   * @var int
   */
  public $ackDeadlineSeconds;
  /**
   * Required. List of acknowledgment IDs.
   *
   * @var string[]
   */
  public $ackIds;

  /**
   * Required. The new ack deadline with respect to the time this request was
   * sent to the Pub/Sub system. For example, if the value is 10, the new ack
   * deadline will expire 10 seconds after the `ModifyAckDeadline` call was
   * made. Specifying zero might immediately make the message available for
   * delivery to another subscriber client. This typically results in an
   * increase in the rate of message redeliveries (that is, duplicates). The
   * minimum deadline you can specify is 0 seconds. The maximum deadline you can
   * specify in a single request is 600 seconds (10 minutes).
   *
   * @param int $ackDeadlineSeconds
   */
  public function setAckDeadlineSeconds($ackDeadlineSeconds)
  {
    $this->ackDeadlineSeconds = $ackDeadlineSeconds;
  }
  /**
   * @return int
   */
  public function getAckDeadlineSeconds()
  {
    return $this->ackDeadlineSeconds;
  }
  /**
   * Required. List of acknowledgment IDs.
   *
   * @param string[] $ackIds
   */
  public function setAckIds($ackIds)
  {
    $this->ackIds = $ackIds;
  }
  /**
   * @return string[]
   */
  public function getAckIds()
  {
    return $this->ackIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ModifyAckDeadlineRequest::class, 'Google_Service_Pubsub_ModifyAckDeadlineRequest');
