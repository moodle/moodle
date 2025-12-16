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

namespace Google\Service\CloudVideoIntelligence;

class GoogleCloudVideointelligenceV1SpeechTranscriptionConfig extends \Google\Collection
{
  protected $collection_key = 'speechContexts';
  /**
   * Optional. For file formats, such as MXF or MKV, supporting multiple audio
   * tracks, specify up to two tracks. Default: track 0.
   *
   * @var int[]
   */
  public $audioTracks;
  /**
   * Optional. If set, specifies the estimated number of speakers in the
   * conversation. If not set, defaults to '2'. Ignored unless
   * enable_speaker_diarization is set to true.
   *
   * @var int
   */
  public $diarizationSpeakerCount;
  /**
   * Optional. If 'true', adds punctuation to recognition result hypotheses.
   * This feature is only available in select languages. Setting this for
   * requests in other languages has no effect at all. The default 'false' value
   * does not add punctuation to result hypotheses. NOTE: "This is currently
   * offered as an experimental service, complimentary to all users. In the
   * future this may be exclusively available as a premium feature."
   *
   * @var bool
   */
  public $enableAutomaticPunctuation;
  /**
   * Optional. If 'true', enables speaker detection for each recognized word in
   * the top alternative of the recognition result using a speaker_tag provided
   * in the WordInfo. Note: When this is true, we send all the words from the
   * beginning of the audio for the top alternative in every consecutive
   * response. This is done in order to improve our speaker tags as our models
   * learn to identify the speakers in the conversation over time.
   *
   * @var bool
   */
  public $enableSpeakerDiarization;
  /**
   * Optional. If `true`, the top result includes a list of words and the
   * confidence for those words. If `false`, no word-level confidence
   * information is returned. The default is `false`.
   *
   * @var bool
   */
  public $enableWordConfidence;
  /**
   * Optional. If set to `true`, the server will attempt to filter out
   * profanities, replacing all but the initial character in each filtered word
   * with asterisks, e.g. "f***". If set to `false` or omitted, profanities
   * won't be filtered out.
   *
   * @var bool
   */
  public $filterProfanity;
  /**
   * Required. *Required* The language of the supplied audio as a
   * [BCP-47](https://www.rfc-editor.org/rfc/bcp/bcp47.txt) language tag.
   * Example: "en-US". See [Language
   * Support](https://cloud.google.com/speech/docs/languages) for a list of the
   * currently supported language codes.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Optional. Maximum number of recognition hypotheses to be returned.
   * Specifically, the maximum number of `SpeechRecognitionAlternative` messages
   * within each `SpeechTranscription`. The server may return fewer than
   * `max_alternatives`. Valid values are `0`-`30`. A value of `0` or `1` will
   * return a maximum of one. If omitted, will return a maximum of one.
   *
   * @var int
   */
  public $maxAlternatives;
  protected $speechContextsType = GoogleCloudVideointelligenceV1SpeechContext::class;
  protected $speechContextsDataType = 'array';

  /**
   * Optional. For file formats, such as MXF or MKV, supporting multiple audio
   * tracks, specify up to two tracks. Default: track 0.
   *
   * @param int[] $audioTracks
   */
  public function setAudioTracks($audioTracks)
  {
    $this->audioTracks = $audioTracks;
  }
  /**
   * @return int[]
   */
  public function getAudioTracks()
  {
    return $this->audioTracks;
  }
  /**
   * Optional. If set, specifies the estimated number of speakers in the
   * conversation. If not set, defaults to '2'. Ignored unless
   * enable_speaker_diarization is set to true.
   *
   * @param int $diarizationSpeakerCount
   */
  public function setDiarizationSpeakerCount($diarizationSpeakerCount)
  {
    $this->diarizationSpeakerCount = $diarizationSpeakerCount;
  }
  /**
   * @return int
   */
  public function getDiarizationSpeakerCount()
  {
    return $this->diarizationSpeakerCount;
  }
  /**
   * Optional. If 'true', adds punctuation to recognition result hypotheses.
   * This feature is only available in select languages. Setting this for
   * requests in other languages has no effect at all. The default 'false' value
   * does not add punctuation to result hypotheses. NOTE: "This is currently
   * offered as an experimental service, complimentary to all users. In the
   * future this may be exclusively available as a premium feature."
   *
   * @param bool $enableAutomaticPunctuation
   */
  public function setEnableAutomaticPunctuation($enableAutomaticPunctuation)
  {
    $this->enableAutomaticPunctuation = $enableAutomaticPunctuation;
  }
  /**
   * @return bool
   */
  public function getEnableAutomaticPunctuation()
  {
    return $this->enableAutomaticPunctuation;
  }
  /**
   * Optional. If 'true', enables speaker detection for each recognized word in
   * the top alternative of the recognition result using a speaker_tag provided
   * in the WordInfo. Note: When this is true, we send all the words from the
   * beginning of the audio for the top alternative in every consecutive
   * response. This is done in order to improve our speaker tags as our models
   * learn to identify the speakers in the conversation over time.
   *
   * @param bool $enableSpeakerDiarization
   */
  public function setEnableSpeakerDiarization($enableSpeakerDiarization)
  {
    $this->enableSpeakerDiarization = $enableSpeakerDiarization;
  }
  /**
   * @return bool
   */
  public function getEnableSpeakerDiarization()
  {
    return $this->enableSpeakerDiarization;
  }
  /**
   * Optional. If `true`, the top result includes a list of words and the
   * confidence for those words. If `false`, no word-level confidence
   * information is returned. The default is `false`.
   *
   * @param bool $enableWordConfidence
   */
  public function setEnableWordConfidence($enableWordConfidence)
  {
    $this->enableWordConfidence = $enableWordConfidence;
  }
  /**
   * @return bool
   */
  public function getEnableWordConfidence()
  {
    return $this->enableWordConfidence;
  }
  /**
   * Optional. If set to `true`, the server will attempt to filter out
   * profanities, replacing all but the initial character in each filtered word
   * with asterisks, e.g. "f***". If set to `false` or omitted, profanities
   * won't be filtered out.
   *
   * @param bool $filterProfanity
   */
  public function setFilterProfanity($filterProfanity)
  {
    $this->filterProfanity = $filterProfanity;
  }
  /**
   * @return bool
   */
  public function getFilterProfanity()
  {
    return $this->filterProfanity;
  }
  /**
   * Required. *Required* The language of the supplied audio as a
   * [BCP-47](https://www.rfc-editor.org/rfc/bcp/bcp47.txt) language tag.
   * Example: "en-US". See [Language
   * Support](https://cloud.google.com/speech/docs/languages) for a list of the
   * currently supported language codes.
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
   * Optional. Maximum number of recognition hypotheses to be returned.
   * Specifically, the maximum number of `SpeechRecognitionAlternative` messages
   * within each `SpeechTranscription`. The server may return fewer than
   * `max_alternatives`. Valid values are `0`-`30`. A value of `0` or `1` will
   * return a maximum of one. If omitted, will return a maximum of one.
   *
   * @param int $maxAlternatives
   */
  public function setMaxAlternatives($maxAlternatives)
  {
    $this->maxAlternatives = $maxAlternatives;
  }
  /**
   * @return int
   */
  public function getMaxAlternatives()
  {
    return $this->maxAlternatives;
  }
  /**
   * Optional. A means to provide context to assist the speech recognition.
   *
   * @param GoogleCloudVideointelligenceV1SpeechContext[] $speechContexts
   */
  public function setSpeechContexts($speechContexts)
  {
    $this->speechContexts = $speechContexts;
  }
  /**
   * @return GoogleCloudVideointelligenceV1SpeechContext[]
   */
  public function getSpeechContexts()
  {
    return $this->speechContexts;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVideointelligenceV1SpeechTranscriptionConfig::class, 'Google_Service_CloudVideoIntelligence_GoogleCloudVideointelligenceV1SpeechTranscriptionConfig');
