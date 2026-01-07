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

namespace Google\Service\AnalyticsHub;

class PubSubTopicSource extends \Google\Collection
{
  protected $collection_key = 'dataAffinityRegions';
  /**
   * Optional. Region hint on where the data might be published. Data affinity
   * regions are modifiable. See https://cloud.google.com/about/locations for
   * full listing of possible Cloud regions.
   *
   * @var string[]
   */
  public $dataAffinityRegions;
  /**
   * Required. Resource name of the Pub/Sub topic source for this listing. e.g.
   * projects/myproject/topics/topicId
   *
   * @var string
   */
  public $topic;

  /**
   * Optional. Region hint on where the data might be published. Data affinity
   * regions are modifiable. See https://cloud.google.com/about/locations for
   * full listing of possible Cloud regions.
   *
   * @param string[] $dataAffinityRegions
   */
  public function setDataAffinityRegions($dataAffinityRegions)
  {
    $this->dataAffinityRegions = $dataAffinityRegions;
  }
  /**
   * @return string[]
   */
  public function getDataAffinityRegions()
  {
    return $this->dataAffinityRegions;
  }
  /**
   * Required. Resource name of the Pub/Sub topic source for this listing. e.g.
   * projects/myproject/topics/topicId
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
class_alias(PubSubTopicSource::class, 'Google_Service_AnalyticsHub_PubSubTopicSource');
