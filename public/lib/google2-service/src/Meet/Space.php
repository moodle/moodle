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

class Space extends \Google\Model
{
  protected $activeConferenceType = ActiveConference::class;
  protected $activeConferenceDataType = '';
  protected $configType = SpaceConfig::class;
  protected $configDataType = '';
  /**
   * Output only. Type friendly unique string used to join the meeting. Format:
   * `[a-z]+-[a-z]+-[a-z]+`. For example, `abc-mnop-xyz`. The maximum length is
   * 128 characters. Can only be used as an alias of the space name to get the
   * space.
   *
   * @var string
   */
  public $meetingCode;
  /**
   * Output only. URI used to join meetings consisting of
   * `https://meet.google.com/` followed by the `meeting_code`. For example,
   * `https://meet.google.com/abc-mnop-xyz`.
   *
   * @var string
   */
  public $meetingUri;
  /**
   * Immutable. Resource name of the space. Format: `spaces/{space}`. `{space}`
   * is the resource identifier for the space. It's a unique, server-generated
   * ID and is case sensitive. For example, `jQCFfuBOdN5z`. For more
   * information, see [How Meet identifies a meeting
   * space](https://developers.google.com/workspace/meet/api/guides/meeting-
   * spaces#identify-meeting-space).
   *
   * @var string
   */
  public $name;

  /**
   * Active conference, if it exists.
   *
   * @param ActiveConference $activeConference
   */
  public function setActiveConference(ActiveConference $activeConference)
  {
    $this->activeConference = $activeConference;
  }
  /**
   * @return ActiveConference
   */
  public function getActiveConference()
  {
    return $this->activeConference;
  }
  /**
   * Configuration pertaining to the meeting space.
   *
   * @param SpaceConfig $config
   */
  public function setConfig(SpaceConfig $config)
  {
    $this->config = $config;
  }
  /**
   * @return SpaceConfig
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * Output only. Type friendly unique string used to join the meeting. Format:
   * `[a-z]+-[a-z]+-[a-z]+`. For example, `abc-mnop-xyz`. The maximum length is
   * 128 characters. Can only be used as an alias of the space name to get the
   * space.
   *
   * @param string $meetingCode
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
   * Output only. URI used to join meetings consisting of
   * `https://meet.google.com/` followed by the `meeting_code`. For example,
   * `https://meet.google.com/abc-mnop-xyz`.
   *
   * @param string $meetingUri
   */
  public function setMeetingUri($meetingUri)
  {
    $this->meetingUri = $meetingUri;
  }
  /**
   * @return string
   */
  public function getMeetingUri()
  {
    return $this->meetingUri;
  }
  /**
   * Immutable. Resource name of the space. Format: `spaces/{space}`. `{space}`
   * is the resource identifier for the space. It's a unique, server-generated
   * ID and is case sensitive. For example, `jQCFfuBOdN5z`. For more
   * information, see [How Meet identifies a meeting
   * space](https://developers.google.com/workspace/meet/api/guides/meeting-
   * spaces#identify-meeting-space).
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Space::class, 'Google_Service_Meet_Space');
