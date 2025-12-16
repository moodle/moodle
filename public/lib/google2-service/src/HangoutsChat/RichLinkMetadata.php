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

namespace Google\Service\HangoutsChat;

class RichLinkMetadata extends \Google\Model
{
  /**
   * Default value for the enum. Don't use.
   */
  public const RICH_LINK_TYPE_RICH_LINK_TYPE_UNSPECIFIED = 'RICH_LINK_TYPE_UNSPECIFIED';
  /**
   * A Google Drive rich link type.
   */
  public const RICH_LINK_TYPE_DRIVE_FILE = 'DRIVE_FILE';
  /**
   * A Chat space rich link type. For example, a space smart chip.
   */
  public const RICH_LINK_TYPE_CHAT_SPACE = 'CHAT_SPACE';
  /**
   * A Gmail message rich link type. Specifically, a Gmail chip from [Share to
   * Chat](https://support.google.com/chat?p=chat_gmail). The API only supports
   * reading messages with GMAIL_MESSAGE rich links.
   */
  public const RICH_LINK_TYPE_GMAIL_MESSAGE = 'GMAIL_MESSAGE';
  /**
   * A Meet message rich link type. For example, a Meet chip.
   */
  public const RICH_LINK_TYPE_MEET_SPACE = 'MEET_SPACE';
  /**
   * A Calendar message rich link type. For example, a Calendar chip.
   */
  public const RICH_LINK_TYPE_CALENDAR_EVENT = 'CALENDAR_EVENT';
  protected $calendarEventLinkDataType = CalendarEventLinkData::class;
  protected $calendarEventLinkDataDataType = '';
  protected $chatSpaceLinkDataType = ChatSpaceLinkData::class;
  protected $chatSpaceLinkDataDataType = '';
  protected $driveLinkDataType = DriveLinkData::class;
  protected $driveLinkDataDataType = '';
  protected $meetSpaceLinkDataType = MeetSpaceLinkData::class;
  protected $meetSpaceLinkDataDataType = '';
  /**
   * The rich link type.
   *
   * @var string
   */
  public $richLinkType;
  /**
   * The URI of this link.
   *
   * @var string
   */
  public $uri;

  /**
   * Data for a Calendar event link.
   *
   * @param CalendarEventLinkData $calendarEventLinkData
   */
  public function setCalendarEventLinkData(CalendarEventLinkData $calendarEventLinkData)
  {
    $this->calendarEventLinkData = $calendarEventLinkData;
  }
  /**
   * @return CalendarEventLinkData
   */
  public function getCalendarEventLinkData()
  {
    return $this->calendarEventLinkData;
  }
  /**
   * Data for a chat space link.
   *
   * @param ChatSpaceLinkData $chatSpaceLinkData
   */
  public function setChatSpaceLinkData(ChatSpaceLinkData $chatSpaceLinkData)
  {
    $this->chatSpaceLinkData = $chatSpaceLinkData;
  }
  /**
   * @return ChatSpaceLinkData
   */
  public function getChatSpaceLinkData()
  {
    return $this->chatSpaceLinkData;
  }
  /**
   * Data for a drive link.
   *
   * @param DriveLinkData $driveLinkData
   */
  public function setDriveLinkData(DriveLinkData $driveLinkData)
  {
    $this->driveLinkData = $driveLinkData;
  }
  /**
   * @return DriveLinkData
   */
  public function getDriveLinkData()
  {
    return $this->driveLinkData;
  }
  /**
   * Data for a Meet space link.
   *
   * @param MeetSpaceLinkData $meetSpaceLinkData
   */
  public function setMeetSpaceLinkData(MeetSpaceLinkData $meetSpaceLinkData)
  {
    $this->meetSpaceLinkData = $meetSpaceLinkData;
  }
  /**
   * @return MeetSpaceLinkData
   */
  public function getMeetSpaceLinkData()
  {
    return $this->meetSpaceLinkData;
  }
  /**
   * The rich link type.
   *
   * Accepted values: RICH_LINK_TYPE_UNSPECIFIED, DRIVE_FILE, CHAT_SPACE,
   * GMAIL_MESSAGE, MEET_SPACE, CALENDAR_EVENT
   *
   * @param self::RICH_LINK_TYPE_* $richLinkType
   */
  public function setRichLinkType($richLinkType)
  {
    $this->richLinkType = $richLinkType;
  }
  /**
   * @return self::RICH_LINK_TYPE_*
   */
  public function getRichLinkType()
  {
    return $this->richLinkType;
  }
  /**
   * The URI of this link.
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
class_alias(RichLinkMetadata::class, 'Google_Service_HangoutsChat_RichLinkMetadata');
