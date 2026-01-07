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

class GoogleCloudContactcenterinsightsV1CallAnnotation extends \Google\Model
{
  protected $annotationEndBoundaryType = GoogleCloudContactcenterinsightsV1AnnotationBoundary::class;
  protected $annotationEndBoundaryDataType = '';
  protected $annotationStartBoundaryType = GoogleCloudContactcenterinsightsV1AnnotationBoundary::class;
  protected $annotationStartBoundaryDataType = '';
  /**
   * The channel of the audio where the annotation occurs. For single-channel
   * audio, this field is not populated.
   *
   * @var int
   */
  public $channelTag;
  protected $entityMentionDataType = GoogleCloudContactcenterinsightsV1EntityMentionData::class;
  protected $entityMentionDataDataType = '';
  protected $holdDataType = GoogleCloudContactcenterinsightsV1HoldData::class;
  protected $holdDataDataType = '';
  protected $intentMatchDataType = GoogleCloudContactcenterinsightsV1IntentMatchData::class;
  protected $intentMatchDataDataType = '';
  protected $interruptionDataType = GoogleCloudContactcenterinsightsV1InterruptionData::class;
  protected $interruptionDataDataType = '';
  protected $issueMatchDataType = GoogleCloudContactcenterinsightsV1IssueMatchData::class;
  protected $issueMatchDataDataType = '';
  protected $phraseMatchDataType = GoogleCloudContactcenterinsightsV1PhraseMatchData::class;
  protected $phraseMatchDataDataType = '';
  protected $sentimentDataType = GoogleCloudContactcenterinsightsV1SentimentData::class;
  protected $sentimentDataDataType = '';
  protected $silenceDataType = GoogleCloudContactcenterinsightsV1SilenceData::class;
  protected $silenceDataDataType = '';

  /**
   * The boundary in the conversation where the annotation ends, inclusive.
   *
   * @param GoogleCloudContactcenterinsightsV1AnnotationBoundary $annotationEndBoundary
   */
  public function setAnnotationEndBoundary(GoogleCloudContactcenterinsightsV1AnnotationBoundary $annotationEndBoundary)
  {
    $this->annotationEndBoundary = $annotationEndBoundary;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1AnnotationBoundary
   */
  public function getAnnotationEndBoundary()
  {
    return $this->annotationEndBoundary;
  }
  /**
   * The boundary in the conversation where the annotation starts, inclusive.
   *
   * @param GoogleCloudContactcenterinsightsV1AnnotationBoundary $annotationStartBoundary
   */
  public function setAnnotationStartBoundary(GoogleCloudContactcenterinsightsV1AnnotationBoundary $annotationStartBoundary)
  {
    $this->annotationStartBoundary = $annotationStartBoundary;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1AnnotationBoundary
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
   * @param GoogleCloudContactcenterinsightsV1EntityMentionData $entityMentionData
   */
  public function setEntityMentionData(GoogleCloudContactcenterinsightsV1EntityMentionData $entityMentionData)
  {
    $this->entityMentionData = $entityMentionData;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1EntityMentionData
   */
  public function getEntityMentionData()
  {
    return $this->entityMentionData;
  }
  /**
   * Data specifying a hold.
   *
   * @param GoogleCloudContactcenterinsightsV1HoldData $holdData
   */
  public function setHoldData(GoogleCloudContactcenterinsightsV1HoldData $holdData)
  {
    $this->holdData = $holdData;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1HoldData
   */
  public function getHoldData()
  {
    return $this->holdData;
  }
  /**
   * Data specifying an intent match.
   *
   * @param GoogleCloudContactcenterinsightsV1IntentMatchData $intentMatchData
   */
  public function setIntentMatchData(GoogleCloudContactcenterinsightsV1IntentMatchData $intentMatchData)
  {
    $this->intentMatchData = $intentMatchData;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1IntentMatchData
   */
  public function getIntentMatchData()
  {
    return $this->intentMatchData;
  }
  /**
   * Data specifying an interruption.
   *
   * @param GoogleCloudContactcenterinsightsV1InterruptionData $interruptionData
   */
  public function setInterruptionData(GoogleCloudContactcenterinsightsV1InterruptionData $interruptionData)
  {
    $this->interruptionData = $interruptionData;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1InterruptionData
   */
  public function getInterruptionData()
  {
    return $this->interruptionData;
  }
  /**
   * Data specifying an issue match.
   *
   * @param GoogleCloudContactcenterinsightsV1IssueMatchData $issueMatchData
   */
  public function setIssueMatchData(GoogleCloudContactcenterinsightsV1IssueMatchData $issueMatchData)
  {
    $this->issueMatchData = $issueMatchData;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1IssueMatchData
   */
  public function getIssueMatchData()
  {
    return $this->issueMatchData;
  }
  /**
   * Data specifying a phrase match.
   *
   * @param GoogleCloudContactcenterinsightsV1PhraseMatchData $phraseMatchData
   */
  public function setPhraseMatchData(GoogleCloudContactcenterinsightsV1PhraseMatchData $phraseMatchData)
  {
    $this->phraseMatchData = $phraseMatchData;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1PhraseMatchData
   */
  public function getPhraseMatchData()
  {
    return $this->phraseMatchData;
  }
  /**
   * Data specifying sentiment.
   *
   * @param GoogleCloudContactcenterinsightsV1SentimentData $sentimentData
   */
  public function setSentimentData(GoogleCloudContactcenterinsightsV1SentimentData $sentimentData)
  {
    $this->sentimentData = $sentimentData;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1SentimentData
   */
  public function getSentimentData()
  {
    return $this->sentimentData;
  }
  /**
   * Data specifying silence.
   *
   * @param GoogleCloudContactcenterinsightsV1SilenceData $silenceData
   */
  public function setSilenceData(GoogleCloudContactcenterinsightsV1SilenceData $silenceData)
  {
    $this->silenceData = $silenceData;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1SilenceData
   */
  public function getSilenceData()
  {
    return $this->silenceData;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1CallAnnotation::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1CallAnnotation');
