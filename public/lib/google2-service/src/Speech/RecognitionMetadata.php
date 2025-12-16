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

namespace Google\Service\Speech;

class RecognitionMetadata extends \Google\Model
{
  /**
   * Use case is either unknown or is something other than one of the other
   * values below.
   */
  public const INTERACTION_TYPE_INTERACTION_TYPE_UNSPECIFIED = 'INTERACTION_TYPE_UNSPECIFIED';
  /**
   * Multiple people in a conversation or discussion. For example in a meeting
   * with two or more people actively participating. Typically all the primary
   * people speaking would be in the same room (if not, see PHONE_CALL)
   */
  public const INTERACTION_TYPE_DISCUSSION = 'DISCUSSION';
  /**
   * One or more persons lecturing or presenting to others, mostly
   * uninterrupted.
   */
  public const INTERACTION_TYPE_PRESENTATION = 'PRESENTATION';
  /**
   * A phone-call or video-conference in which two or more people, who are not
   * in the same room, are actively participating.
   */
  public const INTERACTION_TYPE_PHONE_CALL = 'PHONE_CALL';
  /**
   * A recorded message intended for another person to listen to.
   */
  public const INTERACTION_TYPE_VOICEMAIL = 'VOICEMAIL';
  /**
   * Professionally produced audio (eg. TV Show, Podcast).
   */
  public const INTERACTION_TYPE_PROFESSIONALLY_PRODUCED = 'PROFESSIONALLY_PRODUCED';
  /**
   * Transcribe spoken questions and queries into text.
   */
  public const INTERACTION_TYPE_VOICE_SEARCH = 'VOICE_SEARCH';
  /**
   * Transcribe voice commands, such as for controlling a device.
   */
  public const INTERACTION_TYPE_VOICE_COMMAND = 'VOICE_COMMAND';
  /**
   * Transcribe speech to text to create a written document, such as a text-
   * message, email or report.
   */
  public const INTERACTION_TYPE_DICTATION = 'DICTATION';
  /**
   * Audio type is not known.
   */
  public const MICROPHONE_DISTANCE_MICROPHONE_DISTANCE_UNSPECIFIED = 'MICROPHONE_DISTANCE_UNSPECIFIED';
  /**
   * The audio was captured from a closely placed microphone. Eg. phone,
   * dictaphone, or handheld microphone. Generally if there speaker is within 1
   * meter of the microphone.
   */
  public const MICROPHONE_DISTANCE_NEARFIELD = 'NEARFIELD';
  /**
   * The speaker if within 3 meters of the microphone.
   */
  public const MICROPHONE_DISTANCE_MIDFIELD = 'MIDFIELD';
  /**
   * The speaker is more than 3 meters away from the microphone.
   */
  public const MICROPHONE_DISTANCE_FARFIELD = 'FARFIELD';
  /**
   * Unknown original media type.
   */
  public const ORIGINAL_MEDIA_TYPE_ORIGINAL_MEDIA_TYPE_UNSPECIFIED = 'ORIGINAL_MEDIA_TYPE_UNSPECIFIED';
  /**
   * The speech data is an audio recording.
   */
  public const ORIGINAL_MEDIA_TYPE_AUDIO = 'AUDIO';
  /**
   * The speech data originally recorded on a video.
   */
  public const ORIGINAL_MEDIA_TYPE_VIDEO = 'VIDEO';
  /**
   * The recording device is unknown.
   */
  public const RECORDING_DEVICE_TYPE_RECORDING_DEVICE_TYPE_UNSPECIFIED = 'RECORDING_DEVICE_TYPE_UNSPECIFIED';
  /**
   * Speech was recorded on a smartphone.
   */
  public const RECORDING_DEVICE_TYPE_SMARTPHONE = 'SMARTPHONE';
  /**
   * Speech was recorded using a personal computer or tablet.
   */
  public const RECORDING_DEVICE_TYPE_PC = 'PC';
  /**
   * Speech was recorded over a phone line.
   */
  public const RECORDING_DEVICE_TYPE_PHONE_LINE = 'PHONE_LINE';
  /**
   * Speech was recorded in a vehicle.
   */
  public const RECORDING_DEVICE_TYPE_VEHICLE = 'VEHICLE';
  /**
   * Speech was recorded outdoors.
   */
  public const RECORDING_DEVICE_TYPE_OTHER_OUTDOOR_DEVICE = 'OTHER_OUTDOOR_DEVICE';
  /**
   * Speech was recorded indoors.
   */
  public const RECORDING_DEVICE_TYPE_OTHER_INDOOR_DEVICE = 'OTHER_INDOOR_DEVICE';
  /**
   * Description of the content. Eg. "Recordings of federal supreme court
   * hearings from 2012".
   *
   * @var string
   */
  public $audioTopic;
  /**
   * The industry vertical to which this speech recognition request most closely
   * applies. This is most indicative of the topics contained in the audio. Use
   * the 6-digit NAICS code to identify the industry vertical - see
   * https://www.naics.com/search/.
   *
   * @var string
   */
  public $industryNaicsCodeOfAudio;
  /**
   * The use case most closely describing the audio content to be recognized.
   *
   * @var string
   */
  public $interactionType;
  /**
   * The audio type that most closely describes the audio being recognized.
   *
   * @var string
   */
  public $microphoneDistance;
  /**
   * The original media the speech was recorded on.
   *
   * @var string
   */
  public $originalMediaType;
  /**
   * Mime type of the original audio file. For example `audio/m4a`,
   * `audio/x-alaw-basic`, `audio/mp3`, `audio/3gpp`. A list of possible audio
   * mime types is maintained at http://www.iana.org/assignments/media-
   * types/media-types.xhtml#audio
   *
   * @var string
   */
  public $originalMimeType;
  /**
   * The device used to make the recording. Examples 'Nexus 5X' or 'Polycom
   * SoundStation IP 6000' or 'POTS' or 'VoIP' or 'Cardioid Microphone'.
   *
   * @var string
   */
  public $recordingDeviceName;
  /**
   * The type of device the speech was recorded with.
   *
   * @var string
   */
  public $recordingDeviceType;

  /**
   * Description of the content. Eg. "Recordings of federal supreme court
   * hearings from 2012".
   *
   * @param string $audioTopic
   */
  public function setAudioTopic($audioTopic)
  {
    $this->audioTopic = $audioTopic;
  }
  /**
   * @return string
   */
  public function getAudioTopic()
  {
    return $this->audioTopic;
  }
  /**
   * The industry vertical to which this speech recognition request most closely
   * applies. This is most indicative of the topics contained in the audio. Use
   * the 6-digit NAICS code to identify the industry vertical - see
   * https://www.naics.com/search/.
   *
   * @param string $industryNaicsCodeOfAudio
   */
  public function setIndustryNaicsCodeOfAudio($industryNaicsCodeOfAudio)
  {
    $this->industryNaicsCodeOfAudio = $industryNaicsCodeOfAudio;
  }
  /**
   * @return string
   */
  public function getIndustryNaicsCodeOfAudio()
  {
    return $this->industryNaicsCodeOfAudio;
  }
  /**
   * The use case most closely describing the audio content to be recognized.
   *
   * Accepted values: INTERACTION_TYPE_UNSPECIFIED, DISCUSSION, PRESENTATION,
   * PHONE_CALL, VOICEMAIL, PROFESSIONALLY_PRODUCED, VOICE_SEARCH,
   * VOICE_COMMAND, DICTATION
   *
   * @param self::INTERACTION_TYPE_* $interactionType
   */
  public function setInteractionType($interactionType)
  {
    $this->interactionType = $interactionType;
  }
  /**
   * @return self::INTERACTION_TYPE_*
   */
  public function getInteractionType()
  {
    return $this->interactionType;
  }
  /**
   * The audio type that most closely describes the audio being recognized.
   *
   * Accepted values: MICROPHONE_DISTANCE_UNSPECIFIED, NEARFIELD, MIDFIELD,
   * FARFIELD
   *
   * @param self::MICROPHONE_DISTANCE_* $microphoneDistance
   */
  public function setMicrophoneDistance($microphoneDistance)
  {
    $this->microphoneDistance = $microphoneDistance;
  }
  /**
   * @return self::MICROPHONE_DISTANCE_*
   */
  public function getMicrophoneDistance()
  {
    return $this->microphoneDistance;
  }
  /**
   * The original media the speech was recorded on.
   *
   * Accepted values: ORIGINAL_MEDIA_TYPE_UNSPECIFIED, AUDIO, VIDEO
   *
   * @param self::ORIGINAL_MEDIA_TYPE_* $originalMediaType
   */
  public function setOriginalMediaType($originalMediaType)
  {
    $this->originalMediaType = $originalMediaType;
  }
  /**
   * @return self::ORIGINAL_MEDIA_TYPE_*
   */
  public function getOriginalMediaType()
  {
    return $this->originalMediaType;
  }
  /**
   * Mime type of the original audio file. For example `audio/m4a`,
   * `audio/x-alaw-basic`, `audio/mp3`, `audio/3gpp`. A list of possible audio
   * mime types is maintained at http://www.iana.org/assignments/media-
   * types/media-types.xhtml#audio
   *
   * @param string $originalMimeType
   */
  public function setOriginalMimeType($originalMimeType)
  {
    $this->originalMimeType = $originalMimeType;
  }
  /**
   * @return string
   */
  public function getOriginalMimeType()
  {
    return $this->originalMimeType;
  }
  /**
   * The device used to make the recording. Examples 'Nexus 5X' or 'Polycom
   * SoundStation IP 6000' or 'POTS' or 'VoIP' or 'Cardioid Microphone'.
   *
   * @param string $recordingDeviceName
   */
  public function setRecordingDeviceName($recordingDeviceName)
  {
    $this->recordingDeviceName = $recordingDeviceName;
  }
  /**
   * @return string
   */
  public function getRecordingDeviceName()
  {
    return $this->recordingDeviceName;
  }
  /**
   * The type of device the speech was recorded with.
   *
   * Accepted values: RECORDING_DEVICE_TYPE_UNSPECIFIED, SMARTPHONE, PC,
   * PHONE_LINE, VEHICLE, OTHER_OUTDOOR_DEVICE, OTHER_INDOOR_DEVICE
   *
   * @param self::RECORDING_DEVICE_TYPE_* $recordingDeviceType
   */
  public function setRecordingDeviceType($recordingDeviceType)
  {
    $this->recordingDeviceType = $recordingDeviceType;
  }
  /**
   * @return self::RECORDING_DEVICE_TYPE_*
   */
  public function getRecordingDeviceType()
  {
    return $this->recordingDeviceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RecognitionMetadata::class, 'Google_Service_Speech_RecognitionMetadata');
