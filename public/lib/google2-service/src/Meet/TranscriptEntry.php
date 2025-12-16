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

namespace Google\Service\Meet;

class TranscriptEntry extends \Google\Model
{
  /**
   * Output only. Timestamp when the transcript entry ended.
   *
   * @var string
   */
  public $endTime;
  /**
   * Output only. Language of spoken text, such as "en-US". IETF BCP 47 syntax
   * (https://tools.ietf.org/html/bcp47)
   *
   * @var string
   */
  public $languageCode;
  /**
   * Output only. Resource name of the entry. Format: "conferenceRecords/{confer
   * ence_record}/transcripts/{transcript}/entries/{entry}"
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Refers to the participant who speaks.
   *
   * @var string
   */
  public $participant;
  /**
   * Output only. Timestamp when the transcript entry started.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. The transcribed text of the participant's voice, at maximum
   * 10K words. Note that the limit is subject to change.
   *
   * @var string
   */
  public $text;

  /**
   * Output only. Timestamp when the transcript entry ended.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Output only. Language of spoken text, such as "en-US". IETF BCP 47 syntax
   * (https://tools.ietf.org/html/bcp47)
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
   * Output only. Resource name of the entry. Format: "conferenceRecords/{confer
   * ence_record}/transcripts/{transcript}/entries/{entry}"
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
   * Output only. Refers to the participant who speaks.
   *
   * @param string $participant
   */
  public function setParticipant($participant)
  {
    $this->participant = $participant;
  }
  /**
   * @return string
   */
  public function getParticipant()
  {
    return $this->participant;
  }
  /**
   * Output only. Timestamp when the transcript entry started.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Output only. The transcribed text of the participant's voice, at maximum
   * 10K words. Note that the limit is subject to change.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TranscriptEntry::class, 'Google_Service_Meet_TranscriptEntry');
