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

class GoogleCloudDialogflowV2StreamingRecognitionResult extends \Google\Collection
{
  /**
   * Not specified. Should never be used.
   */
  public const MESSAGE_TYPE_MESSAGE_TYPE_UNSPECIFIED = 'MESSAGE_TYPE_UNSPECIFIED';
  /**
   * Message contains a (possibly partial) transcript.
   */
  public const MESSAGE_TYPE_TRANSCRIPT = 'TRANSCRIPT';
  /**
   * This event indicates that the server has detected the end of the user's
   * speech utterance and expects no additional inputs. Therefore, the server
   * will not process additional audio (although it may subsequently return
   * additional results). The client should stop sending additional audio data,
   * half-close the gRPC connection, and wait for any additional results until
   * the server closes the gRPC connection. This message is only sent if
   * `single_utterance` was set to `true`, and is not used otherwise.
   */
  public const MESSAGE_TYPE_END_OF_SINGLE_UTTERANCE = 'END_OF_SINGLE_UTTERANCE';
  protected $collection_key = 'speechWordInfo';
  /**
   * The Speech confidence between 0.0 and 1.0 for the current portion of audio.
   * A higher number indicates an estimated greater likelihood that the
   * recognized words are correct. The default of 0.0 is a sentinel value
   * indicating that confidence was not set. This field is typically only
   * provided if `is_final` is true and you should not rely on it being accurate
   * or even set.
   *
   * @var float
   */
  public $confidence;
  /**
   * If `false`, the `StreamingRecognitionResult` represents an interim result
   * that may change. If `true`, the recognizer will not return any further
   * hypotheses about this piece of the audio. May only be populated for
   * `message_type` = `TRANSCRIPT`.
   *
   * @var bool
   */
  public $isFinal;
  /**
   * Detected language code for the transcript.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Type of the result message.
   *
   * @var string
   */
  public $messageType;
  /**
   * Time offset of the end of this Speech recognition result relative to the
   * beginning of the audio. Only populated for `message_type` = `TRANSCRIPT`.
   *
   * @var string
   */
  public $speechEndOffset;
  protected $speechWordInfoType = GoogleCloudDialogflowV2SpeechWordInfo::class;
  protected $speechWordInfoDataType = 'array';
  /**
   * Transcript text representing the words that the user spoke. Populated if
   * and only if `message_type` = `TRANSCRIPT`.
   *
   * @var string
   */
  public $transcript;

  /**
   * The Speech confidence between 0.0 and 1.0 for the current portion of audio.
   * A higher number indicates an estimated greater likelihood that the
   * recognized words are correct. The default of 0.0 is a sentinel value
   * indicating that confidence was not set. This field is typically only
   * provided if `is_final` is true and you should not rely on it being accurate
   * or even set.
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
   * If `false`, the `StreamingRecognitionResult` represents an interim result
   * that may change. If `true`, the recognizer will not return any further
   * hypotheses about this piece of the audio. May only be populated for
   * `message_type` = `TRANSCRIPT`.
   *
   * @param bool $isFinal
   */
  public function setIsFinal($isFinal)
  {
    $this->isFinal = $isFinal;
  }
  /**
   * @return bool
   */
  public function getIsFinal()
  {
    return $this->isFinal;
  }
  /**
   * Detected language code for the transcript.
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
   * Type of the result message.
   *
   * Accepted values: MESSAGE_TYPE_UNSPECIFIED, TRANSCRIPT,
   * END_OF_SINGLE_UTTERANCE
   *
   * @param self::MESSAGE_TYPE_* $messageType
   */
  public function setMessageType($messageType)
  {
    $this->messageType = $messageType;
  }
  /**
   * @return self::MESSAGE_TYPE_*
   */
  public function getMessageType()
  {
    return $this->messageType;
  }
  /**
   * Time offset of the end of this Speech recognition result relative to the
   * beginning of the audio. Only populated for `message_type` = `TRANSCRIPT`.
   *
   * @param string $speechEndOffset
   */
  public function setSpeechEndOffset($speechEndOffset)
  {
    $this->speechEndOffset = $speechEndOffset;
  }
  /**
   * @return string
   */
  public function getSpeechEndOffset()
  {
    return $this->speechEndOffset;
  }
  /**
   * Word-specific information for the words recognized by Speech in transcript.
   * Populated if and only if `message_type` = `TRANSCRIPT` and
   * [InputAudioConfig.enable_word_info] is set.
   *
   * @param GoogleCloudDialogflowV2SpeechWordInfo[] $speechWordInfo
   */
  public function setSpeechWordInfo($speechWordInfo)
  {
    $this->speechWordInfo = $speechWordInfo;
  }
  /**
   * @return GoogleCloudDialogflowV2SpeechWordInfo[]
   */
  public function getSpeechWordInfo()
  {
    return $this->speechWordInfo;
  }
  /**
   * Transcript text representing the words that the user spoke. Populated if
   * and only if `message_type` = `TRANSCRIPT`.
   *
   * @param string $transcript
   */
  public function setTranscript($transcript)
  {
    $this->transcript = $transcript;
  }
  /**
   * @return string
   */
  public function getTranscript()
  {
    return $this->transcript;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2StreamingRecognitionResult::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2StreamingRecognitionResult');
