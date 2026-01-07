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

class WatchTarget extends \Google\Model
{
  protected $topicType = CloudPubsubTopic::class;
  protected $topicDataType = '';

  /**
   * A Pub/Sub topic. To receive notifications, the topic must grant publish
   * privileges to the Forms service account `serviceAccount:forms-
   * notifications@system.gserviceaccount.com`. Only the project that owns a
   * topic may create a watch with it. Pub/Sub delivery guarantees should be
   * considered.
   *
   * @param CloudPubsubTopic $topic
   */
  public function setTopic(CloudPubsubTopic $topic)
  {
    $this->topic = $topic;
  }
  /**
   * @return CloudPubsubTopic
   */
  public function getTopic()
  {
    return $this->topic;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WatchTarget::class, 'Google_Service_Forms_WatchTarget');
