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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowV2Message extends \Google\Model
{
  /**
   * Participant role not set.
   */
  public const PARTICIPANT_ROLE_ROLE_UNSPECIFIED = 'ROLE_UNSPECIFIED';
  /**
   * Participant is a human agent.
   */
  public const PARTICIPANT_ROLE_HUMAN_AGENT = 'HUMAN_AGENT';
  /**
   * Participant is an automated agent, such as a Dialogflow agent.
   */
  public const PARTICIPANT_ROLE_AUTOMATED_AGENT = 'AUTOMATED_AGENT';
  /**
   * Participant is an end user that has called or chatted with Dialogflow
   * services.
   */
  public const PARTICIPANT_ROLE_END_USER = 'END_USER';
  /**
   * Required. The message content.
   *
   * @var string
   */
  public $content;
  /**
   * Output only. The time when the message was created in Contact Center AI.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The message language. This should be a [BCP-47](https://www.rfc-
   * editor.org/rfc/bcp/bcp47.txt) language tag. Example: "en-US".
   *
   * @var string
   */
  public $languageCode;
  protected $messageAnnotationType = GoogleCloudDialogflowV2MessageAnnotation::class;
  protected $messageAnnotationDataType = '';
  /**
   * Optional. The unique identifier of the message. Format:
   * `projects//locations//conversations//messages/`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The participant that sends this message.
   *
   * @var string
   */
  public $participant;
  /**
   * Output only. The role of the participant.
   *
   * @var string
   */
  public $participantRole;
  /**
   * Optional. The time when the message was sent. For voice messages, this is
   * the time when an utterance started.
   *
   * @var string
   */
  public $sendTime;
  protected $sentimentAnalysisType = GoogleCloudDialogflowV2SentimentAnalysisResult::class;
  protected $sentimentAnalysisDataType = '';

  /**
   * Required. The message content.
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * Output only. The time when the message was created in Contact Center AI.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. The message language. This should be a [BCP-47](https://www.rfc-
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
   * Output only. The annotation for the message.
   *
   * @param GoogleCloudDialogflowV2MessageAnnotation $messageAnnotation
   */
  public function setMessageAnnotation(GoogleCloudDialogflowV2MessageAnnotation $messageAnnotation)
  {
    $this->messageAnnotation = $messageAnnotation;
  }
  /**
   * @return GoogleCloudDialogflowV2MessageAnnotation
   */
  public function getMessageAnnotation()
  {
    return $this->messageAnnotation;
  }
  /**
   * Optional. The unique identifier of the message. Format:
   * `projects//locations//conversations//messages/`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The participant that sends this message.
   *
   * @param string $participant
   */
  public function setParticipant($participant)
  {
    $this->participant = $participant;
  }
  /**
   * @return string
   */
  public function getParticipant()
  {
    return $this->participant;
  }
  /**
   * Output only. The role of the participant.
   *
   * Accepted values: ROLE_UNSPECIFIED, HUMAN_AGENT, AUTOMATED_AGENT, END_USER
   *
   * @param self::PARTICIPANT_ROLE_* $participantRole
   */
  public function setParticipantRole($participantRole)
  {
    $this->participantRole = $participantRole;
  }
  /**
   * @return self::PARTICIPANT_ROLE_*
   */
  public function getParticipantRole()
  {
    return $this->participantRole;
  }
  /**
   * Optional. The time when the message was sent. For voice messages, this is
   * the time when an utterance started.
   *
   * @param string $sendTime
   */
  public function setSendTime($sendTime)
  {
    $this->sendTime = $sendTime;
  }
  /**
   * @return string
   */
  public function getSendTime()
  {
    return $this->sendTime;
  }
  /**
   * Output only. The sentiment analysis result for the message.
   *
   * @param GoogleCloudDialogflowV2SentimentAnalysisResult $sentimentAnalysis
   */
  public function setSentimentAnalysis(GoogleCloudDialogflowV2SentimentAnalysisResult $sentimentAnalysis)
  {
    $this->sentimentAnalysis = $sentimentAnalysis;
  }
  /**
   * @return GoogleCloudDialogflowV2SentimentAnalysisResult
   */
  public function getSentimentAnalysis()
  {
    return $this->sentimentAnalysis;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2Message::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2Message');
