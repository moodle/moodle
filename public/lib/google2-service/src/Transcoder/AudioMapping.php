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

namespace Google\Service\Transcoder;

class AudioMapping extends \Google\Model
{
  /**
   * Required. The EditAtom.key that references the atom with audio inputs in
   * the JobConfig.edit_list.
   *
   * @var string
   */
  public $atomKey;
  /**
   * Audio volume control in dB. Negative values decrease volume, positive
   * values increase. The default is 0.
   *
   * @var 
   */
  public $gainDb;
  /**
   * Required. The zero-based index of the channel in the input audio stream.
   *
   * @var int
   */
  public $inputChannel;
  /**
   * Required. The Input.key that identifies the input file.
   *
   * @var string
   */
  public $inputKey;
  /**
   * Required. The zero-based index of the track in the input file.
   *
   * @var int
   */
  public $inputTrack;
  /**
   * Required. The zero-based index of the channel in the output audio stream.
   *
   * @var int
   */
  public $outputChannel;

  /**
   * Required. The EditAtom.key that references the atom with audio inputs in
   * the JobConfig.edit_list.
   *
   * @param string $atomKey
   */
  public function setAtomKey($atomKey)
  {
    $this->atomKey = $atomKey;
  }
  /**
   * @return string
   */
  public function getAtomKey()
  {
    return $this->atomKey;
  }
  public function setGainDb($gainDb)
  {
    $this->gainDb = $gainDb;
  }
  public function getGainDb()
  {
    return $this->gainDb;
  }
  /**
   * Required. The zero-based index of the channel in the input audio stream.
   *
   * @param int $inputChannel
   */
  public function setInputChannel($inputChannel)
  {
    $this->inputChannel = $inputChannel;
  }
  /**
   * @return int
   */
  public function getInputChannel()
  {
    return $this->inputChannel;
  }
  /**
   * Required. The Input.key that identifies the input file.
   *
   * @param string $inputKey
   */
  public function setInputKey($inputKey)
  {
    $this->inputKey = $inputKey;
  }
  /**
   * @return string
   */
  public function getInputKey()
  {
    return $this->inputKey;
  }
  /**
   * Required. The zero-based index of the track in the input file.
   *
   * @param int $inputTrack
   */
  public function setInputTrack($inputTrack)
  {
    $this->inputTrack = $inputTrack;
  }
  /**
   * @return int
   */
  public function getInputTrack()
  {
    return $this->inputTrack;
  }
  /**
   * Required. The zero-based index of the channel in the output audio stream.
   *
   * @param int $outputChannel
   */
  public function setOutputChannel($outputChannel)
  {
    $this->outputChannel = $outputChannel;
  }
  /**
   * @return int
   */
  public function getOutputChannel()
  {
    return $this->outputChannel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AudioMapping::class, 'Google_Service_Transcoder_AudioMapping');
