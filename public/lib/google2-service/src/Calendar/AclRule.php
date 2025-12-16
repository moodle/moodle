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

namespace Google\Service\Calendar;

class AclRule extends \Google\Model
{
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Identifier of the Access Control List (ACL) rule. See Sharing calendars.
   *
   * @var string
   */
  public $id;
  /**
   * Type of the resource ("calendar#aclRule").
   *
   * @var string
   */
  public $kind;
  /**
   * The role assigned to the scope. Possible values are: - "none" - Provides no
   * access.  - "freeBusyReader" - Provides read access to free/busy
   * information.  - "reader" - Provides read access to the calendar. Private
   * events will appear to users with reader access, but event details will be
   * hidden.  - "writer" - Provides read and write access to the calendar.
   * Private events will appear to users with writer access, and event details
   * will be visible. Provides read access to the calendar's ACLs.  - "owner" -
   * Provides manager access to the calendar. This role has all of the
   * permissions of the writer role with the additional ability to modify access
   * levels of other users. Important: the owner role is different from the
   * calendar's data owner. A calendar has a single data owner, but can have
   * multiple users with owner role.
   *
   * @var string
   */
  public $role;
  protected $scopeType = AclRuleScope::class;
  protected $scopeDataType = '';

  /**
   * ETag of the resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Identifier of the Access Control List (ACL) rule. See Sharing calendars.
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
   * Type of the resource ("calendar#aclRule").
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
   * The role assigned to the scope. Possible values are: - "none" - Provides no
   * access.  - "freeBusyReader" - Provides read access to free/busy
   * information.  - "reader" - Provides read access to the calendar. Private
   * events will appear to users with reader access, but event details will be
   * hidden.  - "writer" - Provides read and write access to the calendar.
   * Private events will appear to users with writer access, and event details
   * will be visible. Provides read access to the calendar's ACLs.  - "owner" -
   * Provides manager access to the calendar. This role has all of the
   * permissions of the writer role with the additional ability to modify access
   * levels of other users. Important: the owner role is different from the
   * calendar's data owner. A calendar has a single data owner, but can have
   * multiple users with owner role.
   *
   * @param string $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return string
   */
  public function getRole()
  {
    return $this->role;
  }
  /**
   * The extent to which calendar access is granted by this ACL rule.
   *
   * @param AclRuleScope $scope
   */
  public function setScope(AclRuleScope $scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return AclRuleScope
   */
  public function getScope()
  {
    return $this->scope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AclRule::class, 'Google_Service_Calendar_AclRule');
