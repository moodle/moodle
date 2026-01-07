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

namespace Google\Service\Container;

class PubSub extends \Google\Model
{
  /**
   * Enable notifications for Pub/Sub.
   *
   * @var bool
   */
  public $enabled;
  protected $filterType = Filter::class;
  protected $filterDataType = '';
  /**
   * The desired Pub/Sub topic to which notifications will be sent by GKE.
   * Format is `projects/{project}/topics/{topic}`.
   *
   * @var string
   */
  public $topic;

  /**
   * Enable notifications for Pub/Sub.
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
   * Allows filtering to one or more specific event types. If no filter is
   * specified, or if a filter is specified with no event types, all event types
   * will be sent
   *
   * @param Filter $filter
   */
  public function setFilter(Filter $filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return Filter
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * The desired Pub/Sub topic to which notifications will be sent by GKE.
   * Format is `projects/{project}/topics/{topic}`.
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
class_alias(PubSub::class, 'Google_Service_Container_PubSub');
