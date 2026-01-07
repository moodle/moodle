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

class GoogleCloudDialogflowCxV3OutputAudioConfig extends \Google\Model
{
  /**
   * Not specified.
   */
  public const AUDIO_ENCODING_OUTPUT_AUDIO_ENCODING_UNSPECIFIED = 'OUTPUT_AUDIO_ENCODING_UNSPECIFIED';
  /**
   * Uncompressed 16-bit signed little-endian samples (Linear PCM). Audio
   * content returned as LINEAR16 also contains a WAV header. LINT: LEGACY_NAMES
   */
  public const AUDIO_ENCODING_OUTPUT_AUDIO_ENCODING_LINEAR_16 = 'OUTPUT_AUDIO_ENCODING_LINEAR_16';
  /**
   * MP3 audio at 32kbps.
   */
  public const AUDIO_ENCODING_OUTPUT_AUDIO_ENCODING_MP3 = 'OUTPUT_AUDIO_ENCODING_MP3';
  /**
   * MP3 audio at 64kbps. LINT: LEGACY_NAMES
   */
  public const AUDIO_ENCODING_OUTPUT_AUDIO_ENCODING_MP3_64_KBPS = 'OUTPUT_AUDIO_ENCODING_MP3_64_KBPS';
  /**
   * Opus encoded audio wrapped in an ogg container. The result will be a file
   * which can be played natively on Android, and in browsers (at least Chrome
   * and Firefox). The quality of the encoding is considerably higher than MP3
   * while using approximately the same bitrate.
   */
  public const AUDIO_ENCODING_OUTPUT_AUDIO_ENCODING_OGG_OPUS = 'OUTPUT_AUDIO_ENCODING_OGG_OPUS';
  /**
   * 8-bit samples that compand 14-bit audio samples using G.711 PCMU/mu-law.
   */
  public const AUDIO_ENCODING_OUTPUT_AUDIO_ENCODING_MULAW = 'OUTPUT_AUDIO_ENCODING_MULAW';
  /**
   * 8-bit samples that compand 13-bit audio samples using G.711 PCMU/a-law.
   */
  public const AUDIO_ENCODING_OUTPUT_AUDIO_ENCODING_ALAW = 'OUTPUT_AUDIO_ENCODING_ALAW';
  /**
   * Required. Audio encoding of the synthesized audio content.
   *
   * @var string
   */
  public $audioEncoding;
  /**
   * Optional. The synthesis sample rate (in hertz) for this audio. If not
   * provided, then the synthesizer will use the default sample rate based on
   * the audio encoding. If this is different from the voice's natural sample
   * rate, then the synthesizer will honor this request by converting to the
   * desired sample rate (which might result in worse audio quality).
   *
   * @var int
   */
  public $sampleRateHertz;
  protected $synthesizeSpeechConfigType = GoogleCloudDialogflowCxV3SynthesizeSpeechConfig::class;
  protected $synthesizeSpeechConfigDataType = '';

  /**
   * Required. Audio encoding of the synthesized audio content.
   *
   * Accepted values: OUTPUT_AUDIO_ENCODING_UNSPECIFIED,
   * OUTPUT_AUDIO_ENCODING_LINEAR_16, OUTPUT_AUDIO_ENCODING_MP3,
   * OUTPUT_AUDIO_ENCODING_MP3_64_KBPS, OUTPUT_AUDIO_ENCODING_OGG_OPUS,
   * OUTPUT_AUDIO_ENCODING_MULAW, OUTPUT_AUDIO_ENCODING_ALAW
   *
   * @param self::AUDIO_ENCODING_* $audioEncoding
   */
  public function setAudioEncoding($audioEncoding)
  {
    $this->audioEncoding = $audioEncoding;
  }
  /**
   * @return self::AUDIO_ENCODING_*
   */
  public function getAudioEncoding()
  {
    return $this->audioEncoding;
  }
  /**
   * Optional. The synthesis sample rate (in hertz) for this audio. If not
   * provided, then the synthesizer will use the default sample rate based on
   * the audio encoding. If this is different from the voice's natural sample
   * rate, then the synthesizer will honor this request by converting to the
   * desired sample rate (which might result in worse audio quality).
   *
   * @param int $sampleRateHertz
   */
  public function setSampleRateHertz($sampleRateHertz)
  {
    $this->sampleRateHertz = $sampleRateHertz;
  }
  /**
   * @return int
   */
  public function getSampleRateHertz()
  {
    return $this->sampleRateHertz;
  }
  /**
   * Optional. Configuration of how speech should be synthesized. If not
   * specified, Agent.text_to_speech_settings is applied.
   *
   * @param GoogleCloudDialogflowCxV3SynthesizeSpeechConfig $synthesizeSpeechConfig
   */
  public function setSynthesizeSpeechConfig(GoogleCloudDialogflowCxV3SynthesizeSpeechConfig $synthesizeSpeechConfig)
  {
    $this->synthesizeSpeechConfig = $synthesizeSpeechConfig;
  }
  /**
   * @return GoogleCloudDialogflowCxV3SynthesizeSpeechConfig
   */
  public function getSynthesizeSpeechConfig()
  {
    return $this->synthesizeSpeechConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3OutputAudioConfig::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3OutputAudioConfig');
