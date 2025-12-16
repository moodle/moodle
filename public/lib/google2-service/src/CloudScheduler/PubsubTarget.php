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

namespace Google\Service\CloudScheduler;

class PubsubTarget extends \Google\Model
{
  /**
   * Attributes for PubsubMessage. Pubsub message must contain either non-empty
   * data, or at least one attribute.
   *
   * @var string[]
   */
  public $attributes;
  /**
   * The message payload for PubsubMessage. Pubsub message must contain either
   * non-empty data, or at least one attribute.
   *
   * @var string
   */
  public $data;
  /**
   * Required. The name of the Cloud Pub/Sub topic to which messages will be
   * published when a job is delivered. The topic name must be in the same
   * format as required by Pub/Sub's [PublishRequest.name](https://cloud.google.
   * com/pubsub/docs/reference/rpc/google.pubsub.v1#publishrequest), for example
   * `projects/PROJECT_ID/topics/TOPIC_ID`. The topic must be in the same
   * project as the Cloud Scheduler job.
   *
   * @var string
   */
  public $topicName;

  /**
   * Attributes for PubsubMessage. Pubsub message must contain either non-empty
   * data, or at least one attribute.
   *
   * @param string[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return string[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * The message payload for PubsubMessage. Pubsub message must contain either
   * non-empty data, or at least one attribute.
   *
   * @param string $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return string
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * Required. The name of the Cloud Pub/Sub topic to which messages will be
   * published when a job is delivered. The topic name must be in the same
   * format as required by Pub/Sub's [PublishRequest.name](https://cloud.google.
   * com/pubsub/docs/reference/rpc/google.pubsub.v1#publishrequest), for example
   * `projects/PROJECT_ID/topics/TOPIC_ID`. The topic must be in the same
   * project as the Cloud Scheduler job.
   *
   * @param string $topicName
   */
  public function setTopicName($topicName)
  {
    $this->topicName = $topicName;
  }
  /**
   * @return string
   */
  public function getTopicName()
  {
    return $this->topicName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PubsubTarget::class, 'Google_Service_CloudScheduler_PubsubTarget');
