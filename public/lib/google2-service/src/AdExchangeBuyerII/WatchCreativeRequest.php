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

namespace Google\Service\AdExchangeBuyerII;

class WatchCreativeRequest extends \Google\Model
{
  /**
   * The Pub/Sub topic to publish notifications to. This topic must already
   * exist and must give permission to ad-exchange-buyside-reports@google.com to
   * write to the topic. This should be the full resource name in
   * "projects/{project_id}/topics/{topic_id}" format.
   *
   * @var string
   */
  public $topic;

  /**
   * The Pub/Sub topic to publish notifications to. This topic must already
   * exist and must give permission to ad-exchange-buyside-reports@google.com to
   * write to the topic. This should be the full resource name in
   * "projects/{project_id}/topics/{topic_id}" format.
   *
   * @param string $topic
   */
  public function setTopic($topic)
  {
    $this->topic = $topic;
  }
  /**
   * @return string
   */
  public function getTopic()
  {
    return $this->topic;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WatchCreativeRequest::class, 'Google_Service_AdExchangeBuyerII_WatchCreativeRequest');
