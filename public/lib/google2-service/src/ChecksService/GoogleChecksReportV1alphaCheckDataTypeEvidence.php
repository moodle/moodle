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

namespace Google\Service\ChecksService;

class GoogleChecksReportV1alphaCheckDataTypeEvidence extends \Google\Model
{
  /**
   * Not specified.
   */
  public const DATA_TYPE_DATA_TYPE_UNSPECIFIED = 'DATA_TYPE_UNSPECIFIED';
  /**
   * User or device physical location to an area greater than or equal to 3
   * square kilometers, such as the city a user is in, or location provided by
   * Android's ACCESS_COARSE_LOCATION permission.
   */
  public const DATA_TYPE_DATA_TYPE_APPROXIMATE_LOCATION = 'DATA_TYPE_APPROXIMATE_LOCATION';
  /**
   * User or device physical location within an area less than 3 square
   * kilometers, such as location provided by Android's ACCESS_FINE_LOCATION
   * permission.
   */
  public const DATA_TYPE_DATA_TYPE_PRECISE_LOCATION = 'DATA_TYPE_PRECISE_LOCATION';
  /**
   * How a user refers to themselves, such as their first or last name, or
   * nickname.
   */
  public const DATA_TYPE_DATA_TYPE_PERSONAL_NAME = 'DATA_TYPE_PERSONAL_NAME';
  /**
   * A user's email address.
   */
  public const DATA_TYPE_DATA_TYPE_EMAIL_ADDRESS = 'DATA_TYPE_EMAIL_ADDRESS';
  /**
   * Identifiers that relate to an identifiable person. For example, an account
   * ID, account number, or account name.
   */
  public const DATA_TYPE_DATA_TYPE_USER_IDS = 'DATA_TYPE_USER_IDS';
  /**
   * A user's address, such as a mailing or home address.
   */
  public const DATA_TYPE_DATA_TYPE_PHYSICAL_ADDRESS = 'DATA_TYPE_PHYSICAL_ADDRESS';
  /**
   * A user's phone number.
   */
  public const DATA_TYPE_DATA_TYPE_PHONE_NUMBER = 'DATA_TYPE_PHONE_NUMBER';
  /**
   * Information about a user's race or ethnicity.
   */
  public const DATA_TYPE_DATA_TYPE_RACE_AND_ETHNICITY = 'DATA_TYPE_RACE_AND_ETHNICITY';
  /**
   * Information about a user's political or religious beliefs.
   */
  public const DATA_TYPE_DATA_TYPE_POLITICAL_OR_RELIGIOUS_BELIEFS = 'DATA_TYPE_POLITICAL_OR_RELIGIOUS_BELIEFS';
  /**
   * Information about a user's sexual orientation.
   */
  public const DATA_TYPE_DATA_TYPE_SEXUAL_ORIENTATION = 'DATA_TYPE_SEXUAL_ORIENTATION';
  /**
   * Any other personal information such as date of birth, gender identity,
   * veteran status, etc.
   */
  public const DATA_TYPE_DATA_TYPE_OTHER_PERSONAL_INFO = 'DATA_TYPE_OTHER_PERSONAL_INFO';
  /**
   * Information about a user's financial accounts such as credit card number.
   */
  public const DATA_TYPE_DATA_TYPE_PAYMENT_INFO = 'DATA_TYPE_PAYMENT_INFO';
  /**
   * Information about purchases or transactions a user has made.
   */
  public const DATA_TYPE_DATA_TYPE_PURCHASE_HISTORY = 'DATA_TYPE_PURCHASE_HISTORY';
  /**
   * Information about a user's credit score.
   */
  public const DATA_TYPE_DATA_TYPE_CREDIT_SCORE = 'DATA_TYPE_CREDIT_SCORE';
  /**
   * Any other financial information such as user salary or debts.
   */
  public const DATA_TYPE_DATA_TYPE_OTHER_FINANCIAL_INFO = 'DATA_TYPE_OTHER_FINANCIAL_INFO';
  /**
   * Information about a user's health, such as medical records or symptoms.
   */
  public const DATA_TYPE_DATA_TYPE_HEALTH_INFO = 'DATA_TYPE_HEALTH_INFO';
  /**
   * Information about a user's fitness, such as exercise or other physical
   * activity.
   */
  public const DATA_TYPE_DATA_TYPE_FITNESS_INFO = 'DATA_TYPE_FITNESS_INFO';
  /**
   * A user's emails including the email subject line, sender, recipients, and
   * the content of the email.
   */
  public const DATA_TYPE_DATA_TYPE_EMAILS = 'DATA_TYPE_EMAILS';
  /**
   * A user's text messages including the sender, recipients, and the content of
   * the message.
   */
  public const DATA_TYPE_DATA_TYPE_TEXT_MESSAGES = 'DATA_TYPE_TEXT_MESSAGES';
  /**
   * Any other types of messages. For example, instant messages or chat content.
   */
  public const DATA_TYPE_DATA_TYPE_OTHER_IN_APP_MESSAGES = 'DATA_TYPE_OTHER_IN_APP_MESSAGES';
  /**
   * A user's photos.
   */
  public const DATA_TYPE_DATA_TYPE_PHOTOS = 'DATA_TYPE_PHOTOS';
  /**
   * A user's videos.
   */
  public const DATA_TYPE_DATA_TYPE_VIDEOS = 'DATA_TYPE_VIDEOS';
  /**
   * A user's voice such as a voicemail or a sound recording.
   */
  public const DATA_TYPE_DATA_TYPE_VOICE_OR_SOUND_RECORDINGS = 'DATA_TYPE_VOICE_OR_SOUND_RECORDINGS';
  /**
   * A user's music files.
   */
  public const DATA_TYPE_DATA_TYPE_MUSIC_FILES = 'DATA_TYPE_MUSIC_FILES';
  /**
   * Any other user-created or user-provided audio files.
   */
  public const DATA_TYPE_DATA_TYPE_OTHER_AUDIO_FILES = 'DATA_TYPE_OTHER_AUDIO_FILES';
  /**
   * A user's files or documents, or information about their files or documents
   * such as file names.
   */
  public const DATA_TYPE_DATA_TYPE_FILES_AND_DOCS = 'DATA_TYPE_FILES_AND_DOCS';
  /**
   * Information from a user's calendar such as events, event notes, and
   * attendees.
   */
  public const DATA_TYPE_DATA_TYPE_CALENDAR_EVENTS = 'DATA_TYPE_CALENDAR_EVENTS';
  /**
   * Information about the user’s contacts such as contact names, message
   * history, and social graph information like usernames, contact recency,
   * contact frequency, interaction duration and call history.
   */
  public const DATA_TYPE_DATA_TYPE_CONTACTS = 'DATA_TYPE_CONTACTS';
  /**
   * Information about how a user interacts with your app, such as the number of
   * page views or taps.
   */
  public const DATA_TYPE_DATA_TYPE_APP_INTERACTIONS = 'DATA_TYPE_APP_INTERACTIONS';
  /**
   * Information about what a user has searched for in your app.
   */
  public const DATA_TYPE_DATA_TYPE_IN_APP_SEARCH_HISTORY = 'DATA_TYPE_IN_APP_SEARCH_HISTORY';
  /**
   * Inventory of apps or packages installed on the user’s device.
   */
  public const DATA_TYPE_DATA_TYPE_INSTALLED_APPS = 'DATA_TYPE_INSTALLED_APPS';
  /**
   * Any other user-generated content not listed here, or in any other section.
   * For example, user bios, notes, or open-ended responses.
   */
  public const DATA_TYPE_DATA_TYPE_OTHER_USER_GENERATED_CONTENT = 'DATA_TYPE_OTHER_USER_GENERATED_CONTENT';
  /**
   * Any other user activity or actions in-app not listed here such as gameplay,
   * likes, and dialog options.
   */
  public const DATA_TYPE_DATA_TYPE_OTHER_ACTIONS = 'DATA_TYPE_OTHER_ACTIONS';
  /**
   * Information about the websites a user has visited.
   */
  public const DATA_TYPE_DATA_TYPE_WEB_BROWSING_HISTORY = 'DATA_TYPE_WEB_BROWSING_HISTORY';
  /**
   * Crash log data from your app. For example, the number of times your app has
   * crashed, stack traces, or other information directly related to a crash.
   */
  public const DATA_TYPE_DATA_TYPE_CRASH_LOGS = 'DATA_TYPE_CRASH_LOGS';
  /**
   * Information about the performance of your app. For example battery life,
   * loading time, latency, framerate, or any technical diagnostics.
   */
  public const DATA_TYPE_DATA_TYPE_PERFORMANCE_DIAGNOSTICS = 'DATA_TYPE_PERFORMANCE_DIAGNOSTICS';
  /**
   * Any other app performance data not listed here.
   */
  public const DATA_TYPE_DATA_TYPE_OTHER_APP_PERFORMANCE_DATA = 'DATA_TYPE_OTHER_APP_PERFORMANCE_DATA';
  /**
   * Identifiers that relate to an individual device, browser or app. For
   * example, an IMEI number, MAC address, Widevine Device ID, Firebase
   * installation ID, or advertising identifier.
   */
  public const DATA_TYPE_DATA_TYPE_DEVICE_OR_OTHER_IDS = 'DATA_TYPE_DEVICE_OR_OTHER_IDS';
  /**
   * The data type that was found in your app.
   *
   * @var string
   */
  public $dataType;
  protected $dataTypeEvidenceType = GoogleChecksReportV1alphaDataTypeEvidence::class;
  protected $dataTypeEvidenceDataType = '';

  /**
   * The data type that was found in your app.
   *
   * Accepted values: DATA_TYPE_UNSPECIFIED, DATA_TYPE_APPROXIMATE_LOCATION,
   * DATA_TYPE_PRECISE_LOCATION, DATA_TYPE_PERSONAL_NAME,
   * DATA_TYPE_EMAIL_ADDRESS, DATA_TYPE_USER_IDS, DATA_TYPE_PHYSICAL_ADDRESS,
   * DATA_TYPE_PHONE_NUMBER, DATA_TYPE_RACE_AND_ETHNICITY,
   * DATA_TYPE_POLITICAL_OR_RELIGIOUS_BELIEFS, DATA_TYPE_SEXUAL_ORIENTATION,
   * DATA_TYPE_OTHER_PERSONAL_INFO, DATA_TYPE_PAYMENT_INFO,
   * DATA_TYPE_PURCHASE_HISTORY, DATA_TYPE_CREDIT_SCORE,
   * DATA_TYPE_OTHER_FINANCIAL_INFO, DATA_TYPE_HEALTH_INFO,
   * DATA_TYPE_FITNESS_INFO, DATA_TYPE_EMAILS, DATA_TYPE_TEXT_MESSAGES,
   * DATA_TYPE_OTHER_IN_APP_MESSAGES, DATA_TYPE_PHOTOS, DATA_TYPE_VIDEOS,
   * DATA_TYPE_VOICE_OR_SOUND_RECORDINGS, DATA_TYPE_MUSIC_FILES,
   * DATA_TYPE_OTHER_AUDIO_FILES, DATA_TYPE_FILES_AND_DOCS,
   * DATA_TYPE_CALENDAR_EVENTS, DATA_TYPE_CONTACTS, DATA_TYPE_APP_INTERACTIONS,
   * DATA_TYPE_IN_APP_SEARCH_HISTORY, DATA_TYPE_INSTALLED_APPS,
   * DATA_TYPE_OTHER_USER_GENERATED_CONTENT, DATA_TYPE_OTHER_ACTIONS,
   * DATA_TYPE_WEB_BROWSING_HISTORY, DATA_TYPE_CRASH_LOGS,
   * DATA_TYPE_PERFORMANCE_DIAGNOSTICS, DATA_TYPE_OTHER_APP_PERFORMANCE_DATA,
   * DATA_TYPE_DEVICE_OR_OTHER_IDS
   *
   * @param self::DATA_TYPE_* $dataType
   */
  public function setDataType($dataType)
  {
    $this->dataType = $dataType;
  }
  /**
   * @return self::DATA_TYPE_*
   */
  public function getDataType()
  {
    return $this->dataType;
  }
  /**
   * Evidence collected about the data type.
   *
   * @param GoogleChecksReportV1alphaDataTypeEvidence $dataTypeEvidence
   */
  public function setDataTypeEvidence(GoogleChecksReportV1alphaDataTypeEvidence $dataTypeEvidence)
  {
    $this->dataTypeEvidence = $dataTypeEvidence;
  }
  /**
   * @return GoogleChecksReportV1alphaDataTypeEvidence
   */
  public function getDataTypeEvidence()
  {
    return $this->dataTypeEvidence;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChecksReportV1alphaCheckDataTypeEvidence::class, 'Google_Service_ChecksService_GoogleChecksReportV1alphaCheckDataTypeEvidence');
