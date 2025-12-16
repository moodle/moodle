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

class TransferError extends \Google\Model
{
  /**
   * Entity type wasn't set.
   */
  public const ENTITY_TYPE_TRANSFER_ENTITY_TYPE_UNSPECIFIED = 'TRANSFER_ENTITY_TYPE_UNSPECIFIED';
  /**
   * Transfer to auto attendant.
   */
  public const ENTITY_TYPE_TRANSFER_AUTO_ATTENDANT = 'TRANSFER_AUTO_ATTENDANT';
  /**
   * Transfer to ring group.
   */
  public const ENTITY_TYPE_TRANSFER_RING_GROUP = 'TRANSFER_RING_GROUP';
  /**
   * Transfer to user.
   */
  public const ENTITY_TYPE_TRANSFER_USER = 'TRANSFER_USER';
  /**
   * Reason wasn't specified.
   */
  public const INVALID_REASON_TRANSFER_INVALID_REASON_UNSPECIFIED = 'TRANSFER_INVALID_REASON_UNSPECIFIED';
  /**
   * The transfer target can't be found—most likely it was deleted.
   */
  public const INVALID_REASON_TRANSFER_TARGET_DELETED = 'TRANSFER_TARGET_DELETED';
  /**
   * The user's Google Voice license was removed.
   */
  public const INVALID_REASON_UNLICENSED = 'UNLICENSED';
  /**
   * The user's Google Workspace account was suspended.
   */
  public const INVALID_REASON_SUSPENDED = 'SUSPENDED';
  /**
   * The transfer target no longer has a phone number. This reason should become
   * deprecated once we support numberless transfer.
   */
  public const INVALID_REASON_NO_PHONE_NUMBER = 'NO_PHONE_NUMBER';
  /**
   * User's email address. This may be unavailable if the entity was deleted.
   *
   * @var string
   */
  public $email;
  /**
   * Type of entity being transferred to. For ring group members, this should
   * always be USER.
   *
   * @var string
   */
  public $entityType;
  /**
   * Ring group or auto attendant ID. Not set for users.
   *
   * @var string
   */
  public $id;
  /**
   * Reason for the error.
   *
   * @var string
   */
  public $invalidReason;
  /**
   * User's full name, or the ring group / auto attendant name. This may be
   * unavailable if the entity was deleted.
   *
   * @var string
   */
  public $name;

  /**
   * User's email address. This may be unavailable if the entity was deleted.
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
  /**
   * Type of entity being transferred to. For ring group members, this should
   * always be USER.
   *
   * Accepted values: TRANSFER_ENTITY_TYPE_UNSPECIFIED, TRANSFER_AUTO_ATTENDANT,
   * TRANSFER_RING_GROUP, TRANSFER_USER
   *
   * @param self::ENTITY_TYPE_* $entityType
   */
  public function setEntityType($entityType)
  {
    $this->entityType = $entityType;
  }
  /**
   * @return self::ENTITY_TYPE_*
   */
  public function getEntityType()
  {
    return $this->entityType;
  }
  /**
   * Ring group or auto attendant ID. Not set for users.
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
   * Reason for the error.
   *
   * Accepted values: TRANSFER_INVALID_REASON_UNSPECIFIED,
   * TRANSFER_TARGET_DELETED, UNLICENSED, SUSPENDED, NO_PHONE_NUMBER
   *
   * @param self::INVALID_REASON_* $invalidReason
   */
  public function setInvalidReason($invalidReason)
  {
    $this->invalidReason = $invalidReason;
  }
  /**
   * @return self::INVALID_REASON_*
   */
  public function getInvalidReason()
  {
    return $this->invalidReason;
  }
  /**
   * User's full name, or the ring group / auto attendant name. This may be
   * unavailable if the entity was deleted.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TransferError::class, 'Google_Service_AlertCenter_TransferError');
