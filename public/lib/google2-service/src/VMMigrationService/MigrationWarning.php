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

namespace Google\Service\VMMigrationService;

class MigrationWarning extends \Google\Collection
{
  /**
   * Default value. This value is not used.
   */
  public const CODE_WARNING_CODE_UNSPECIFIED = 'WARNING_CODE_UNSPECIFIED';
  /**
   * A warning originated from OS Adaptation.
   */
  public const CODE_ADAPTATION_WARNING = 'ADAPTATION_WARNING';
  protected $collection_key = 'helpLinks';
  protected $actionItemType = LocalizedMessage::class;
  protected $actionItemDataType = '';
  /**
   * The warning code.
   *
   * @var string
   */
  public $code;
  protected $helpLinksType = Link::class;
  protected $helpLinksDataType = 'array';
  protected $warningMessageType = LocalizedMessage::class;
  protected $warningMessageDataType = '';
  /**
   * The time the warning occurred.
   *
   * @var string
   */
  public $warningTime;

  /**
   * Output only. Suggested action for solving the warning.
   *
   * @param LocalizedMessage $actionItem
   */
  public function setActionItem(LocalizedMessage $actionItem)
  {
    $this->actionItem = $actionItem;
  }
  /**
   * @return LocalizedMessage
   */
  public function getActionItem()
  {
    return $this->actionItem;
  }
  /**
   * The warning code.
   *
   * Accepted values: WARNING_CODE_UNSPECIFIED, ADAPTATION_WARNING
   *
   * @param self::CODE_* $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return self::CODE_*
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * Output only. URL(s) pointing to additional information on handling the
   * current warning.
   *
   * @param Link[] $helpLinks
   */
  public function setHelpLinks($helpLinks)
  {
    $this->helpLinks = $helpLinks;
  }
  /**
   * @return Link[]
   */
  public function getHelpLinks()
  {
    return $this->helpLinks;
  }
  /**
   * Output only. The localized warning message.
   *
   * @param LocalizedMessage $warningMessage
   */
  public function setWarningMessage(LocalizedMessage $warningMessage)
  {
    $this->warningMessage = $warningMessage;
  }
  /**
   * @return LocalizedMessage
   */
  public function getWarningMessage()
  {
    return $this->warningMessage;
  }
  /**
   * The time the warning occurred.
   *
   * @param string $warningTime
   */
  public function setWarningTime($warningTime)
  {
    $this->warningTime = $warningTime;
  }
  /**
   * @return string
   */
  public function getWarningTime()
  {
    return $this->warningTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MigrationWarning::class, 'Google_Service_VMMigrationService_MigrationWarning');
