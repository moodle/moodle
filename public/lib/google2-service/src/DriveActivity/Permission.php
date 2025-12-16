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

namespace Google\Service\DriveActivity;

class Permission extends \Google\Model
{
  /**
   * The role is not available.
   */
  public const ROLE_ROLE_UNSPECIFIED = 'ROLE_UNSPECIFIED';
  /**
   * A role granting full access.
   */
  public const ROLE_OWNER = 'OWNER';
  /**
   * A role granting the ability to manage people and settings.
   */
  public const ROLE_ORGANIZER = 'ORGANIZER';
  /**
   * A role granting the ability to contribute and manage content.
   */
  public const ROLE_FILE_ORGANIZER = 'FILE_ORGANIZER';
  /**
   * A role granting the ability to contribute content. This role is sometimes
   * also known as "writer".
   */
  public const ROLE_EDITOR = 'EDITOR';
  /**
   * A role granting the ability to view and comment on content.
   */
  public const ROLE_COMMENTER = 'COMMENTER';
  /**
   * A role granting the ability to view content. This role is sometimes also
   * known as "reader".
   */
  public const ROLE_VIEWER = 'VIEWER';
  /**
   * A role granting the ability to view content only after it has been
   * published to the web. This role is sometimes also known as "published
   * reader". See https://support.google.com/sites/answer/6372880 for more
   * information.
   */
  public const ROLE_PUBLISHED_VIEWER = 'PUBLISHED_VIEWER';
  /**
   * If true, the item can be discovered (e.g. in the user's "Shared with me"
   * collection) without needing a link to the item.
   *
   * @var bool
   */
  public $allowDiscovery;
  protected $anyoneType = Anyone::class;
  protected $anyoneDataType = '';
  protected $domainType = Domain::class;
  protected $domainDataType = '';
  protected $groupType = Group::class;
  protected $groupDataType = '';
  /**
   * Indicates the [Google Drive permissions
   * role](https://developers.google.com/workspace/drive/web/manage-
   * sharing#roles). The role determines a user's ability to read, write, and
   * comment on items.
   *
   * @var string
   */
  public $role;
  protected $userType = User::class;
  protected $userDataType = '';

  /**
   * If true, the item can be discovered (e.g. in the user's "Shared with me"
   * collection) without needing a link to the item.
   *
   * @param bool $allowDiscovery
   */
  public function setAllowDiscovery($allowDiscovery)
  {
    $this->allowDiscovery = $allowDiscovery;
  }
  /**
   * @return bool
   */
  public function getAllowDiscovery()
  {
    return $this->allowDiscovery;
  }
  /**
   * If set, this permission applies to anyone, even logged out users.
   *
   * @param Anyone $anyone
   */
  public function setAnyone(Anyone $anyone)
  {
    $this->anyone = $anyone;
  }
  /**
   * @return Anyone
   */
  public function getAnyone()
  {
    return $this->anyone;
  }
  /**
   * The domain to whom this permission applies.
   *
   * @param Domain $domain
   */
  public function setDomain(Domain $domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return Domain
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * The group to whom this permission applies.
   *
   * @param Group $group
   */
  public function setGroup(Group $group)
  {
    $this->group = $group;
  }
  /**
   * @return Group
   */
  public function getGroup()
  {
    return $this->group;
  }
  /**
   * Indicates the [Google Drive permissions
   * role](https://developers.google.com/workspace/drive/web/manage-
   * sharing#roles). The role determines a user's ability to read, write, and
   * comment on items.
   *
   * Accepted values: ROLE_UNSPECIFIED, OWNER, ORGANIZER, FILE_ORGANIZER,
   * EDITOR, COMMENTER, VIEWER, PUBLISHED_VIEWER
   *
   * @param self::ROLE_* $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return self::ROLE_*
   */
  public function getRole()
  {
    return $this->role;
  }
  /**
   * The user to whom this permission applies.
   *
   * @param User $user
   */
  public function setUser(User $user)
  {
    $this->user = $user;
  }
  /**
   * @return User
   */
  public function getUser()
  {
    return $this->user;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Permission::class, 'Google_Service_DriveActivity_Permission');
