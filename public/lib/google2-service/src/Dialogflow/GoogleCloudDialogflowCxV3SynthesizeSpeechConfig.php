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

class GoogleCloudDialogflowCxV3SynthesizeSpeechConfig extends \Google\Collection
{
  protected $collection_key = 'effectsProfileId';
  /**
   * Optional. An identifier which selects 'audio effects' profiles that are
   * applied on (post synthesized) text to speech. Effects are applied on top of
   * each other in the order they are given.
   *
   * @var string[]
   */
  public $effectsProfileId;
  /**
   * Optional. Speaking pitch, in the range [-20.0, 20.0]. 20 means increase 20
   * semitones from the original pitch. -20 means decrease 20 semitones from the
   * original pitch.
   *
   * @var 
   */
  public $pitch;
  /**
   * Optional. Speaking rate/speed, in the range [0.25, 4.0]. 1.0 is the normal
   * native speed supported by the specific voice. 2.0 is twice as fast, and 0.5
   * is half as fast. If unset(0.0), defaults to the native 1.0 speed. Any other
   * values < 0.25 or > 4.0 will return an error.
   *
   * @var 
   */
  public $speakingRate;
  protected $voiceType = GoogleCloudDialogflowCxV3VoiceSelectionParams::class;
  protected $voiceDataType = '';
  /**
   * Optional. Volume gain (in dB) of the normal native volume supported by the
   * specific voice, in the range [-96.0, 16.0]. If unset, or set to a value of
   * 0.0 (dB), will play at normal native signal amplitude. A value of -6.0 (dB)
   * will play at approximately half the amplitude of the normal native signal
   * amplitude. A value of +6.0 (dB) will play at approximately twice the
   * amplitude of the normal native signal amplitude. We strongly recommend not
   * to exceed +10 (dB) as there's usually no effective increase in loudness for
   * any value greater than that.
   *
   * @var 
   */
  public $volumeGainDb;

  /**
   * Optional. An identifier which selects 'audio effects' profiles that are
   * applied on (post synthesized) text to speech. Effects are applied on top of
   * each other in the order they are given.
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
  public function setSpeakingRate($speakingRate)
  {
    $this->speakingRate = $speakingRate;
  }
  public function getSpeakingRate()
  {
    return $this->speakingRate;
  }
  /**
   * Optional. The desired voice of the synthesized audio.
   *
   * @param GoogleCloudDialogflowCxV3VoiceSelectionParams $voice
   */
  public function setVoice(GoogleCloudDialogflowCxV3VoiceSelectionParams $voice)
  {
    $this->voice = $voice;
  }
  /**
   * @return GoogleCloudDialogflowCxV3VoiceSelectionParams
   */
  public function getVoice()
  {
    return $this->voice;
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
class_alias(GoogleCloudDialogflowCxV3SynthesizeSpeechConfig::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3SynthesizeSpeechConfig');
