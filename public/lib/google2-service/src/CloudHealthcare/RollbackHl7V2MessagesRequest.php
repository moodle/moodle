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

namespace Google\Service\CloudHealthcare;

class RollbackHl7V2MessagesRequest extends \Google\Model
{
  /**
   * When unspecified, revert all transactions
   */
  public const CHANGE_TYPE_CHANGE_TYPE_UNSPECIFIED = 'CHANGE_TYPE_UNSPECIFIED';
  /**
   * All transactions
   */
  public const CHANGE_TYPE_ALL = 'ALL';
  /**
   * Revert only CREATE transactions
   */
  public const CHANGE_TYPE_CREATE = 'CREATE';
  /**
   * Revert only Update transactions
   */
  public const CHANGE_TYPE_UPDATE = 'UPDATE';
  /**
   * Revert only Delete transactions
   */
  public const CHANGE_TYPE_DELETE = 'DELETE';
  /**
   * Optional. CREATE/UPDATE/DELETE/ALL for reverting all txns of a certain
   * type.
   *
   * @var string
   */
  public $changeType;
  /**
   * Optional. Specifies whether to exclude earlier rollbacks.
   *
   * @var bool
   */
  public $excludeRollbacks;
  protected $filteringFieldsType = RollbackHL7MessagesFilteringFields::class;
  protected $filteringFieldsDataType = '';
  /**
   * Optional. When enabled, changes will be reverted without explicit
   * confirmation.
   *
   * @var bool
   */
  public $force;
  /**
   * Optional. Cloud storage object containing list of {resourceId} lines,
   * identifying resources to be reverted
   *
   * @var string
   */
  public $inputGcsObject;
  /**
   * Required. Bucket to deposit result
   *
   * @var string
   */
  public $resultGcsBucket;
  /**
   * Required. Times point to rollback to.
   *
   * @var string
   */
  public $rollbackTime;

  /**
   * Optional. CREATE/UPDATE/DELETE/ALL for reverting all txns of a certain
   * type.
   *
   * Accepted values: CHANGE_TYPE_UNSPECIFIED, ALL, CREATE, UPDATE, DELETE
   *
   * @param self::CHANGE_TYPE_* $changeType
   */
  public function setChangeType($changeType)
  {
    $this->changeType = $changeType;
  }
  /**
   * @return self::CHANGE_TYPE_*
   */
  public function getChangeType()
  {
    return $this->changeType;
  }
  /**
   * Optional. Specifies whether to exclude earlier rollbacks.
   *
   * @param bool $excludeRollbacks
   */
  public function setExcludeRollbacks($excludeRollbacks)
  {
    $this->excludeRollbacks = $excludeRollbacks;
  }
  /**
   * @return bool
   */
  public function getExcludeRollbacks()
  {
    return $this->excludeRollbacks;
  }
  /**
   * Optional. Parameters for filtering.
   *
   * @param RollbackHL7MessagesFilteringFields $filteringFields
   */
  public function setFilteringFields(RollbackHL7MessagesFilteringFields $filteringFields)
  {
    $this->filteringFields = $filteringFields;
  }
  /**
   * @return RollbackHL7MessagesFilteringFields
   */
  public function getFilteringFields()
  {
    return $this->filteringFields;
  }
  /**
   * Optional. When enabled, changes will be reverted without explicit
   * confirmation.
   *
   * @param bool $force
   */
  public function setForce($force)
  {
    $this->force = $force;
  }
  /**
   * @return bool
   */
  public function getForce()
  {
    return $this->force;
  }
  /**
   * Optional. Cloud storage object containing list of {resourceId} lines,
   * identifying resources to be reverted
   *
   * @param string $inputGcsObject
   */
  public function setInputGcsObject($inputGcsObject)
  {
    $this->inputGcsObject = $inputGcsObject;
  }
  /**
   * @return string
   */
  public function getInputGcsObject()
  {
    return $this->inputGcsObject;
  }
  /**
   * Required. Bucket to deposit result
   *
   * @param string $resultGcsBucket
   */
  public function setResultGcsBucket($resultGcsBucket)
  {
    $this->resultGcsBucket = $resultGcsBucket;
  }
  /**
   * @return string
   */
  public function getResultGcsBucket()
  {
    return $this->resultGcsBucket;
  }
  /**
   * Required. Times point to rollback to.
   *
   * @param string $rollbackTime
   */
  public function setRollbackTime($rollbackTime)
  {
    $this->rollbackTime = $rollbackTime;
  }
  /**
   * @return string
   */
  public function getRollbackTime()
  {
    return $this->rollbackTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RollbackHl7V2MessagesRequest::class, 'Google_Service_CloudHealthcare_RollbackHl7V2MessagesRequest');
