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

namespace Google\Service\DataFusion;

class EventPublishConfig extends \Google\Model
{
  /**
   * Required. Option to enable Event Publishing.
   *
   * @var bool
   */
  public $enabled;
  /**
   * Required. The resource name of the Pub/Sub topic. Format:
   * projects/{project_id}/topics/{topic_id}
   *
   * @var string
   */
  public $topic;

  /**
   * Required. Option to enable Event Publishing.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * Required. The resource name of the Pub/Sub topic. Format:
   * projects/{project_id}/topics/{topic_id}
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
class_alias(EventPublishConfig::class, 'Google_Service_DataFusion_EventPublishConfig');
