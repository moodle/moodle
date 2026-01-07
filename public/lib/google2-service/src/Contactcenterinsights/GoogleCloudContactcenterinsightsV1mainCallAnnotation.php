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

class GoogleCloudContactcenterinsightsV1mainCallAnnotation extends \Google\Model
{
  protected $annotationEndBoundaryType = GoogleCloudContactcenterinsightsV1mainAnnotationBoundary::class;
  protected $annotationEndBoundaryDataType = '';
  protected $annotationStartBoundaryType = GoogleCloudContactcenterinsightsV1mainAnnotationBoundary::class;
  protected $annotationStartBoundaryDataType = '';
  /**
   * The channel of the audio where the annotation occurs. For single-channel
   * audio, this field is not populated.
   *
   * @var int
   */
  public $channelTag;
  protected $entityMentionDataType = GoogleCloudContactcenterinsightsV1mainEntityMentionData::class;
  protected $entityMentionDataDataType = '';
  protected $holdDataType = GoogleCloudContactcenterinsightsV1mainHoldData::class;
  protected $holdDataDataType = '';
  protected $intentMatchDataType = GoogleCloudContactcenterinsightsV1mainIntentMatchData::class;
  protected $intentMatchDataDataType = '';
  protected $interruptionDataType = GoogleCloudContactcenterinsightsV1mainInterruptionData::class;
  protected $interruptionDataDataType = '';
  protected $issueMatchDataType = GoogleCloudContactcenterinsightsV1mainIssueMatchData::class;
  protected $issueMatchDataDataType = '';
  protected $phraseMatchDataType = GoogleCloudContactcenterinsightsV1mainPhraseMatchData::class;
  protected $phraseMatchDataDataType = '';
  protected $sentimentDataType = GoogleCloudContactcenterinsightsV1mainSentimentData::class;
  protected $sentimentDataDataType = '';
  protected $silenceDataType = GoogleCloudContactcenterinsightsV1mainSilenceData::class;
  protected $silenceDataDataType = '';

  /**
   * The boundary in the conversation where the annotation ends, inclusive.
   *
   * @param GoogleCloudContactcenterinsightsV1mainAnnotationBoundary $annotationEndBoundary
   */
  public function setAnnotationEndBoundary(GoogleCloudContactcenterinsightsV1mainAnnotationBoundary $annotationEndBoundary)
  {
    $this->annotationEndBoundary = $annotationEndBoundary;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainAnnotationBoundary
   */
  public function getAnnotationEndBoundary()
  {
    return $this->annotationEndBoundary;
  }
  /**
   * The boundary in the conversation where the annotation starts, inclusive.
   *
   * @param GoogleCloudContactcenterinsightsV1mainAnnotationBoundary $annotationStartBoundary
   */
  public function setAnnotationStartBoundary(GoogleCloudContactcenterinsightsV1mainAnnotationBoundary $annotationStartBoundary)
  {
    $this->annotationStartBoundary = $annotationStartBoundary;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainAnnotationBoundary
   */
  public function getAnnotationStartBoundary()
  {
    return $this->annotationStartBoundary;
  }
  /**
   * The channel of the audio where the annotation occurs. For single-channel
   * audio, this field is not populated.
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
   * Data specifying an entity mention.
   *
   * @param GoogleCloudContactcenterinsightsV1mainEntityMentionData $entityMentionData
   */
  public function setEntityMentionData(GoogleCloudContactcenterinsightsV1mainEntityMentionData $entityMentionData)
  {
    $this->entityMentionData = $entityMentionData;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainEntityMentionData
   */
  public function getEntityMentionData()
  {
    return $this->entityMentionData;
  }
  /**
   * Data specifying a hold.
   *
   * @param GoogleCloudContactcenterinsightsV1mainHoldData $holdData
   */
  public function setHoldData(GoogleCloudContactcenterinsightsV1mainHoldData $holdData)
  {
    $this->holdData = $holdData;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainHoldData
   */
  public function getHoldData()
  {
    return $this->holdData;
  }
  /**
   * Data specifying an intent match.
   *
   * @param GoogleCloudContactcenterinsightsV1mainIntentMatchData $intentMatchData
   */
  public function setIntentMatchData(GoogleCloudContactcenterinsightsV1mainIntentMatchData $intentMatchData)
  {
    $this->intentMatchData = $intentMatchData;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainIntentMatchData
   */
  public function getIntentMatchData()
  {
    return $this->intentMatchData;
  }
  /**
   * Data specifying an interruption.
   *
   * @param GoogleCloudContactcenterinsightsV1mainInterruptionData $interruptionData
   */
  public function setInterruptionData(GoogleCloudContactcenterinsightsV1mainInterruptionData $interruptionData)
  {
    $this->interruptionData = $interruptionData;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainInterruptionData
   */
  public function getInterruptionData()
  {
    return $this->interruptionData;
  }
  /**
   * Data specifying an issue match.
   *
   * @param GoogleCloudContactcenterinsightsV1mainIssueMatchData $issueMatchData
   */
  public function setIssueMatchData(GoogleCloudContactcenterinsightsV1mainIssueMatchData $issueMatchData)
  {
    $this->issueMatchData = $issueMatchData;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainIssueMatchData
   */
  public function getIssueMatchData()
  {
    return $this->issueMatchData;
  }
  /**
   * Data specifying a phrase match.
   *
   * @param GoogleCloudContactcenterinsightsV1mainPhraseMatchData $phraseMatchData
   */
  public function setPhraseMatchData(GoogleCloudContactcenterinsightsV1mainPhraseMatchData $phraseMatchData)
  {
    $this->phraseMatchData = $phraseMatchData;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainPhraseMatchData
   */
  public function getPhraseMatchData()
  {
    return $this->phraseMatchData;
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
  /**
   * Data specifying silence.
   *
   * @param GoogleCloudContactcenterinsightsV1mainSilenceData $silenceData
   */
  public function setSilenceData(GoogleCloudContactcenterinsightsV1mainSilenceData $silenceData)
  {
    $this->silenceData = $silenceData;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainSilenceData
   */
  public function getSilenceData()
  {
    return $this->silenceData;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1mainCallAnnotation::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainCallAnnotation');
