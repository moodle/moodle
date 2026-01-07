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

namespace Google\Service\Vault;

class ExportOptions extends \Google\Model
{
  /**
   * The region is unspecified. Defaults to ANY.
   */
  public const REGION_EXPORT_REGION_UNSPECIFIED = 'EXPORT_REGION_UNSPECIFIED';
  /**
   * Any region.
   */
  public const REGION_ANY = 'ANY';
  /**
   * United States region.
   */
  public const REGION_US = 'US';
  /**
   * Europe region.
   */
  public const REGION_EUROPE = 'EUROPE';
  protected $calendarOptionsType = CalendarExportOptions::class;
  protected $calendarOptionsDataType = '';
  protected $driveOptionsType = DriveExportOptions::class;
  protected $driveOptionsDataType = '';
  protected $geminiOptionsType = GeminiExportOptions::class;
  protected $geminiOptionsDataType = '';
  protected $groupsOptionsType = GroupsExportOptions::class;
  protected $groupsOptionsDataType = '';
  protected $hangoutsChatOptionsType = HangoutsChatExportOptions::class;
  protected $hangoutsChatOptionsDataType = '';
  protected $mailOptionsType = MailExportOptions::class;
  protected $mailOptionsDataType = '';
  /**
   * The requested data region for the export.
   *
   * @var string
   */
  public $region;
  protected $voiceOptionsType = VoiceExportOptions::class;
  protected $voiceOptionsDataType = '';

  /**
   * Option available for Calendar export.
   *
   * @param CalendarExportOptions $calendarOptions
   */
  public function setCalendarOptions(CalendarExportOptions $calendarOptions)
  {
    $this->calendarOptions = $calendarOptions;
  }
  /**
   * @return CalendarExportOptions
   */
  public function getCalendarOptions()
  {
    return $this->calendarOptions;
  }
  /**
   * Options for Drive exports.
   *
   * @param DriveExportOptions $driveOptions
   */
  public function setDriveOptions(DriveExportOptions $driveOptions)
  {
    $this->driveOptions = $driveOptions;
  }
  /**
   * @return DriveExportOptions
   */
  public function getDriveOptions()
  {
    return $this->driveOptions;
  }
  /**
   * Option available for Gemini export.
   *
   * @param GeminiExportOptions $geminiOptions
   */
  public function setGeminiOptions(GeminiExportOptions $geminiOptions)
  {
    $this->geminiOptions = $geminiOptions;
  }
  /**
   * @return GeminiExportOptions
   */
  public function getGeminiOptions()
  {
    return $this->geminiOptions;
  }
  /**
   * Options for Groups exports.
   *
   * @param GroupsExportOptions $groupsOptions
   */
  public function setGroupsOptions(GroupsExportOptions $groupsOptions)
  {
    $this->groupsOptions = $groupsOptions;
  }
  /**
   * @return GroupsExportOptions
   */
  public function getGroupsOptions()
  {
    return $this->groupsOptions;
  }
  /**
   * Options for Chat exports.
   *
   * @param HangoutsChatExportOptions $hangoutsChatOptions
   */
  public function setHangoutsChatOptions(HangoutsChatExportOptions $hangoutsChatOptions)
  {
    $this->hangoutsChatOptions = $hangoutsChatOptions;
  }
  /**
   * @return HangoutsChatExportOptions
   */
  public function getHangoutsChatOptions()
  {
    return $this->hangoutsChatOptions;
  }
  /**
   * Options for Gmail exports.
   *
   * @param MailExportOptions $mailOptions
   */
  public function setMailOptions(MailExportOptions $mailOptions)
  {
    $this->mailOptions = $mailOptions;
  }
  /**
   * @return MailExportOptions
   */
  public function getMailOptions()
  {
    return $this->mailOptions;
  }
  /**
   * The requested data region for the export.
   *
   * Accepted values: EXPORT_REGION_UNSPECIFIED, ANY, US, EUROPE
   *
   * @param self::REGION_* $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return self::REGION_*
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * Options for Voice exports.
   *
   * @param VoiceExportOptions $voiceOptions
   */
  public function setVoiceOptions(VoiceExportOptions $voiceOptions)
  {
    $this->voiceOptions = $voiceOptions;
  }
  /**
   * @return VoiceExportOptions
   */
  public function getVoiceOptions()
  {
    return $this->voiceOptions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExportOptions::class, 'Google_Service_Vault_ExportOptions');
