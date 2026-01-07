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

namespace Google\Service\AdExchangeBuyerII;

class Note extends \Google\Model
{
  /**
   * A placeholder for an undefined buyer/seller role.
   */
  public const CREATOR_ROLE_BUYER_SELLER_ROLE_UNSPECIFIED = 'BUYER_SELLER_ROLE_UNSPECIFIED';
  /**
   * Specifies the role as buyer.
   */
  public const CREATOR_ROLE_BUYER = 'BUYER';
  /**
   * Specifies the role as seller.
   */
  public const CREATOR_ROLE_SELLER = 'SELLER';
  /**
   * Output only. The timestamp for when this note was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The role of the person (buyer/seller) creating the note.
   *
   * @var string
   */
  public $creatorRole;
  /**
   * The actual note to attach. (max-length: 1024 unicode code units) Note: This
   * field may be set only when creating the resource. Modifying this field
   * while updating the resource will result in an error.
   *
   * @var string
   */
  public $note;
  /**
   * Output only. The unique ID for the note.
   *
   * @var string
   */
  public $noteId;
  /**
   * Output only. The revision number of the proposal when the note is created.
   *
   * @var string
   */
  public $proposalRevision;

  /**
   * Output only. The timestamp for when this note was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. The role of the person (buyer/seller) creating the note.
   *
   * Accepted values: BUYER_SELLER_ROLE_UNSPECIFIED, BUYER, SELLER
   *
   * @param self::CREATOR_ROLE_* $creatorRole
   */
  public function setCreatorRole($creatorRole)
  {
    $this->creatorRole = $creatorRole;
  }
  /**
   * @return self::CREATOR_ROLE_*
   */
  public function getCreatorRole()
  {
    return $this->creatorRole;
  }
  /**
   * The actual note to attach. (max-length: 1024 unicode code units) Note: This
   * field may be set only when creating the resource. Modifying this field
   * while updating the resource will result in an error.
   *
   * @param string $note
   */
  public function setNote($note)
  {
    $this->note = $note;
  }
  /**
   * @return string
   */
  public function getNote()
  {
    return $this->note;
  }
  /**
   * Output only. The unique ID for the note.
   *
   * @param string $noteId
   */
  public function setNoteId($noteId)
  {
    $this->noteId = $noteId;
  }
  /**
   * @return string
   */
  public function getNoteId()
  {
    return $this->noteId;
  }
  /**
   * Output only. The revision number of the proposal when the note is created.
   *
   * @param string $proposalRevision
   */
  public function setProposalRevision($proposalRevision)
  {
    $this->proposalRevision = $proposalRevision;
  }
  /**
   * @return string
   */
  public function getProposalRevision()
  {
    return $this->proposalRevision;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Note::class, 'Google_Service_AdExchangeBuyerII_Note');
