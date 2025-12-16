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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1mainConversationLevelSentiment extends \Google\Model
{
  /**
   * The channel of the audio that the data applies to.
   *
   * @var int
   */
  public $channelTag;
  protected $sentimentDataType = GoogleCloudContactcenterinsightsV1mainSentimentData::class;
  protected $sentimentDataDataType = '';

  /**
   * The channel of the audio that the data applies to.
   *
   * @param int $channelTag
   */
  public function setChannelTag($channelTag)
  {
    $this->channelTag = $channelTag;
  }
  /**
   * @return int
   */
  public function getChannelTag()
  {
    return $this->channelTag;
  }
  /**
   * Data specifying sentiment.
   *
   * @param GoogleCloudContactcenterinsightsV1mainSentimentData $sentimentData
   */
  public function setSentimentData(GoogleCloudContactcenterinsightsV1mainSentimentData $sentimentData)
  {
    $this->sentimentData = $sentimentData;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainSentimentData
   */
  public function getSentimentData()
  {
    return $this->sentimentData;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1mainConversationLevelSentiment::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainConversationLevelSentiment');
