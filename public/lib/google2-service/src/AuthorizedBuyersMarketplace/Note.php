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

namespace Google\Service\AuthorizedBuyersMarketplace;

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
   * Output only. When this note was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The role who created the note.
   *
   * @var string
   */
  public $creatorRole;
  /**
   * The text of the note. Maximum length is 1024 characters.
   *
   * @var string
   */
  public $note;

  /**
   * Output only. When this note was created.
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
   * Output only. The role who created the note.
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
   * The text of the note. Maximum length is 1024 characters.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Note::class, 'Google_Service_AuthorizedBuyersMarketplace_Note');
