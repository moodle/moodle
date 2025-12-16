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

class GoogleCloudDialogflowCxV3SecuritySettingsAudioExportSettings extends \Google\Model
{
  /**
   * Unspecified. Do not use.
   */
  public const AUDIO_FORMAT_AUDIO_FORMAT_UNSPECIFIED = 'AUDIO_FORMAT_UNSPECIFIED';
  /**
   * G.711 mu-law PCM with 8kHz sample rate.
   */
  public const AUDIO_FORMAT_MULAW = 'MULAW';
  /**
   * MP3 file format.
   */
  public const AUDIO_FORMAT_MP3 = 'MP3';
  /**
   * OGG Vorbis.
   */
  public const AUDIO_FORMAT_OGG = 'OGG';
  /**
   * Filename pattern for exported audio. {conversation} and {timestamp} are
   * placeholders that will be replaced with the conversation ID and epoch
   * micros of the conversation. For example,
   * "{conversation}/recording_{timestamp}.mulaw".
   *
   * @var string
   */
  public $audioExportPattern;
  /**
   * File format for exported audio file. Currently only in telephony
   * recordings.
   *
   * @var string
   */
  public $audioFormat;
  /**
   * Enable audio redaction if it is true. Note that this only redacts end-user
   * audio data; Synthesised audio from the virtual agent is not redacted.
   *
   * @var bool
   */
  public $enableAudioRedaction;
  /**
   * Cloud Storage bucket to export audio record to. Setting this field would
   * grant the Storage Object Creator role to the Dialogflow Service Agent. API
   * caller that tries to modify this field should have the permission of
   * storage.buckets.setIamPolicy.
   *
   * @var string
   */
  public $gcsBucket;
  /**
   * Whether to store TTS audio. By default, TTS audio from the virtual agent is
   * not exported.
   *
   * @var bool
   */
  public $storeTtsAudio;

  /**
   * Filename pattern for exported audio. {conversation} and {timestamp} are
   * placeholders that will be replaced with the conversation ID and epoch
   * micros of the conversation. For example,
   * "{conversation}/recording_{timestamp}.mulaw".
   *
   * @param string $audioExportPattern
   */
  public function setAudioExportPattern($audioExportPattern)
  {
    $this->audioExportPattern = $audioExportPattern;
  }
  /**
   * @return string
   */
  public function getAudioExportPattern()
  {
    return $this->audioExportPattern;
  }
  /**
   * File format for exported audio file. Currently only in telephony
   * recordings.
   *
   * Accepted values: AUDIO_FORMAT_UNSPECIFIED, MULAW, MP3, OGG
   *
   * @param self::AUDIO_FORMAT_* $audioFormat
   */
  public function setAudioFormat($audioFormat)
  {
    $this->audioFormat = $audioFormat;
  }
  /**
   * @return self::AUDIO_FORMAT_*
   */
  public function getAudioFormat()
  {
    return $this->audioFormat;
  }
  /**
   * Enable audio redaction if it is true. Note that this only redacts end-user
   * audio data; Synthesised audio from the virtual agent is not redacted.
   *
   * @param bool $enableAudioRedaction
   */
  public function setEnableAudioRedaction($enableAudioRedaction)
  {
    $this->enableAudioRedaction = $enableAudioRedaction;
  }
  /**
   * @return bool
   */
  public function getEnableAudioRedaction()
  {
    return $this->enableAudioRedaction;
  }
  /**
   * Cloud Storage bucket to export audio record to. Setting this field would
   * grant the Storage Object Creator role to the Dialogflow Service Agent. API
   * caller that tries to modify this field should have the permission of
   * storage.buckets.setIamPolicy.
   *
   * @param string $gcsBucket
   */
  public function setGcsBucket($gcsBucket)
  {
    $this->gcsBucket = $gcsBucket;
  }
  /**
   * @return string
   */
  public function getGcsBucket()
  {
    return $this->gcsBucket;
  }
  /**
   * Whether to store TTS audio. By default, TTS audio from the virtual agent is
   * not exported.
   *
   * @param bool $storeTtsAudio
   */
  public function setStoreTtsAudio($storeTtsAudio)
  {
    $this->storeTtsAudio = $storeTtsAudio;
  }
  /**
   * @return bool
   */
  public function getStoreTtsAudio()
  {
    return $this->storeTtsAudio;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3SecuritySettingsAudioExportSettings::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3SecuritySettingsAudioExportSettings');
