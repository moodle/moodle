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

class GoogleCloudContactcenterinsightsV1alpha1IngestConversationsRequest extends \Google\Model
{
  protected $conversationConfigType = GoogleCloudContactcenterinsightsV1alpha1IngestConversationsRequestConversationConfig::class;
  protected $conversationConfigDataType = '';
  protected $gcsSourceType = GoogleCloudContactcenterinsightsV1alpha1IngestConversationsRequestGcsSource::class;
  protected $gcsSourceDataType = '';
  /**
   * Required. The parent resource for new conversations.
   *
   * @var string
   */
  public $parent;
  protected $redactionConfigType = GoogleCloudContactcenterinsightsV1alpha1RedactionConfig::class;
  protected $redactionConfigDataType = '';
  /**
   * Optional. If set, this fields indicates the number of objects to ingest
   * from the Cloud Storage bucket. If empty, the entire bucket will be
   * ingested. Unless they are first deleted, conversations produced through
   * sampling won't be ingested by subsequent ingest requests.
   *
   * @var int
   */
  public $sampleSize;
  protected $speechConfigType = GoogleCloudContactcenterinsightsV1alpha1SpeechConfig::class;
  protected $speechConfigDataType = '';
  protected $transcriptObjectConfigType = GoogleCloudContactcenterinsightsV1alpha1IngestConversationsRequestTranscriptObjectConfig::class;
  protected $transcriptObjectConfigDataType = '';

  /**
   * Configuration that applies to all conversations.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1IngestConversationsRequestConversationConfig $conversationConfig
   */
  public function setConversationConfig(GoogleCloudContactcenterinsightsV1alpha1IngestConversationsRequestConversationConfig $conversationConfig)
  {
    $this->conversationConfig = $conversationConfig;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1IngestConversationsRequestConversationConfig
   */
  public function getConversationConfig()
  {
    return $this->conversationConfig;
  }
  /**
   * A cloud storage bucket source. Note that any previously ingested objects
   * from the source will be skipped to avoid duplication.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1IngestConversationsRequestGcsSource $gcsSource
   */
  public function setGcsSource(GoogleCloudContactcenterinsightsV1alpha1IngestConversationsRequestGcsSource $gcsSource)
  {
    $this->gcsSource = $gcsSource;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1IngestConversationsRequestGcsSource
   */
  public function getGcsSource()
  {
    return $this->gcsSource;
  }
  /**
   * Required. The parent resource for new conversations.
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * Optional. DLP settings for transcript redaction. Optional, will default to
   * the config specified in Settings.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1RedactionConfig $redactionConfig
   */
  public function setRedactionConfig(GoogleCloudContactcenterinsightsV1alpha1RedactionConfig $redactionConfig)
  {
    $this->redactionConfig = $redactionConfig;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1RedactionConfig
   */
  public function getRedactionConfig()
  {
    return $this->redactionConfig;
  }
  /**
   * Optional. If set, this fields indicates the number of objects to ingest
   * from the Cloud Storage bucket. If empty, the entire bucket will be
   * ingested. Unless they are first deleted, conversations produced through
   * sampling won't be ingested by subsequent ingest requests.
   *
   * @param int $sampleSize
   */
  public function setSampleSize($sampleSize)
  {
    $this->sampleSize = $sampleSize;
  }
  /**
   * @return int
   */
  public function getSampleSize()
  {
    return $this->sampleSize;
  }
  /**
   * Optional. Default Speech-to-Text configuration. Optional, will default to
   * the config specified in Settings.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1SpeechConfig $speechConfig
   */
  public function setSpeechConfig(GoogleCloudContactcenterinsightsV1alpha1SpeechConfig $speechConfig)
  {
    $this->speechConfig = $speechConfig;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1SpeechConfig
   */
  public function getSpeechConfig()
  {
    return $this->speechConfig;
  }
  /**
   * Configuration for when `source` contains conversation transcripts.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1IngestConversationsRequestTranscriptObjectConfig $transcriptObjectConfig
   */
  public function setTranscriptObjectConfig(GoogleCloudContactcenterinsightsV1alpha1IngestConversationsRequestTranscriptObjectConfig $transcriptObjectConfig)
  {
    $this->transcriptObjectConfig = $transcriptObjectConfig;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1IngestConversationsRequestTranscriptObjectConfig
   */
  public function getTranscriptObjectConfig()
  {
    return $this->transcriptObjectConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1alpha1IngestConversationsRequest::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1alpha1IngestConversationsRequest');
