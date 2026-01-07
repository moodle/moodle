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

namespace Google\Service\Dfareporting;

class ChangeLog extends \Google\Model
{
  /**
   * Account ID of the modified object.
   *
   * @var string
   */
  public $accountId;
  /**
   * Action which caused the change.
   *
   * @var string
   */
  public $action;
  /**
   * @var string
   */
  public $changeTime;
  /**
   * Field name of the object which changed.
   *
   * @var string
   */
  public $fieldName;
  /**
   * ID of this change log.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#changeLog".
   *
   * @var string
   */
  public $kind;
  /**
   * New value of the object field.
   *
   * @var string
   */
  public $newValue;
  /**
   * ID of the object of this change log. The object could be a campaign,
   * placement, ad, or other type.
   *
   * @var string
   */
  public $objectId;
  /**
   * Object type of the change log.
   *
   * @var string
   */
  public $objectType;
  /**
   * Old value of the object field.
   *
   * @var string
   */
  public $oldValue;
  /**
   * Subaccount ID of the modified object.
   *
   * @var string
   */
  public $subaccountId;
  /**
   * Transaction ID of this change log. When a single API call results in many
   * changes, each change will have a separate ID in the change log but will
   * share the same transactionId.
   *
   * @var string
   */
  public $transactionId;
  /**
   * ID of the user who modified the object.
   *
   * @var string
   */
  public $userProfileId;
  /**
   * User profile name of the user who modified the object.
   *
   * @var string
   */
  public $userProfileName;

  /**
   * Account ID of the modified object.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * Action which caused the change.
   *
   * @param string $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return string
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * @param string $changeTime
   */
  public function setChangeTime($changeTime)
  {
    $this->changeTime = $changeTime;
  }
  /**
   * @return string
   */
  public function getChangeTime()
  {
    return $this->changeTime;
  }
  /**
   * Field name of the object which changed.
   *
   * @param string $fieldName
   */
  public function setFieldName($fieldName)
  {
    $this->fieldName = $fieldName;
  }
  /**
   * @return string
   */
  public function getFieldName()
  {
    return $this->fieldName;
  }
  /**
   * ID of this change log.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#changeLog".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * New value of the object field.
   *
   * @param string $newValue
   */
  public function setNewValue($newValue)
  {
    $this->newValue = $newValue;
  }
  /**
   * @return string
   */
  public function getNewValue()
  {
    return $this->newValue;
  }
  /**
   * ID of the object of this change log. The object could be a campaign,
   * placement, ad, or other type.
   *
   * @param string $objectId
   */
  public function setObjectId($objectId)
  {
    $this->objectId = $objectId;
  }
  /**
   * @return string
   */
  public function getObjectId()
  {
    return $this->objectId;
  }
  /**
   * Object type of the change log.
   *
   * @param string $objectType
   */
  public function setObjectType($objectType)
  {
    $this->objectType = $objectType;
  }
  /**
   * @return string
   */
  public function getObjectType()
  {
    return $this->objectType;
  }
  /**
   * Old value of the object field.
   *
   * @param string $oldValue
   */
  public function setOldValue($oldValue)
  {
    $this->oldValue = $oldValue;
  }
  /**
   * @return string
   */
  public function getOldValue()
  {
    return $this->oldValue;
  }
  /**
   * Subaccount ID of the modified object.
   *
   * @param string $subaccountId
   */
  public function setSubaccountId($subaccountId)
  {
    $this->subaccountId = $subaccountId;
  }
  /**
   * @return string
   */
  public function getSubaccountId()
  {
    return $this->subaccountId;
  }
  /**
   * Transaction ID of this change log. When a single API call results in many
   * changes, each change will have a separate ID in the change log but will
   * share the same transactionId.
   *
   * @param string $transactionId
   */
  public function setTransactionId($transactionId)
  {
    $this->transactionId = $transactionId;
  }
  /**
   * @return string
   */
  public function getTransactionId()
  {
    return $this->transactionId;
  }
  /**
   * ID of the user who modified the object.
   *
   * @param string $userProfileId
   */
  public function setUserProfileId($userProfileId)
  {
    $this->userProfileId = $userProfileId;
  }
  /**
   * @return string
   */
  public function getUserProfileId()
  {
    return $this->userProfileId;
  }
  /**
   * User profile name of the user who modified the object.
   *
   * @param string $userProfileName
   */
  public function setUserProfileName($userProfileName)
  {
    $this->userProfileName = $userProfileName;
  }
  /**
   * @return string
   */
  public function getUserProfileName()
  {
    return $this->userProfileName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChangeLog::class, 'Google_Service_Dfareporting_ChangeLog');
