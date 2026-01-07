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

class GoogleCloudContactcenterinsightsV1mainEntityMentionData extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const TYPE_MENTION_TYPE_UNSPECIFIED = 'MENTION_TYPE_UNSPECIFIED';
  /**
   * Proper noun.
   */
  public const TYPE_PROPER = 'PROPER';
  /**
   * Common noun (or noun compound).
   */
  public const TYPE_COMMON = 'COMMON';
  /**
   * The key of this entity in conversation entities. Can be used to retrieve
   * the exact `Entity` this mention is attached to.
   *
   * @var string
   */
  public $entityUniqueId;
  protected $sentimentType = GoogleCloudContactcenterinsightsV1mainSentimentData::class;
  protected $sentimentDataType = '';
  /**
   * The type of the entity mention.
   *
   * @var string
   */
  public $type;

  /**
   * The key of this entity in conversation entities. Can be used to retrieve
   * the exact `Entity` this mention is attached to.
   *
   * @param string $entityUniqueId
   */
  public function setEntityUniqueId($entityUniqueId)
  {
    $this->entityUniqueId = $entityUniqueId;
  }
  /**
   * @return string
   */
  public function getEntityUniqueId()
  {
    return $this->entityUniqueId;
  }
  /**
   * Sentiment expressed for this mention of the entity.
   *
   * @param GoogleCloudContactcenterinsightsV1mainSentimentData $sentiment
   */
  public function setSentiment(GoogleCloudContactcenterinsightsV1mainSentimentData $sentiment)
  {
    $this->sentiment = $sentiment;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainSentimentData
   */
  public function getSentiment()
  {
    return $this->sentiment;
  }
  /**
   * The type of the entity mention.
   *
   * Accepted values: MENTION_TYPE_UNSPECIFIED, PROPER, COMMON
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1mainEntityMentionData::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainEntityMentionData');
