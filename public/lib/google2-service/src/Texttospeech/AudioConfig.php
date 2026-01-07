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

namespace Google\Service\Texttospeech;

class AudioConfig extends \Google\Collection
{
  /**
   * Not specified. Only used by GenerateVoiceCloningKey. Otherwise, will return
   * result google.rpc.Code.INVALID_ARGUMENT.
   */
  public const AUDIO_ENCODING_AUDIO_ENCODING_UNSPECIFIED = 'AUDIO_ENCODING_UNSPECIFIED';
  /**
   * Uncompressed 16-bit signed little-endian samples (Linear PCM). Audio
   * content returned as LINEAR16 also contains a WAV header.
   */
  public const AUDIO_ENCODING_LINEAR16 = 'LINEAR16';
  /**
   * MP3 audio at 32kbps.
   */
  public const AUDIO_ENCODING_MP3 = 'MP3';
  /**
   * Opus encoded audio wrapped in an ogg container. The result is a file which
   * can be played natively on Android, and in browsers (at least Chrome and
   * Firefox). The quality of the encoding is considerably higher than MP3 while
   * using approximately the same bitrate.
   */
  public const AUDIO_ENCODING_OGG_OPUS = 'OGG_OPUS';
  /**
   * 8-bit samples that compand 14-bit audio samples using G.711 PCMU/mu-law.
   * Audio content returned as MULAW also contains a WAV header.
   */
  public const AUDIO_ENCODING_MULAW = 'MULAW';
  /**
   * 8-bit samples that compand 14-bit audio samples using G.711 PCMU/A-law.
   * Audio content returned as ALAW also contains a WAV header.
   */
  public const AUDIO_ENCODING_ALAW = 'ALAW';
  /**
   * Uncompressed 16-bit signed little-endian samples (Linear PCM). Note that as
   * opposed to LINEAR16, audio won't be wrapped in a WAV (or any other) header.
   */
  public const AUDIO_ENCODING_PCM = 'PCM';
  /**
   * M4A audio.
   */
  public const AUDIO_ENCODING_M4A = 'M4A';
  protected $collection_key = 'effectsProfileId';
  /**
   * Required. The format of the audio byte stream.
   *
   * @var string
   */
  public $audioEncoding;
  /**
   * Optional. Input only. An identifier which selects 'audio effects' profiles
   * that are applied on (post synthesized) text to speech. Effects are applied
   * on top of each other in the order they are given. See [audio
   * profiles](https://cloud.google.com/text-to-speech/docs/audio-profiles) for
   * current supported profile ids.
   *
   * @var string[]
   */
  public $effectsProfileId;
  /**
   * Optional. Input only. Speaking pitch, in the range [-20.0, 20.0]. 20 means
   * increase 20 semitones from the original pitch. -20 means decrease 20
   * semitones from the original pitch.
   *
   * @var 
   */
  public $pitch;
  /**
   * Optional. The synthesis sample rate (in hertz) for this audio. When this is
   * specified in SynthesizeSpeechRequest, if this is different from the voice's
   * natural sample rate, then the synthesizer will honor this request by
   * converting to the desired sample rate (which might result in worse audio
   * quality), unless the specified sample rate is not supported for the
   * encoding chosen, in which case it will fail the request and return
   * google.rpc.Code.INVALID_ARGUMENT.
   *
   * @var int
   */
  public $sampleRateHertz;
  /**
   * Optional. Input only. Speaking rate/speed, in the range [0.25, 2.0]. 1.0 is
   * the normal native speed supported by the specific voice. 2.0 is twice as
   * fast, and 0.5 is half as fast. If unset(0.0), defaults to the native 1.0
   * speed. Any other values < 0.25 or > 2.0 will return an error.
   *
   * @var 
   */
  public $speakingRate;
  /**
   * Optional. Input only. Volume gain (in dB) of the normal native volume
   * supported by the specific voice, in the range [-96.0, 16.0]. If unset, or
   * set to a value of 0.0 (dB), will play at normal native signal amplitude. A
   * value of -6.0 (dB) will play at approximately half the amplitude of the
   * normal native signal amplitude. A value of +6.0 (dB) will play at
   * approximately twice the amplitude of the normal native signal amplitude.
   * Strongly recommend not to exceed +10 (dB) as there's usually no effective
   * increase in loudness for any value greater than that.
   *
   * @var 
   */
  public $volumeGainDb;

  /**
   * Required. The format of the audio byte stream.
   *
   * Accepted values: AUDIO_ENCODING_UNSPECIFIED, LINEAR16, MP3, OGG_OPUS,
   * MULAW, ALAW, PCM, M4A
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
   * Optional. Input only. An identifier which selects 'audio effects' profiles
   * that are applied on (post synthesized) text to speech. Effects are applied
   * on top of each other in the order they are given. See [audio
   * profiles](https://cloud.google.com/text-to-speech/docs/audio-profiles) for
   * current supported profile ids.
   *
   * @param string[] $effectsProfileId
   */
  public function setEffectsProfileId($effectsProfileId)
  {
    $this->effectsProfileId = $effectsProfileId;
  }
  /**
   * @return string[]
   */
  public function getEffectsProfileId()
  {
    return $this->effectsProfileId;
  }
  public function setPitch($pitch)
  {
    $this->pitch = $pitch;
  }
  public function getPitch()
  {
    return $this->pitch;
  }
  /**
   * Optional. The synthesis sample rate (in hertz) for this audio. When this is
   * specified in SynthesizeSpeechRequest, if this is different from the voice's
   * natural sample rate, then the synthesizer will honor this request by
   * converting to the desired sample rate (which might result in worse audio
   * quality), unless the specified sample rate is not supported for the
   * encoding chosen, in which case it will fail the request and return
   * google.rpc.Code.INVALID_ARGUMENT.
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
  public function setSpeakingRate($speakingRate)
  {
    $this->speakingRate = $speakingRate;
  }
  public function getSpeakingRate()
  {
    return $this->speakingRate;
  }
  public function setVolumeGainDb($volumeGainDb)
  {
    $this->volumeGainDb = $volumeGainDb;
  }
  public function getVolumeGainDb()
  {
    return $this->volumeGainDb;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AudioConfig::class, 'Google_Service_Texttospeech_AudioConfig');
