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

class MigrationError extends \Google\Collection
{
  /**
   * Default value. This value is not used.
   */
  public const CODE_ERROR_CODE_UNSPECIFIED = 'ERROR_CODE_UNSPECIFIED';
  /**
   * Migrate to Virtual Machines encountered an unknown error.
   */
  public const CODE_UNKNOWN_ERROR = 'UNKNOWN_ERROR';
  /**
   * Migrate to Virtual Machines encountered an error while validating
   * replication source health.
   */
  public const CODE_SOURCE_VALIDATION_ERROR = 'SOURCE_VALIDATION_ERROR';
  /**
   * Migrate to Virtual Machines encountered an error during source data
   * operation.
   */
  public const CODE_SOURCE_REPLICATION_ERROR = 'SOURCE_REPLICATION_ERROR';
  /**
   * Migrate to Virtual Machines encountered an error during target data
   * operation.
   */
  public const CODE_TARGET_REPLICATION_ERROR = 'TARGET_REPLICATION_ERROR';
  /**
   * Migrate to Virtual Machines encountered an error during OS adaptation.
   */
  public const CODE_OS_ADAPTATION_ERROR = 'OS_ADAPTATION_ERROR';
  /**
   * Migrate to Virtual Machines encountered an error in clone operation.
   */
  public const CODE_CLONE_ERROR = 'CLONE_ERROR';
  /**
   * Migrate to Virtual Machines encountered an error in cutover operation.
   */
  public const CODE_CUTOVER_ERROR = 'CUTOVER_ERROR';
  /**
   * Migrate to Virtual Machines encountered an error during utilization report
   * creation.
   */
  public const CODE_UTILIZATION_REPORT_ERROR = 'UTILIZATION_REPORT_ERROR';
  /**
   * Migrate to Virtual Machines encountered an error during appliance upgrade.
   */
  public const CODE_APPLIANCE_UPGRADE_ERROR = 'APPLIANCE_UPGRADE_ERROR';
  /**
   * Migrate to Virtual Machines encountered an error in image import operation.
   */
  public const CODE_IMAGE_IMPORT_ERROR = 'IMAGE_IMPORT_ERROR';
  /**
   * Migrate to Virtual Machines encountered an error in disk migration
   * operation.
   */
  public const CODE_DISK_MIGRATION_ERROR = 'DISK_MIGRATION_ERROR';
  protected $collection_key = 'helpLinks';
  protected $actionItemType = LocalizedMessage::class;
  protected $actionItemDataType = '';
  /**
   * Output only. The error code.
   *
   * @var string
   */
  public $code;
  protected $errorMessageType = LocalizedMessage::class;
  protected $errorMessageDataType = '';
  /**
   * Output only. The time the error occurred.
   *
   * @var string
   */
  public $errorTime;
  protected $helpLinksType = Link::class;
  protected $helpLinksDataType = 'array';

  /**
   * Output only. Suggested action for solving the error.
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
   * Output only. The error code.
   *
   * Accepted values: ERROR_CODE_UNSPECIFIED, UNKNOWN_ERROR,
   * SOURCE_VALIDATION_ERROR, SOURCE_REPLICATION_ERROR,
   * TARGET_REPLICATION_ERROR, OS_ADAPTATION_ERROR, CLONE_ERROR, CUTOVER_ERROR,
   * UTILIZATION_REPORT_ERROR, APPLIANCE_UPGRADE_ERROR, IMAGE_IMPORT_ERROR,
   * DISK_MIGRATION_ERROR
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
   * Output only. The localized error message.
   *
   * @param LocalizedMessage $errorMessage
   */
  public function setErrorMessage(LocalizedMessage $errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return LocalizedMessage
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  /**
   * Output only. The time the error occurred.
   *
   * @param string $errorTime
   */
  public function setErrorTime($errorTime)
  {
    $this->errorTime = $errorTime;
  }
  /**
   * @return string
   */
  public function getErrorTime()
  {
    return $this->errorTime;
  }
  /**
   * Output only. URL(s) pointing to additional information on handling the
   * current error.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MigrationError::class, 'Google_Service_VMMigrationService_MigrationError');
