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

namespace Google\Service\Batch;

class JobNotification extends \Google\Model
{
  protected $messageType = Message::class;
  protected $messageDataType = '';
  /**
   * The Pub/Sub topic where notifications for the job, like state changes, will
   * be published. If undefined, no Pub/Sub notifications are sent for this job.
   * Specify the topic using the following format:
   * `projects/{project}/topics/{topic}`. Notably, if you want to specify a
   * Pub/Sub topic that is in a different project than the job, your
   * administrator must grant your project's Batch service agent permission to
   * publish to that topic. For more information about configuring Pub/Sub
   * notifications for a job, see https://cloud.google.com/batch/docs/enable-
   * notifications.
   *
   * @var string
   */
  public $pubsubTopic;

  /**
   * The attribute requirements of messages to be sent to this Pub/Sub topic.
   * Without this field, no message will be sent.
   *
   * @param Message $message
   */
  public function setMessage(Message $message)
  {
    $this->message = $message;
  }
  /**
   * @return Message
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * The Pub/Sub topic where notifications for the job, like state changes, will
   * be published. If undefined, no Pub/Sub notifications are sent for this job.
   * Specify the topic using the following format:
   * `projects/{project}/topics/{topic}`. Notably, if you want to specify a
   * Pub/Sub topic that is in a different project than the job, your
   * administrator must grant your project's Batch service agent permission to
   * publish to that topic. For more information about configuring Pub/Sub
   * notifications for a job, see https://cloud.google.com/batch/docs/enable-
   * notifications.
   *
   * @param string $pubsubTopic
   */
  public function setPubsubTopic($pubsubTopic)
  {
    $this->pubsubTopic = $pubsubTopic;
  }
  /**
   * @return string
   */
  public function getPubsubTopic()
  {
    return $this->pubsubTopic;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JobNotification::class, 'Google_Service_Batch_JobNotification');
