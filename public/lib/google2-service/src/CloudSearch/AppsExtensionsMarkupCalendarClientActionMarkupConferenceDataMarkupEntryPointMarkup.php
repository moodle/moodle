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

namespace Google\Service\CloudSearch;

class AppsExtensionsMarkupCalendarClientActionMarkupConferenceDataMarkupEntryPointMarkup extends \Google\Collection
{
  protected $collection_key = 'features';
  /**
   * @var string
   */
  public $accessCode;
  /**
   * @var string[]
   */
  public $features;
  /**
   * @var string
   */
  public $label;
  /**
   * @var string
   */
  public $meetingCode;
  /**
   * @var string
   */
  public $passcode;
  /**
   * @var string
   */
  public $password;
  /**
   * @var string
   */
  public $pin;
  /**
   * @var string
   */
  public $regionCode;
  /**
   * @var string
   */
  public $type;
  /**
   * @var string
   */
  public $uri;

  /**
   * @param string
   */
  public function setAccessCode($accessCode)
  {
    $this->accessCode = $accessCode;
  }
  /**
   * @return string
   */
  public function getAccessCode()
  {
    return $this->accessCode;
  }
  /**
   * @param string[]
   */
  public function setFeatures($features)
  {
    $this->features = $features;
  }
  /**
   * @return string[]
   */
  public function getFeatures()
  {
    return $this->features;
  }
  /**
   * @param string
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }
  /**
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }
  /**
   * @param string
   */
  public function setMeetingCode($meetingCode)
  {
    $this->meetingCode = $meetingCode;
  }
  /**
   * @return string
   */
  public function getMeetingCode()
  {
    return $this->meetingCode;
  }
  /**
   * @param string
   */
  public function setPasscode($passcode)
  {
    $this->passcode = $passcode;
  }
  /**
   * @return string
   */
  public function getPasscode()
  {
    return $this->passcode;
  }
  /**
   * @param string
   */
  public function setPassword($password)
  {
    $this->password = $password;
  }
  /**
   * @return string
   */
  public function getPassword()
  {
    return $this->password;
  }
  /**
   * @param string
   */
  public function setPin($pin)
  {
    $this->pin = $pin;
  }
  /**
   * @return string
   */
  public function getPin()
  {
    return $this->pin;
  }
  /**
   * @param string
   */
  public function setRegionCode($regionCode)
  {
    $this->regionCode = $regionCode;
  }
  /**
   * @return string
   */
  public function getRegionCode()
  {
    return $this->regionCode;
  }
  /**
   * @param string
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * @param string
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppsExtensionsMarkupCalendarClientActionMarkupConferenceDataMarkupEntryPointMarkup::class, 'Google_Service_CloudSearch_AppsExtensionsMarkupCalendarClientActionMarkupConferenceDataMarkupEntryPointMarkup');
