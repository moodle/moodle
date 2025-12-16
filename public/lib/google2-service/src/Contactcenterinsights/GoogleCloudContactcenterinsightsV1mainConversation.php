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

class GoogleCloudContactcenterinsightsV1mainConversation extends \Google\Collection
{
  /**
   * Default value, if unspecified will default to PHONE_CALL.
   */
  public const MEDIUM_MEDIUM_UNSPECIFIED = 'MEDIUM_UNSPECIFIED';
  /**
   * The format for conversations that took place over the phone.
   */
  public const MEDIUM_PHONE_CALL = 'PHONE_CALL';
  /**
   * The format for conversations that took place over chat.
   */
  public const MEDIUM_CHAT = 'CHAT';
  protected $collection_key = 'runtimeAnnotations';
  /**
   * An opaque, user-specified string representing the human agent who handled
   * the conversation.
   *
   * @var string
   */
  public $agentId;
  protected $callMetadataType = GoogleCloudContactcenterinsightsV1mainConversationCallMetadata::class;
  protected $callMetadataDataType = '';
  /**
   * Output only. The time at which the conversation was created.
   *
   * @var string
   */
  public $createTime;
  protected $dataSourceType = GoogleCloudContactcenterinsightsV1mainConversationDataSource::class;
  protected $dataSourceDataType = '';
  protected $dialogflowIntentsType = GoogleCloudContactcenterinsightsV1mainDialogflowIntent::class;
  protected $dialogflowIntentsDataType = 'map';
  /**
   * Output only. The duration of the conversation.
   *
   * @var string
   */
  public $duration;
  /**
   * The time at which this conversation should expire. After this time, the
   * conversation data and any associated analyses will be deleted.
   *
   * @var string
   */
  public $expireTime;
  /**
   * A map for the user to specify any custom fields. A maximum of 100 labels
   * per conversation is allowed, with a maximum of 256 characters per entry.
   *
   * @var string[]
   */
  public $labels;
  /**
   * A user-specified language code for the conversation.
   *
   * @var string
   */
  public $languageCode;
  protected $latestAnalysisType = GoogleCloudContactcenterinsightsV1mainAnalysis::class;
  protected $latestAnalysisDataType = '';
  protected $latestSummaryType = GoogleCloudContactcenterinsightsV1mainConversationSummarizationSuggestionData::class;
  protected $latestSummaryDataType = '';
  /**
   * Immutable. The conversation medium, if unspecified will default to
   * PHONE_CALL.
   *
   * @var string
   */
  public $medium;
  /**
   * Input only. JSON metadata encoded as a string. This field is primarily used
   * by Insights integrations with various telephony systems and must be in one
   * of Insight's supported formats.
   *
   * @var string
   */
  public $metadataJson;
  /**
   * Immutable. The resource name of the conversation. Format:
   * projects/{project}/locations/{location}/conversations/{conversation}
   *
   * @var string
   */
  public $name;
  /**
   * Obfuscated user ID which the customer sent to us.
   *
   * @var string
   */
  public $obfuscatedUserId;
  protected $qualityMetadataType = GoogleCloudContactcenterinsightsV1mainConversationQualityMetadata::class;
  protected $qualityMetadataDataType = '';
  protected $runtimeAnnotationsType = GoogleCloudContactcenterinsightsV1mainRuntimeAnnotation::class;
  protected $runtimeAnnotationsDataType = 'array';
  /**
   * The time at which the conversation started.
   *
   * @var string
   */
  public $startTime;
  protected $transcriptType = GoogleCloudContactcenterinsightsV1mainConversationTranscript::class;
  protected $transcriptDataType = '';
  /**
   * Input only. The TTL for this resource. If specified, then this TTL will be
   * used to calculate the expire time.
   *
   * @var string
   */
  public $ttl;
  /**
   * Output only. The number of turns in the conversation.
   *
   * @var int
   */
  public $turnCount;
  /**
   * Output only. The most recent time at which the conversation was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * An opaque, user-specified string representing the human agent who handled
   * the conversation.
   *
   * @param string $agentId
   */
  public function setAgentId($agentId)
  {
    $this->agentId = $agentId;
  }
  /**
   * @return string
   */
  public function getAgentId()
  {
    return $this->agentId;
  }
  /**
   * Call-specific metadata.
   *
   * @param GoogleCloudContactcenterinsightsV1mainConversationCallMetadata $callMetadata
   */
  public function setCallMetadata(GoogleCloudContactcenterinsightsV1mainConversationCallMetadata $callMetadata)
  {
    $this->callMetadata = $callMetadata;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainConversationCallMetadata
   */
  public function getCallMetadata()
  {
    return $this->callMetadata;
  }
  /**
   * Output only. The time at which the conversation was created.
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
   * The source of the audio and transcription for the conversation.
   *
   * @param GoogleCloudContactcenterinsightsV1mainConversationDataSource $dataSource
   */
  public function setDataSource(GoogleCloudContactcenterinsightsV1mainConversationDataSource $dataSource)
  {
    $this->dataSource = $dataSource;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainConversationDataSource
   */
  public function getDataSource()
  {
    return $this->dataSource;
  }
  /**
   * Output only. All the matched Dialogflow intents in the call. The key
   * corresponds to a Dialogflow intent, format:
   * projects/{project}/agent/{agent}/intents/{intent}
   *
   * @param GoogleCloudContactcenterinsightsV1mainDialogflowIntent[] $dialogflowIntents
   */
  public function setDialogflowIntents($dialogflowIntents)
  {
    $this->dialogflowIntents = $dialogflowIntents;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainDialogflowIntent[]
   */
  public function getDialogflowIntents()
  {
    return $this->dialogflowIntents;
  }
  /**
   * Output only. The duration of the conversation.
   *
   * @param string $duration
   */
  public function setDuration($duration)
  {
    $this->duration = $duration;
  }
  /**
   * @return string
   */
  public function getDuration()
  {
    return $this->duration;
  }
  /**
   * The time at which this conversation should expire. After this time, the
   * conversation data and any associated analyses will be deleted.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * A map for the user to specify any custom fields. A maximum of 100 labels
   * per conversation is allowed, with a maximum of 256 characters per entry.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * A user-specified language code for the conversation.
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
   * Output only. The conversation's latest analysis, if one exists.
   *
   * @param GoogleCloudContactcenterinsightsV1mainAnalysis $latestAnalysis
   */
  public function setLatestAnalysis(GoogleCloudContactcenterinsightsV1mainAnalysis $latestAnalysis)
  {
    $this->latestAnalysis = $latestAnalysis;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainAnalysis
   */
  public function getLatestAnalysis()
  {
    return $this->latestAnalysis;
  }
  /**
   * Output only. Latest summary of the conversation.
   *
   * @param GoogleCloudContactcenterinsightsV1mainConversationSummarizationSuggestionData $latestSummary
   */
  public function setLatestSummary(GoogleCloudContactcenterinsightsV1mainConversationSummarizationSuggestionData $latestSummary)
  {
    $this->latestSummary = $latestSummary;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainConversationSummarizationSuggestionData
   */
  public function getLatestSummary()
  {
    return $this->latestSummary;
  }
  /**
   * Immutable. The conversation medium, if unspecified will default to
   * PHONE_CALL.
   *
   * Accepted values: MEDIUM_UNSPECIFIED, PHONE_CALL, CHAT
   *
   * @param self::MEDIUM_* $medium
   */
  public function setMedium($medium)
  {
    $this->medium = $medium;
  }
  /**
   * @return self::MEDIUM_*
   */
  public function getMedium()
  {
    return $this->medium;
  }
  /**
   * Input only. JSON metadata encoded as a string. This field is primarily used
   * by Insights integrations with various telephony systems and must be in one
   * of Insight's supported formats.
   *
   * @param string $metadataJson
   */
  public function setMetadataJson($metadataJson)
  {
    $this->metadataJson = $metadataJson;
  }
  /**
   * @return string
   */
  public function getMetadataJson()
  {
    return $this->metadataJson;
  }
  /**
   * Immutable. The resource name of the conversation. Format:
   * projects/{project}/locations/{location}/conversations/{conversation}
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
   * Obfuscated user ID which the customer sent to us.
   *
   * @param string $obfuscatedUserId
   */
  public function setObfuscatedUserId($obfuscatedUserId)
  {
    $this->obfuscatedUserId = $obfuscatedUserId;
  }
  /**
   * @return string
   */
  public function getObfuscatedUserId()
  {
    return $this->obfuscatedUserId;
  }
  /**
   * Conversation metadata related to quality management.
   *
   * @param GoogleCloudContactcenterinsightsV1mainConversationQualityMetadata $qualityMetadata
   */
  public function setQualityMetadata(GoogleCloudContactcenterinsightsV1mainConversationQualityMetadata $qualityMetadata)
  {
    $this->qualityMetadata = $qualityMetadata;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainConversationQualityMetadata
   */
  public function getQualityMetadata()
  {
    return $this->qualityMetadata;
  }
  /**
   * Output only. The annotations that were generated during the customer and
   * agent interaction.
   *
   * @param GoogleCloudContactcenterinsightsV1mainRuntimeAnnotation[] $runtimeAnnotations
   */
  public function setRuntimeAnnotations($runtimeAnnotations)
  {
    $this->runtimeAnnotations = $runtimeAnnotations;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainRuntimeAnnotation[]
   */
  public function getRuntimeAnnotations()
  {
    return $this->runtimeAnnotations;
  }
  /**
   * The time at which the conversation started.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Output only. The conversation transcript.
   *
   * @param GoogleCloudContactcenterinsightsV1mainConversationTranscript $transcript
   */
  public function setTranscript(GoogleCloudContactcenterinsightsV1mainConversationTranscript $transcript)
  {
    $this->transcript = $transcript;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainConversationTranscript
   */
  public function getTranscript()
  {
    return $this->transcript;
  }
  /**
   * Input only. The TTL for this resource. If specified, then this TTL will be
   * used to calculate the expire time.
   *
   * @param string $ttl
   */
  public function setTtl($ttl)
  {
    $this->ttl = $ttl;
  }
  /**
   * @return string
   */
  public function getTtl()
  {
    return $this->ttl;
  }
  /**
   * Output only. The number of turns in the conversation.
   *
   * @param int $turnCount
   */
  public function setTurnCount($turnCount)
  {
    $this->turnCount = $turnCount;
  }
  /**
   * @return int
   */
  public function getTurnCount()
  {
    return $this->turnCount;
  }
  /**
   * Output only. The most recent time at which the conversation was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1mainConversation::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainConversation');
