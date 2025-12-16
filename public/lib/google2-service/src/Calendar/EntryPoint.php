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

namespace Google\Service\Calendar;

class EntryPoint extends \Google\Collection
{
  protected $collection_key = 'entryPointFeatures';
  /**
   * The access code to access the conference. The maximum length is 128
   * characters. When creating new conference data, populate only the subset of
   * {meetingCode, accessCode, passcode, password, pin} fields that match the
   * terminology that the conference provider uses. Only the populated fields
   * should be displayed. Optional.
   *
   * @var string
   */
  public $accessCode;
  /**
   * Features of the entry point, such as being toll or toll-free. One entry
   * point can have multiple features. However, toll and toll-free cannot be
   * both set on the same entry point.
   *
   * @var string[]
   */
  public $entryPointFeatures;
  /**
   * The type of the conference entry point. Possible values are:   - "video" -
   * joining a conference over HTTP. A conference can have zero or one video
   * entry point. - "phone" - joining a conference by dialing a phone number. A
   * conference can have zero or more phone entry points. - "sip" - joining a
   * conference over SIP. A conference can have zero or one sip entry point. -
   * "more" - further conference joining instructions, for example additional
   * phone numbers. A conference can have zero or one more entry point. A
   * conference with only a more entry point is not a valid conference.
   *
   * @var string
   */
  public $entryPointType;
  /**
   * The label for the URI. Visible to end users. Not localized. The maximum
   * length is 512 characters. Examples:   - for video: meet.google.com/aaa-
   * bbbb-ccc - for phone: +1 123 268 2601 - for sip: 12345678@altostrat.com -
   * for more: should not be filled   Optional.
   *
   * @var string
   */
  public $label;
  /**
   * The meeting code to access the conference. The maximum length is 128
   * characters. When creating new conference data, populate only the subset of
   * {meetingCode, accessCode, passcode, password, pin} fields that match the
   * terminology that the conference provider uses. Only the populated fields
   * should be displayed. Optional.
   *
   * @var string
   */
  public $meetingCode;
  /**
   * The passcode to access the conference. The maximum length is 128
   * characters. When creating new conference data, populate only the subset of
   * {meetingCode, accessCode, passcode, password, pin} fields that match the
   * terminology that the conference provider uses. Only the populated fields
   * should be displayed.
   *
   * @var string
   */
  public $passcode;
  /**
   * The password to access the conference. The maximum length is 128
   * characters. When creating new conference data, populate only the subset of
   * {meetingCode, accessCode, passcode, password, pin} fields that match the
   * terminology that the conference provider uses. Only the populated fields
   * should be displayed. Optional.
   *
   * @var string
   */
  public $password;
  /**
   * The PIN to access the conference. The maximum length is 128 characters.
   * When creating new conference data, populate only the subset of
   * {meetingCode, accessCode, passcode, password, pin} fields that match the
   * terminology that the conference provider uses. Only the populated fields
   * should be displayed. Optional.
   *
   * @var string
   */
  public $pin;
  /**
   * The CLDR/ISO 3166 region code for the country associated with this phone
   * access. Example: "SE" for Sweden. Calendar backend will populate this field
   * only for EntryPointType.PHONE.
   *
   * @var string
   */
  public $regionCode;
  /**
   * The URI of the entry point. The maximum length is 1300 characters. Format:
   * - for video, http: or https: schema is required. - for phone, tel: schema
   * is required. The URI should include the entire dial sequence (e.g.,
   * tel:+12345678900,,,123456789;1234). - for sip, sip: schema is required,
   * e.g., sip:12345678@myprovider.com. - for more, http: or https: schema is
   * required.
   *
   * @var string
   */
  public $uri;

  /**
   * The access code to access the conference. The maximum length is 128
   * characters. When creating new conference data, populate only the subset of
   * {meetingCode, accessCode, passcode, password, pin} fields that match the
   * terminology that the conference provider uses. Only the populated fields
   * should be displayed. Optional.
   *
   * @param string $accessCode
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
   * Features of the entry point, such as being toll or toll-free. One entry
   * point can have multiple features. However, toll and toll-free cannot be
   * both set on the same entry point.
   *
   * @param string[] $entryPointFeatures
   */
  public function setEntryPointFeatures($entryPointFeatures)
  {
    $this->entryPointFeatures = $entryPointFeatures;
  }
  /**
   * @return string[]
   */
  public function getEntryPointFeatures()
  {
    return $this->entryPointFeatures;
  }
  /**
   * The type of the conference entry point. Possible values are:   - "video" -
   * joining a conference over HTTP. A conference can have zero or one video
   * entry point. - "phone" - joining a conference by dialing a phone number. A
   * conference can have zero or more phone entry points. - "sip" - joining a
   * conference over SIP. A conference can have zero or one sip entry point. -
   * "more" - further conference joining instructions, for example additional
   * phone numbers. A conference can have zero or one more entry point. A
   * conference with only a more entry point is not a valid conference.
   *
   * @param string $entryPointType
   */
  public function setEntryPointType($entryPointType)
  {
    $this->entryPointType = $entryPointType;
  }
  /**
   * @return string
   */
  public function getEntryPointType()
  {
    return $this->entryPointType;
  }
  /**
   * The label for the URI. Visible to end users. Not localized. The maximum
   * length is 512 characters. Examples:   - for video: meet.google.com/aaa-
   * bbbb-ccc - for phone: +1 123 268 2601 - for sip: 12345678@altostrat.com -
   * for more: should not be filled   Optional.
   *
   * @param string $label
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
   * The meeting code to access the conference. The maximum length is 128
   * characters. When creating new conference data, populate only the subset of
   * {meetingCode, accessCode, passcode, password, pin} fields that match the
   * terminology that the conference provider uses. Only the populated fields
   * should be displayed. Optional.
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
   * The passcode to access the conference. The maximum length is 128
   * characters. When creating new conference data, populate only the subset of
   * {meetingCode, accessCode, passcode, password, pin} fields that match the
   * terminology that the conference provider uses. Only the populated fields
   * should be displayed.
   *
   * @param string $passcode
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
   * The password to access the conference. The maximum length is 128
   * characters. When creating new conference data, populate only the subset of
   * {meetingCode, accessCode, passcode, password, pin} fields that match the
   * terminology that the conference provider uses. Only the populated fields
   * should be displayed. Optional.
   *
   * @param string $password
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
   * The PIN to access the conference. The maximum length is 128 characters.
   * When creating new conference data, populate only the subset of
   * {meetingCode, accessCode, passcode, password, pin} fields that match the
   * terminology that the conference provider uses. Only the populated fields
   * should be displayed. Optional.
   *
   * @param string $pin
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
   * The CLDR/ISO 3166 region code for the country associated with this phone
   * access. Example: "SE" for Sweden. Calendar backend will populate this field
   * only for EntryPointType.PHONE.
   *
   * @param string $regionCode
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
   * The URI of the entry point. The maximum length is 1300 characters. Format:
   * - for video, http: or https: schema is required. - for phone, tel: schema
   * is required. The URI should include the entire dial sequence (e.g.,
   * tel:+12345678900,,,123456789;1234). - for sip, sip: schema is required,
   * e.g., sip:12345678@myprovider.com. - for more, http: or https: schema is
   * required.
   *
   * @param string $uri
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
class_alias(EntryPoint::class, 'Google_Service_Calendar_EntryPoint');
