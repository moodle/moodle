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

namespace Google\Service\AlertCenter;

class RuleViolationInfo extends \Google\Collection
{
  /**
   * Data source is unspecified.
   */
  public const DATA_SOURCE_DATA_SOURCE_UNSPECIFIED = 'DATA_SOURCE_UNSPECIFIED';
  /**
   * Drive data source.
   */
  public const DATA_SOURCE_DRIVE = 'DRIVE';
  /**
   * Chrome data source.
   */
  public const DATA_SOURCE_CHROME = 'CHROME';
  /**
   * Chat data source.
   */
  public const DATA_SOURCE_CHAT = 'CHAT';
  /**
   * Event type wasn't set.
   */
  public const EVENT_TYPE_EVENT_TYPE_UNSPECIFIED = 'EVENT_TYPE_UNSPECIFIED';
  /**
   * An access attempt was blocked.
   */
  public const EVENT_TYPE_ACCESS_BLOCKED = 'ACCESS_BLOCKED';
  /**
   * A sharing attempt was blocked.
   */
  public const EVENT_TYPE_SHARING_BLOCKED = 'SHARING_BLOCKED';
  /**
   * Trigger is unspecified.
   */
  public const TRIGGER_TRIGGER_UNSPECIFIED = 'TRIGGER_UNSPECIFIED';
  /**
   * A Drive file is shared.
   */
  public const TRIGGER_DRIVE_SHARE = 'DRIVE_SHARE';
  /**
   * A file being downloaded in a Chrome browser.
   */
  public const TRIGGER_CHROME_FILE_DOWNLOAD = 'CHROME_FILE_DOWNLOAD';
  /**
   * A file being uploaded from a Chrome browser.
   */
  public const TRIGGER_CHROME_FILE_UPLOAD = 'CHROME_FILE_UPLOAD';
  /**
   * Web content being uploaded from a Chrome browser.
   */
  public const TRIGGER_CHROME_WEB_CONTENT_UPLOAD = 'CHROME_WEB_CONTENT_UPLOAD';
  /**
   * A Chat message is sent.
   */
  public const TRIGGER_CHAT_MESSAGE_SENT = 'CHAT_MESSAGE_SENT';
  /**
   * A Chat attachment is uploaded.
   */
  public const TRIGGER_CHAT_ATTACHMENT_UPLOADED = 'CHAT_ATTACHMENT_UPLOADED';
  /**
   * A page is being printed by Chrome.
   */
  public const TRIGGER_CHROME_PAGE_PRINT = 'CHROME_PAGE_PRINT';
  /**
   * A URL is visited within Chrome.
   */
  public const TRIGGER_CHROME_URL_VISITED = 'CHROME_URL_VISITED';
  protected $collection_key = 'triggeredActionTypes';
  /**
   * Source of the data.
   *
   * @var string
   */
  public $dataSource;
  /**
   * Event associated with this alert after applying the rule.
   *
   * @var string
   */
  public $eventType;
  protected $matchInfoType = MatchInfo::class;
  protected $matchInfoDataType = 'array';
  /**
   * Resource recipients. For Drive, they are grantees that the Drive file was
   * shared with at the time of rule triggering. Valid values include user
   * emails, group emails, domains, or 'anyone' if the file was publicly
   * accessible. If the file was private the recipients list will be empty. For
   * Gmail, they are emails of the users or groups that the Gmail message was
   * sent to.
   *
   * @var string[]
   */
  public $recipients;
  protected $resourceInfoType = ResourceInfo::class;
  protected $resourceInfoDataType = '';
  protected $ruleInfoType = RuleInfo::class;
  protected $ruleInfoDataType = '';
  /**
   * Actions suppressed due to other actions with higher priority.
   *
   * @var string[]
   */
  public $suppressedActionTypes;
  /**
   * Trigger of the rule.
   *
   * @var string
   */
  public $trigger;
  protected $triggeredActionInfoType = ActionInfo::class;
  protected $triggeredActionInfoDataType = 'array';
  /**
   * Actions applied as a consequence of the rule being triggered.
   *
   * @var string[]
   */
  public $triggeredActionTypes;
  /**
   * Email of the user who caused the violation. Value could be empty if not
   * applicable, for example, a violation found by drive continuous scan.
   *
   * @var string
   */
  public $triggeringUserEmail;

  /**
   * Source of the data.
   *
   * Accepted values: DATA_SOURCE_UNSPECIFIED, DRIVE, CHROME, CHAT
   *
   * @param self::DATA_SOURCE_* $dataSource
   */
  public function setDataSource($dataSource)
  {
    $this->dataSource = $dataSource;
  }
  /**
   * @return self::DATA_SOURCE_*
   */
  public function getDataSource()
  {
    return $this->dataSource;
  }
  /**
   * Event associated with this alert after applying the rule.
   *
   * Accepted values: EVENT_TYPE_UNSPECIFIED, ACCESS_BLOCKED, SHARING_BLOCKED
   *
   * @param self::EVENT_TYPE_* $eventType
   */
  public function setEventType($eventType)
  {
    $this->eventType = $eventType;
  }
  /**
   * @return self::EVENT_TYPE_*
   */
  public function getEventType()
  {
    return $this->eventType;
  }
  /**
   * List of matches that were found in the resource content.
   *
   * @param MatchInfo[] $matchInfo
   */
  public function setMatchInfo($matchInfo)
  {
    $this->matchInfo = $matchInfo;
  }
  /**
   * @return MatchInfo[]
   */
  public function getMatchInfo()
  {
    return $this->matchInfo;
  }
  /**
   * Resource recipients. For Drive, they are grantees that the Drive file was
   * shared with at the time of rule triggering. Valid values include user
   * emails, group emails, domains, or 'anyone' if the file was publicly
   * accessible. If the file was private the recipients list will be empty. For
   * Gmail, they are emails of the users or groups that the Gmail message was
   * sent to.
   *
   * @param string[] $recipients
   */
  public function setRecipients($recipients)
  {
    $this->recipients = $recipients;
  }
  /**
   * @return string[]
   */
  public function getRecipients()
  {
    return $this->recipients;
  }
  /**
   * Details of the resource which violated the rule.
   *
   * @param ResourceInfo $resourceInfo
   */
  public function setResourceInfo(ResourceInfo $resourceInfo)
  {
    $this->resourceInfo = $resourceInfo;
  }
  /**
   * @return ResourceInfo
   */
  public function getResourceInfo()
  {
    return $this->resourceInfo;
  }
  /**
   * Details of the violated rule.
   *
   * @param RuleInfo $ruleInfo
   */
  public function setRuleInfo(RuleInfo $ruleInfo)
  {
    $this->ruleInfo = $ruleInfo;
  }
  /**
   * @return RuleInfo
   */
  public function getRuleInfo()
  {
    return $this->ruleInfo;
  }
  /**
   * Actions suppressed due to other actions with higher priority.
   *
   * @param string[] $suppressedActionTypes
   */
  public function setSuppressedActionTypes($suppressedActionTypes)
  {
    $this->suppressedActionTypes = $suppressedActionTypes;
  }
  /**
   * @return string[]
   */
  public function getSuppressedActionTypes()
  {
    return $this->suppressedActionTypes;
  }
  /**
   * Trigger of the rule.
   *
   * Accepted values: TRIGGER_UNSPECIFIED, DRIVE_SHARE, CHROME_FILE_DOWNLOAD,
   * CHROME_FILE_UPLOAD, CHROME_WEB_CONTENT_UPLOAD, CHAT_MESSAGE_SENT,
   * CHAT_ATTACHMENT_UPLOADED, CHROME_PAGE_PRINT, CHROME_URL_VISITED
   *
   * @param self::TRIGGER_* $trigger
   */
  public function setTrigger($trigger)
  {
    $this->trigger = $trigger;
  }
  /**
   * @return self::TRIGGER_*
   */
  public function getTrigger()
  {
    return $this->trigger;
  }
  /**
   * Metadata related to the triggered actions.
   *
   * @param ActionInfo[] $triggeredActionInfo
   */
  public function setTriggeredActionInfo($triggeredActionInfo)
  {
    $this->triggeredActionInfo = $triggeredActionInfo;
  }
  /**
   * @return ActionInfo[]
   */
  public function getTriggeredActionInfo()
  {
    return $this->triggeredActionInfo;
  }
  /**
   * Actions applied as a consequence of the rule being triggered.
   *
   * @param string[] $triggeredActionTypes
   */
  public function setTriggeredActionTypes($triggeredActionTypes)
  {
    $this->triggeredActionTypes = $triggeredActionTypes;
  }
  /**
   * @return string[]
   */
  public function getTriggeredActionTypes()
  {
    return $this->triggeredActionTypes;
  }
  /**
   * Email of the user who caused the violation. Value could be empty if not
   * applicable, for example, a violation found by drive continuous scan.
   *
   * @param string $triggeringUserEmail
   */
  public function setTriggeringUserEmail($triggeringUserEmail)
  {
    $this->triggeringUserEmail = $triggeringUserEmail;
  }
  /**
   * @return string
   */
  public function getTriggeringUserEmail()
  {
    return $this->triggeringUserEmail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RuleViolationInfo::class, 'Google_Service_AlertCenter_RuleViolationInfo');
