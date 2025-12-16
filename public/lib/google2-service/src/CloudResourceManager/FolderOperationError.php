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

namespace Google\Service\CloudResourceManager;

class FolderOperationError extends \Google\Model
{
  /**
   * The error type was unrecognized or unspecified.
   */
  public const ERROR_MESSAGE_ID_ERROR_TYPE_UNSPECIFIED = 'ERROR_TYPE_UNSPECIFIED';
  /**
   * The attempted action would violate the max folder depth constraint.
   */
  public const ERROR_MESSAGE_ID_ACTIVE_FOLDER_HEIGHT_VIOLATION = 'ACTIVE_FOLDER_HEIGHT_VIOLATION';
  /**
   * The attempted action would violate the max child folders constraint.
   */
  public const ERROR_MESSAGE_ID_MAX_CHILD_FOLDERS_VIOLATION = 'MAX_CHILD_FOLDERS_VIOLATION';
  /**
   * The attempted action would violate the locally-unique folder display_name
   * constraint.
   */
  public const ERROR_MESSAGE_ID_FOLDER_NAME_UNIQUENESS_VIOLATION = 'FOLDER_NAME_UNIQUENESS_VIOLATION';
  /**
   * The resource being moved has been deleted.
   */
  public const ERROR_MESSAGE_ID_RESOURCE_DELETED_VIOLATION = 'RESOURCE_DELETED_VIOLATION';
  /**
   * The resource a folder was being added to has been deleted.
   */
  public const ERROR_MESSAGE_ID_PARENT_DELETED_VIOLATION = 'PARENT_DELETED_VIOLATION';
  /**
   * The attempted action would introduce cycle in resource path.
   */
  public const ERROR_MESSAGE_ID_CYCLE_INTRODUCED_VIOLATION = 'CYCLE_INTRODUCED_VIOLATION';
  /**
   * The attempted action would move a folder that is already being moved.
   */
  public const ERROR_MESSAGE_ID_FOLDER_BEING_MOVED_VIOLATION = 'FOLDER_BEING_MOVED_VIOLATION';
  /**
   * The folder the caller is trying to delete contains active resources.
   */
  public const ERROR_MESSAGE_ID_FOLDER_TO_DELETE_NON_EMPTY_VIOLATION = 'FOLDER_TO_DELETE_NON_EMPTY_VIOLATION';
  /**
   * The attempted action would violate the max deleted folder depth constraint.
   */
  public const ERROR_MESSAGE_ID_DELETED_FOLDER_HEIGHT_VIOLATION = 'DELETED_FOLDER_HEIGHT_VIOLATION';
  /**
   * The folder being deleted has a configured capability.
   */
  public const ERROR_MESSAGE_ID_FOLDER_TO_DELETE_CONFIGURED_CAPABILITY_VIOLATION = 'FOLDER_TO_DELETE_CONFIGURED_CAPABILITY_VIOLATION';
  /**
   * The type of operation error experienced.
   *
   * @var string
   */
  public $errorMessageId;

  /**
   * The type of operation error experienced.
   *
   * Accepted values: ERROR_TYPE_UNSPECIFIED, ACTIVE_FOLDER_HEIGHT_VIOLATION,
   * MAX_CHILD_FOLDERS_VIOLATION, FOLDER_NAME_UNIQUENESS_VIOLATION,
   * RESOURCE_DELETED_VIOLATION, PARENT_DELETED_VIOLATION,
   * CYCLE_INTRODUCED_VIOLATION, FOLDER_BEING_MOVED_VIOLATION,
   * FOLDER_TO_DELETE_NON_EMPTY_VIOLATION, DELETED_FOLDER_HEIGHT_VIOLATION,
   * FOLDER_TO_DELETE_CONFIGURED_CAPABILITY_VIOLATION
   *
   * @param self::ERROR_MESSAGE_ID_* $errorMessageId
   */
  public function setErrorMessageId($errorMessageId)
  {
    $this->errorMessageId = $errorMessageId;
  }
  /**
   * @return self::ERROR_MESSAGE_ID_*
   */
  public function getErrorMessageId()
  {
    return $this->errorMessageId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FolderOperationError::class, 'Google_Service_CloudResourceManager_FolderOperationError');
