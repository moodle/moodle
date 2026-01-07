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

class GoogleCloudContactcenterinsightsV1mainConversationTranscriptTranscriptSegment extends \Google\Collection
{
  protected $collection_key = 'words';
  /**
   * For conversations derived from multi-channel audio, this is the channel
   * number corresponding to the audio from that channel. For audioChannelCount
   * = N, its output values can range from '1' to 'N'. A channel tag of 0
   * indicates that the audio is mono.
   *
   * @var int
   */
  public $channelTag;
  /**
   * A confidence estimate between 0.0 and 1.0 of the fidelity of this segment.
   * A default value of 0.0 indicates that the value is unset.
   *
   * @var float
   */
  public $confidence;
  protected $dialogflowSegmentMetadataType = GoogleCloudContactcenterinsightsV1mainConversationTranscriptTranscriptSegmentDialogflowSegmentMetadata::class;
  protected $dialogflowSegmentMetadataDataType = '';
  /**
   * The language code of this segment as a [BCP-47](https://www.rfc-
   * editor.org/rfc/bcp/bcp47.txt) language tag. Example: "en-US".
   *
   * @var string
   */
  public $languageCode;
  /**
   * The time that the message occurred, if provided.
   *
   * @var string
   */
  public $messageTime;
  protected $segmentParticipantType = GoogleCloudContactcenterinsightsV1mainConversationParticipant::class;
  protected $segmentParticipantDataType = '';
  protected $sentimentType = GoogleCloudContactcenterinsightsV1mainSentimentData::class;
  protected $sentimentDataType = '';
  /**
   * The text of this segment.
   *
   * @var string
   */
  public $text;
  protected $wordsType = GoogleCloudContactcenterinsightsV1mainConversationTranscriptTranscriptSegmentWordInfo::class;
  protected $wordsDataType = 'array';

  /**
   * For conversations derived from multi-channel audio, this is the channel
   * number corresponding to the audio from that channel. For audioChannelCount
   * = N, its output values can range from '1' to 'N'. A channel tag of 0
   * indicates that the audio is mono.
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
   * A confidence estimate between 0.0 and 1.0 of the fidelity of this segment.
   * A default value of 0.0 indicates that the value is unset.
   *
   * @param float $confidence
   */
  public function setConfidence($confidence)
  {
    $this->confidence = $confidence;
  }
  /**
   * @return float
   */
  public function getConfidence()
  {
    return $this->confidence;
  }
  /**
   * CCAI metadata relating to the current transcript segment.
   *
   * @param GoogleCloudContactcenterinsightsV1mainConversationTranscriptTranscriptSegmentDialogflowSegmentMetadata $dialogflowSegmentMetadata
   */
  public function setDialogflowSegmentMetadata(GoogleCloudContactcenterinsightsV1mainConversationTranscriptTranscriptSegmentDialogflowSegmentMetadata $dialogflowSegmentMetadata)
  {
    $this->dialogflowSegmentMetadata = $dialogflowSegmentMetadata;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainConversationTranscriptTranscriptSegmentDialogflowSegmentMetadata
   */
  public function getDialogflowSegmentMetadata()
  {
    return $this->dialogflowSegmentMetadata;
  }
  /**
   * The language code of this segment as a [BCP-47](https://www.rfc-
   * editor.org/rfc/bcp/bcp47.txt) language tag. Example: "en-US".
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * The time that the message occurred, if provided.
   *
   * @param string $messageTime
   */
  public function setMessageTime($messageTime)
  {
    $this->messageTime = $messageTime;
  }
  /**
   * @return string
   */
  public function getMessageTime()
  {
    return $this->messageTime;
  }
  /**
   * The participant of this segment.
   *
   * @param GoogleCloudContactcenterinsightsV1mainConversationParticipant $segmentParticipant
   */
  public function setSegmentParticipant(GoogleCloudContactcenterinsightsV1mainConversationParticipant $segmentParticipant)
  {
    $this->segmentParticipant = $segmentParticipant;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainConversationParticipant
   */
  public function getSegmentParticipant()
  {
    return $this->segmentParticipant;
  }
  /**
   * The sentiment for this transcript segment.
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
   * The text of this segment.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
  /**
   * A list of the word-specific information for each word in the segment.
   *
   * @param GoogleCloudContactcenterinsightsV1mainConversationTranscriptTranscriptSegmentWordInfo[] $words
   */
  public function setWords($words)
  {
    $this->words = $words;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainConversationTranscriptTranscriptSegmentWordInfo[]
   */
  public function getWords()
  {
    return $this->words;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1mainConversationTranscriptTranscriptSegment::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainConversationTranscriptTranscriptSegment');
