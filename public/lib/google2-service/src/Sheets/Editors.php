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

namespace Google\Service\Sheets;

class Editors extends \Google\Collection
{
  protected $collection_key = 'users';
  /**
   * True if anyone in the document's domain has edit access to the protected
   * range. Domain protection is only supported on documents within a domain.
   *
   * @var bool
   */
  public $domainUsersCanEdit;
  /**
   * The email addresses of groups with edit access to the protected range.
   *
   * @var string[]
   */
  public $groups;
  /**
   * The email addresses of users with edit access to the protected range.
   *
   * @var string[]
   */
  public $users;

  /**
   * True if anyone in the document's domain has edit access to the protected
   * range. Domain protection is only supported on documents within a domain.
   *
   * @param bool $domainUsersCanEdit
   */
  public function setDomainUsersCanEdit($domainUsersCanEdit)
  {
    $this->domainUsersCanEdit = $domainUsersCanEdit;
  }
  /**
   * @return bool
   */
  public function getDomainUsersCanEdit()
  {
    return $this->domainUsersCanEdit;
  }
  /**
   * The email addresses of groups with edit access to the protected range.
   *
   * @param string[] $groups
   */
  public function setGroups($groups)
  {
    $this->groups = $groups;
  }
  /**
   * @return string[]
   */
  public function getGroups()
  {
    return $this->groups;
  }
  /**
   * The email addresses of users with edit access to the protected range.
   *
   * @param string[] $users
   */
  public function setUsers($users)
  {
    $this->users = $users;
  }
  /**
   * @return string[]
   */
  public function getUsers()
  {
    return $this->users;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Editors::class, 'Google_Service_Sheets_Editors');
