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

namespace Google\Service\ManagedKafka;

class RemoveAclEntryResponse extends \Google\Model
{
  protected $aclType = Acl::class;
  protected $aclDataType = '';
  /**
   * Returned with value true if the removed acl entry was the last entry in the
   * acl, resulting in acl deletion.
   *
   * @var bool
   */
  public $aclDeleted;

  /**
   * The updated acl. Returned if the removed acl entry was not the last entry
   * in the acl.
   *
   * @param Acl $acl
   */
  public function setAcl(Acl $acl)
  {
    $this->acl = $acl;
  }
  /**
   * @return Acl
   */
  public function getAcl()
  {
    return $this->acl;
  }
  /**
   * Returned with value true if the removed acl entry was the last entry in the
   * acl, resulting in acl deletion.
   *
   * @param bool $aclDeleted
   */
  public function setAclDeleted($aclDeleted)
  {
    $this->aclDeleted = $aclDeleted;
  }
  /**
   * @return bool
   */
  public function getAclDeleted()
  {
    return $this->aclDeleted;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RemoveAclEntryResponse::class, 'Google_Service_ManagedKafka_RemoveAclEntryResponse');
