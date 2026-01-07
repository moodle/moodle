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

class GoogleCloudContactcenterinsightsV1Settings extends \Google\Model
{
  protected $analysisConfigType = GoogleCloudContactcenterinsightsV1SettingsAnalysisConfig::class;
  protected $analysisConfigDataType = '';
  /**
   * The default TTL for newly-created conversations. If a conversation has a
   * specified expiration, that value will be used instead. Changing this value
   * will not change the expiration of existing conversations. Conversations
   * with no expire time persist until they are deleted.
   *
   * @var string
   */
  public $conversationTtl;
  /**
   * Output only. The time at which the settings was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * A language code to be applied to each transcript segment unless the segment
   * already specifies a language code. Language code defaults to "en-US" if it
   * is neither specified on the segment nor here.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Immutable. The resource name of the settings resource. Format:
   * projects/{project}/locations/{location}/settings
   *
   * @var string
   */
  public $name;
  /**
   * A map that maps a notification trigger to a Pub/Sub topic. Each time a
   * specified trigger occurs, Insights will notify the corresponding Pub/Sub
   * topic. Keys are notification triggers. Supported keys are: * "all-
   * triggers": Notify each time any of the supported triggers occurs. *
   * "create-analysis": Notify each time an analysis is created. * "create-
   * conversation": Notify each time a conversation is created. * "export-
   * insights-data": Notify each time an export is complete. * "ingest-
   * conversations": Notify each time an IngestConversations LRO is complete. *
   * "update-conversation": Notify each time a conversation is updated via
   * UpdateConversation. * "upload-conversation": Notify when an
   * UploadConversation LRO is complete. * "update-or-analyze-conversation":
   * Notify when an analysis for a conversation is completed or when the
   * conversation is updated. The message will contain the conversation with
   * transcript, analysis and other metadata. Values are Pub/Sub topics. The
   * format of each Pub/Sub topic is: projects/{project}/topics/{topic}
   *
   * @var string[]
   */
  public $pubsubNotificationSettings;
  protected $redactionConfigType = GoogleCloudContactcenterinsightsV1RedactionConfig::class;
  protected $redactionConfigDataType = '';
  /**
   * Optional. The path to a Cloud Storage bucket containing conversation screen
   * recordings. If provided, Insights will search in the bucket for a screen
   * recording file matching the conversation data source object name prefix. If
   * matches are found, these file URIs will be stored in the conversation
   * screen recordings field.
   *
   * @var string
   */
  public $screenRecordingBucketUri;
  protected $speechConfigType = GoogleCloudContactcenterinsightsV1SpeechConfig::class;
  protected $speechConfigDataType = '';
  /**
   * Output only. The time at which the settings were last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Default analysis settings.
   *
   * @param GoogleCloudContactcenterinsightsV1SettingsAnalysisConfig $analysisConfig
   */
  public function setAnalysisConfig(GoogleCloudContactcenterinsightsV1SettingsAnalysisConfig $analysisConfig)
  {
    $this->analysisConfig = $analysisConfig;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1SettingsAnalysisConfig
   */
  public function getAnalysisConfig()
  {
    return $this->analysisConfig;
  }
  /**
   * The default TTL for newly-created conversations. If a conversation has a
   * specified expiration, that value will be used instead. Changing this value
   * will not change the expiration of existing conversations. Conversations
   * with no expire time persist until they are deleted.
   *
   * @param string $conversationTtl
   */
  public function setConversationTtl($conversationTtl)
  {
    $this->conversationTtl = $conversationTtl;
  }
  /**
   * @return string
   */
  public function getConversationTtl()
  {
    return $this->conversationTtl;
  }
  /**
   * Output only. The time at which the settings was created.
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
   * A language code to be applied to each transcript segment unless the segment
   * already specifies a language code. Language code defaults to "en-US" if it
   * is neither specified on the segment nor here.
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
   * Immutable. The resource name of the settings resource. Format:
   * projects/{project}/locations/{location}/settings
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
   * A map that maps a notification trigger to a Pub/Sub topic. Each time a
   * specified trigger occurs, Insights will notify the corresponding Pub/Sub
   * topic. Keys are notification triggers. Supported keys are: * "all-
   * triggers": Notify each time any of the supported triggers occurs. *
   * "create-analysis": Notify each time an analysis is created. * "create-
   * conversation": Notify each time a conversation is created. * "export-
   * insights-data": Notify each time an export is complete. * "ingest-
   * conversations": Notify each time an IngestConversations LRO is complete. *
   * "update-conversation": Notify each time a conversation is updated via
   * UpdateConversation. * "upload-conversation": Notify when an
   * UploadConversation LRO is complete. * "update-or-analyze-conversation":
   * Notify when an analysis for a conversation is completed or when the
   * conversation is updated. The message will contain the conversation with
   * transcript, analysis and other metadata. Values are Pub/Sub topics. The
   * format of each Pub/Sub topic is: projects/{project}/topics/{topic}
   *
   * @param string[] $pubsubNotificationSettings
   */
  public function setPubsubNotificationSettings($pubsubNotificationSettings)
  {
    $this->pubsubNotificationSettings = $pubsubNotificationSettings;
  }
  /**
   * @return string[]
   */
  public function getPubsubNotificationSettings()
  {
    return $this->pubsubNotificationSettings;
  }
  /**
   * Default DLP redaction resources to be applied while ingesting
   * conversations. This applies to conversations ingested from the
   * `UploadConversation` and `IngestConversations` endpoints, including
   * conversations coming from CCAI Platform.
   *
   * @param GoogleCloudContactcenterinsightsV1RedactionConfig $redactionConfig
   */
  public function setRedactionConfig(GoogleCloudContactcenterinsightsV1RedactionConfig $redactionConfig)
  {
    $this->redactionConfig = $redactionConfig;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1RedactionConfig
   */
  public function getRedactionConfig()
  {
    return $this->redactionConfig;
  }
  /**
   * Optional. The path to a Cloud Storage bucket containing conversation screen
   * recordings. If provided, Insights will search in the bucket for a screen
   * recording file matching the conversation data source object name prefix. If
   * matches are found, these file URIs will be stored in the conversation
   * screen recordings field.
   *
   * @param string $screenRecordingBucketUri
   */
  public function setScreenRecordingBucketUri($screenRecordingBucketUri)
  {
    $this->screenRecordingBucketUri = $screenRecordingBucketUri;
  }
  /**
   * @return string
   */
  public function getScreenRecordingBucketUri()
  {
    return $this->screenRecordingBucketUri;
  }
  /**
   * Optional. Default Speech-to-Text resources to use while ingesting audio
   * files. Optional, CCAI Insights will create a default if not provided. This
   * applies to conversations ingested from the `UploadConversation` and
   * `IngestConversations` endpoints, including conversations coming from CCAI
   * Platform.
   *
   * @param GoogleCloudContactcenterinsightsV1SpeechConfig $speechConfig
   */
  public function setSpeechConfig(GoogleCloudContactcenterinsightsV1SpeechConfig $speechConfig)
  {
    $this->speechConfig = $speechConfig;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1SpeechConfig
   */
  public function getSpeechConfig()
  {
    return $this->speechConfig;
  }
  /**
   * Output only. The time at which the settings were last updated.
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
class_alias(GoogleCloudContactcenterinsightsV1Settings::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1Settings');
