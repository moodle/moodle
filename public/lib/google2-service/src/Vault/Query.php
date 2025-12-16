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

class Query extends \Google\Model
{
  /**
   * No service specified.
   */
  public const CORPUS_CORPUS_TYPE_UNSPECIFIED = 'CORPUS_TYPE_UNSPECIFIED';
  /**
   * Drive, including Meet and Sites.
   */
  public const CORPUS_DRIVE = 'DRIVE';
  /**
   * For search, Gmail and classic Hangouts. For holds, Gmail only.
   */
  public const CORPUS_MAIL = 'MAIL';
  /**
   * Groups.
   */
  public const CORPUS_GROUPS = 'GROUPS';
  /**
   * For export, Google Chat only. For holds, Google Chat and classic Hangouts.
   */
  public const CORPUS_HANGOUTS_CHAT = 'HANGOUTS_CHAT';
  /**
   * Google Voice.
   */
  public const CORPUS_VOICE = 'VOICE';
  /**
   * Calendar.
   */
  public const CORPUS_CALENDAR = 'CALENDAR';
  /**
   * Gemini.
   */
  public const CORPUS_GEMINI = 'GEMINI';
  /**
   * No data source specified.
   */
  public const DATA_SCOPE_DATA_SCOPE_UNSPECIFIED = 'DATA_SCOPE_UNSPECIFIED';
  /**
   * All available data.
   */
  public const DATA_SCOPE_ALL_DATA = 'ALL_DATA';
  /**
   * Only data on hold.
   */
  public const DATA_SCOPE_HELD_DATA = 'HELD_DATA';
  /**
   * Only data not yet processed by Vault. (Gmail and Groups only)
   */
  public const DATA_SCOPE_UNPROCESSED_DATA = 'UNPROCESSED_DATA';
  /**
   * A search method must be specified or else it is rejected.
   */
  public const METHOD_SEARCH_METHOD_UNSPECIFIED = 'SEARCH_METHOD_UNSPECIFIED';
  /**
   * Search the data of the accounts specified in [AccountInfo](https://develope
   * rs.google.com/workspace/vault/reference/rest/v1/Query#accountinfo).
   */
  public const METHOD_ACCOUNT = 'ACCOUNT';
  /**
   * Search the data of all accounts in the organizational unit specified in [Or
   * gUnitInfo](https://developers.google.com/workspace/vault/reference/rest/v1/
   * Query#orgunitinfo).
   */
  public const METHOD_ORG_UNIT = 'ORG_UNIT';
  /**
   * Search the data in the Team Drive specified in **team_drive_info**.
   *
   * @deprecated
   */
  public const METHOD_TEAM_DRIVE = 'TEAM_DRIVE';
  /**
   * Search the data of all accounts in the organization. Supported only for
   * Gmail. When specified, you don't need to specify **AccountInfo** or
   * **OrgUnitInfo**.
   */
  public const METHOD_ENTIRE_ORG = 'ENTIRE_ORG';
  /**
   * Search messages in the Chat spaces specified in [HangoutsChatInfo](https://
   * developers.google.com/workspace/vault/reference/rest/v1/Query#hangoutschati
   * nfo).
   */
  public const METHOD_ROOM = 'ROOM';
  /**
   * Search for sites by the published site URLs specified in [SitesUrlInfo](htt
   * ps://developers.google.com/workspace/vault/reference/rest/v1/Query#sitesurl
   * info).
   */
  public const METHOD_SITES_URL = 'SITES_URL';
  /**
   * Search the files in the shared drives specified in [SharedDriveInfo](https:
   * //developers.google.com/workspace/vault/reference/rest/v1/Query#shareddrive
   * info).
   */
  public const METHOD_SHARED_DRIVE = 'SHARED_DRIVE';
  /**
   * Retrieve the documents specified in DriveDocumentInfo.
   */
  public const METHOD_DRIVE_DOCUMENT = 'DRIVE_DOCUMENT';
  /**
   * A search method must be specified or else it is rejected.
   */
  public const SEARCH_METHOD_SEARCH_METHOD_UNSPECIFIED = 'SEARCH_METHOD_UNSPECIFIED';
  /**
   * Search the data of the accounts specified in [AccountInfo](https://develope
   * rs.google.com/workspace/vault/reference/rest/v1/Query#accountinfo).
   */
  public const SEARCH_METHOD_ACCOUNT = 'ACCOUNT';
  /**
   * Search the data of all accounts in the organizational unit specified in [Or
   * gUnitInfo](https://developers.google.com/workspace/vault/reference/rest/v1/
   * Query#orgunitinfo).
   */
  public const SEARCH_METHOD_ORG_UNIT = 'ORG_UNIT';
  /**
   * Search the data in the Team Drive specified in **team_drive_info**.
   *
   * @deprecated
   */
  public const SEARCH_METHOD_TEAM_DRIVE = 'TEAM_DRIVE';
  /**
   * Search the data of all accounts in the organization. Supported only for
   * Gmail. When specified, you don't need to specify **AccountInfo** or
   * **OrgUnitInfo**.
   */
  public const SEARCH_METHOD_ENTIRE_ORG = 'ENTIRE_ORG';
  /**
   * Search messages in the Chat spaces specified in [HangoutsChatInfo](https://
   * developers.google.com/workspace/vault/reference/rest/v1/Query#hangoutschati
   * nfo).
   */
  public const SEARCH_METHOD_ROOM = 'ROOM';
  /**
   * Search for sites by the published site URLs specified in [SitesUrlInfo](htt
   * ps://developers.google.com/workspace/vault/reference/rest/v1/Query#sitesurl
   * info).
   */
  public const SEARCH_METHOD_SITES_URL = 'SITES_URL';
  /**
   * Search the files in the shared drives specified in [SharedDriveInfo](https:
   * //developers.google.com/workspace/vault/reference/rest/v1/Query#shareddrive
   * info).
   */
  public const SEARCH_METHOD_SHARED_DRIVE = 'SHARED_DRIVE';
  /**
   * Retrieve the documents specified in DriveDocumentInfo.
   */
  public const SEARCH_METHOD_DRIVE_DOCUMENT = 'DRIVE_DOCUMENT';
  protected $accountInfoType = AccountInfo::class;
  protected $accountInfoDataType = '';
  protected $calendarOptionsType = CalendarOptions::class;
  protected $calendarOptionsDataType = '';
  /**
   * The Google Workspace service to search.
   *
   * @var string
   */
  public $corpus;
  /**
   * The data source to search.
   *
   * @var string
   */
  public $dataScope;
  protected $driveDocumentInfoType = DriveDocumentInfo::class;
  protected $driveDocumentInfoDataType = '';
  protected $driveOptionsType = DriveOptions::class;
  protected $driveOptionsDataType = '';
  /**
   * The end time for the search query. Specify in GMT. The value is rounded to
   * 12 AM on the specified date.
   *
   * @var string
   */
  public $endTime;
  protected $geminiOptionsType = GeminiOptions::class;
  protected $geminiOptionsDataType = '';
  protected $hangoutsChatInfoType = HangoutsChatInfo::class;
  protected $hangoutsChatInfoDataType = '';
  protected $hangoutsChatOptionsType = HangoutsChatOptions::class;
  protected $hangoutsChatOptionsDataType = '';
  protected $mailOptionsType = MailOptions::class;
  protected $mailOptionsDataType = '';
  /**
   * The entity to search. This field replaces **searchMethod** to support
   * shared drives. When **searchMethod** is **TEAM_DRIVE**, the response of
   * this field is **SHARED_DRIVE**.
   *
   * @var string
   */
  public $method;
  protected $orgUnitInfoType = OrgUnitInfo::class;
  protected $orgUnitInfoDataType = '';
  /**
   * The search method to use.
   *
   * @deprecated
   * @var string
   */
  public $searchMethod;
  protected $sharedDriveInfoType = SharedDriveInfo::class;
  protected $sharedDriveInfoDataType = '';
  protected $sitesUrlInfoType = SitesUrlInfo::class;
  protected $sitesUrlInfoDataType = '';
  /**
   * The start time for the search query. Specify in GMT. The value is rounded
   * to 12 AM on the specified date.
   *
   * @var string
   */
  public $startTime;
  protected $teamDriveInfoType = TeamDriveInfo::class;
  protected $teamDriveInfoDataType = '';
  /**
   * Service-specific [search
   * operators](https://support.google.com/vault/answer/2474474) to filter
   * search results.
   *
   * @var string
   */
  public $terms;
  /**
   * The time zone name. It should be an IANA TZ name, such as
   * "America/Los_Angeles". For a list of time zone names, see [Time
   * Zone](https://en.wikipedia.org/wiki/List_of_tz_database_time_zones). For
   * more information about how Vault uses time zones, see [the Vault help
   * center](https://support.google.com/vault/answer/6092995#time).
   *
   * @var string
   */
  public $timeZone;
  protected $voiceOptionsType = VoiceOptions::class;
  protected $voiceOptionsDataType = '';

  /**
   * Required when **SearchMethod** is **ACCOUNT**.
   *
   * @param AccountInfo $accountInfo
   */
  public function setAccountInfo(AccountInfo $accountInfo)
  {
    $this->accountInfo = $accountInfo;
  }
  /**
   * @return AccountInfo
   */
  public function getAccountInfo()
  {
    return $this->accountInfo;
  }
  /**
   * Set Calendar search-specific options.
   *
   * @param CalendarOptions $calendarOptions
   */
  public function setCalendarOptions(CalendarOptions $calendarOptions)
  {
    $this->calendarOptions = $calendarOptions;
  }
  /**
   * @return CalendarOptions
   */
  public function getCalendarOptions()
  {
    return $this->calendarOptions;
  }
  /**
   * The Google Workspace service to search.
   *
   * Accepted values: CORPUS_TYPE_UNSPECIFIED, DRIVE, MAIL, GROUPS,
   * HANGOUTS_CHAT, VOICE, CALENDAR, GEMINI
   *
   * @param self::CORPUS_* $corpus
   */
  public function setCorpus($corpus)
  {
    $this->corpus = $corpus;
  }
  /**
   * @return self::CORPUS_*
   */
  public function getCorpus()
  {
    return $this->corpus;
  }
  /**
   * The data source to search.
   *
   * Accepted values: DATA_SCOPE_UNSPECIFIED, ALL_DATA, HELD_DATA,
   * UNPROCESSED_DATA
   *
   * @param self::DATA_SCOPE_* $dataScope
   */
  public function setDataScope($dataScope)
  {
    $this->dataScope = $dataScope;
  }
  /**
   * @return self::DATA_SCOPE_*
   */
  public function getDataScope()
  {
    return $this->dataScope;
  }
  /**
   * Required when **SearchMethod** is **DRIVE_DOCUMENT**.
   *
   * @param DriveDocumentInfo $driveDocumentInfo
   */
  public function setDriveDocumentInfo(DriveDocumentInfo $driveDocumentInfo)
  {
    $this->driveDocumentInfo = $driveDocumentInfo;
  }
  /**
   * @return DriveDocumentInfo
   */
  public function getDriveDocumentInfo()
  {
    return $this->driveDocumentInfo;
  }
  /**
   * Set Drive search-specific options.
   *
   * @param DriveOptions $driveOptions
   */
  public function setDriveOptions(DriveOptions $driveOptions)
  {
    $this->driveOptions = $driveOptions;
  }
  /**
   * @return DriveOptions
   */
  public function getDriveOptions()
  {
    return $this->driveOptions;
  }
  /**
   * The end time for the search query. Specify in GMT. The value is rounded to
   * 12 AM on the specified date.
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
   * Set Gemini search-specific options.
   *
   * @param GeminiOptions $geminiOptions
   */
  public function setGeminiOptions(GeminiOptions $geminiOptions)
  {
    $this->geminiOptions = $geminiOptions;
  }
  /**
   * @return GeminiOptions
   */
  public function getGeminiOptions()
  {
    return $this->geminiOptions;
  }
  /**
   * Required when **SearchMethod** is **ROOM**. (read-only)
   *
   * @param HangoutsChatInfo $hangoutsChatInfo
   */
  public function setHangoutsChatInfo(HangoutsChatInfo $hangoutsChatInfo)
  {
    $this->hangoutsChatInfo = $hangoutsChatInfo;
  }
  /**
   * @return HangoutsChatInfo
   */
  public function getHangoutsChatInfo()
  {
    return $this->hangoutsChatInfo;
  }
  /**
   * Set Chat search-specific options. (read-only)
   *
   * @param HangoutsChatOptions $hangoutsChatOptions
   */
  public function setHangoutsChatOptions(HangoutsChatOptions $hangoutsChatOptions)
  {
    $this->hangoutsChatOptions = $hangoutsChatOptions;
  }
  /**
   * @return HangoutsChatOptions
   */
  public function getHangoutsChatOptions()
  {
    return $this->hangoutsChatOptions;
  }
  /**
   * Set Gmail search-specific options.
   *
   * @param MailOptions $mailOptions
   */
  public function setMailOptions(MailOptions $mailOptions)
  {
    $this->mailOptions = $mailOptions;
  }
  /**
   * @return MailOptions
   */
  public function getMailOptions()
  {
    return $this->mailOptions;
  }
  /**
   * The entity to search. This field replaces **searchMethod** to support
   * shared drives. When **searchMethod** is **TEAM_DRIVE**, the response of
   * this field is **SHARED_DRIVE**.
   *
   * Accepted values: SEARCH_METHOD_UNSPECIFIED, ACCOUNT, ORG_UNIT, TEAM_DRIVE,
   * ENTIRE_ORG, ROOM, SITES_URL, SHARED_DRIVE, DRIVE_DOCUMENT
   *
   * @param self::METHOD_* $method
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }
  /**
   * @return self::METHOD_*
   */
  public function getMethod()
  {
    return $this->method;
  }
  /**
   * Required when **SearchMethod** is **ORG_UNIT**.
   *
   * @param OrgUnitInfo $orgUnitInfo
   */
  public function setOrgUnitInfo(OrgUnitInfo $orgUnitInfo)
  {
    $this->orgUnitInfo = $orgUnitInfo;
  }
  /**
   * @return OrgUnitInfo
   */
  public function getOrgUnitInfo()
  {
    return $this->orgUnitInfo;
  }
  /**
   * The search method to use.
   *
   * Accepted values: SEARCH_METHOD_UNSPECIFIED, ACCOUNT, ORG_UNIT, TEAM_DRIVE,
   * ENTIRE_ORG, ROOM, SITES_URL, SHARED_DRIVE, DRIVE_DOCUMENT
   *
   * @deprecated
   * @param self::SEARCH_METHOD_* $searchMethod
   */
  public function setSearchMethod($searchMethod)
  {
    $this->searchMethod = $searchMethod;
  }
  /**
   * @deprecated
   * @return self::SEARCH_METHOD_*
   */
  public function getSearchMethod()
  {
    return $this->searchMethod;
  }
  /**
   * Required when **SearchMethod** is **SHARED_DRIVE**.
   *
   * @param SharedDriveInfo $sharedDriveInfo
   */
  public function setSharedDriveInfo(SharedDriveInfo $sharedDriveInfo)
  {
    $this->sharedDriveInfo = $sharedDriveInfo;
  }
  /**
   * @return SharedDriveInfo
   */
  public function getSharedDriveInfo()
  {
    return $this->sharedDriveInfo;
  }
  /**
   * Required when **SearchMethod** is **SITES_URL**.
   *
   * @param SitesUrlInfo $sitesUrlInfo
   */
  public function setSitesUrlInfo(SitesUrlInfo $sitesUrlInfo)
  {
    $this->sitesUrlInfo = $sitesUrlInfo;
  }
  /**
   * @return SitesUrlInfo
   */
  public function getSitesUrlInfo()
  {
    return $this->sitesUrlInfo;
  }
  /**
   * The start time for the search query. Specify in GMT. The value is rounded
   * to 12 AM on the specified date.
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
   * Required when **SearchMethod** is **TEAM_DRIVE**.
   *
   * @deprecated
   * @param TeamDriveInfo $teamDriveInfo
   */
  public function setTeamDriveInfo(TeamDriveInfo $teamDriveInfo)
  {
    $this->teamDriveInfo = $teamDriveInfo;
  }
  /**
   * @deprecated
   * @return TeamDriveInfo
   */
  public function getTeamDriveInfo()
  {
    return $this->teamDriveInfo;
  }
  /**
   * Service-specific [search
   * operators](https://support.google.com/vault/answer/2474474) to filter
   * search results.
   *
   * @param string $terms
   */
  public function setTerms($terms)
  {
    $this->terms = $terms;
  }
  /**
   * @return string
   */
  public function getTerms()
  {
    return $this->terms;
  }
  /**
   * The time zone name. It should be an IANA TZ name, such as
   * "America/Los_Angeles". For a list of time zone names, see [Time
   * Zone](https://en.wikipedia.org/wiki/List_of_tz_database_time_zones). For
   * more information about how Vault uses time zones, see [the Vault help
   * center](https://support.google.com/vault/answer/6092995#time).
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
  /**
   * Set Voice search-specific options.
   *
   * @param VoiceOptions $voiceOptions
   */
  public function setVoiceOptions(VoiceOptions $voiceOptions)
  {
    $this->voiceOptions = $voiceOptions;
  }
  /**
   * @return VoiceOptions
   */
  public function getVoiceOptions()
  {
    return $this->voiceOptions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Query::class, 'Google_Service_Vault_Query');
