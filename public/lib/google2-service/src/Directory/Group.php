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

namespace Google\Service\Directory;

class Group extends \Google\Collection
{
  protected $collection_key = 'nonEditableAliases';
  /**
   * Read-only. Value is `true` if this group was created by an administrator
   * rather than a user.
   *
   * @var bool
   */
  public $adminCreated;
  /**
   * Read-only. The list of a group's alias email addresses. To add, update, or
   * remove a group's aliases, use the `groups.aliases` methods. If edited in a
   * group's POST or PUT request, the edit is ignored.
   *
   * @var string[]
   */
  public $aliases;
  /**
   * An extended description to help users determine the purpose of a group. For
   * example, you can include information about who should join the group, the
   * types of messages to send to the group, links to FAQs about the group, or
   * related groups. Maximum length is `4,096` characters.
   *
   * @var string
   */
  public $description;
  /**
   * The number of users that are direct members of the group. If a group is a
   * member (child) of this group (the parent), members of the child group are
   * not counted in the `directMembersCount` property of the parent group.
   *
   * @var string
   */
  public $directMembersCount;
  /**
   * The group's email address. If your account has multiple domains, select the
   * appropriate domain for the email address. The `email` must be unique. This
   * property is required when creating a group. Group email addresses are
   * subject to the same character usage rules as usernames, see the [help
   * center](https://support.google.com/a/answer/9193374) for details.
   *
   * @var string
   */
  public $email;
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Read-only. The unique ID of a group. A group `id` can be used as a group
   * request URI's `groupKey`.
   *
   * @var string
   */
  public $id;
  /**
   * The type of the API resource. For Groups resources, the value is
   * `admin#directory#group`.
   *
   * @var string
   */
  public $kind;
  /**
   * The group's display name.
   *
   * @var string
   */
  public $name;
  /**
   * Read-only. The list of the group's non-editable alias email addresses that
   * are outside of the account's primary domain or subdomains. These are
   * functioning email addresses used by the group. This is a read-only property
   * returned in the API's response for a group. If edited in a group's POST or
   * PUT request, the edit is ignored.
   *
   * @var string[]
   */
  public $nonEditableAliases;

  /**
   * Read-only. Value is `true` if this group was created by an administrator
   * rather than a user.
   *
   * @param bool $adminCreated
   */
  public function setAdminCreated($adminCreated)
  {
    $this->adminCreated = $adminCreated;
  }
  /**
   * @return bool
   */
  public function getAdminCreated()
  {
    return $this->adminCreated;
  }
  /**
   * Read-only. The list of a group's alias email addresses. To add, update, or
   * remove a group's aliases, use the `groups.aliases` methods. If edited in a
   * group's POST or PUT request, the edit is ignored.
   *
   * @param string[] $aliases
   */
  public function setAliases($aliases)
  {
    $this->aliases = $aliases;
  }
  /**
   * @return string[]
   */
  public function getAliases()
  {
    return $this->aliases;
  }
  /**
   * An extended description to help users determine the purpose of a group. For
   * example, you can include information about who should join the group, the
   * types of messages to send to the group, links to FAQs about the group, or
   * related groups. Maximum length is `4,096` characters.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The number of users that are direct members of the group. If a group is a
   * member (child) of this group (the parent), members of the child group are
   * not counted in the `directMembersCount` property of the parent group.
   *
   * @param string $directMembersCount
   */
  public function setDirectMembersCount($directMembersCount)
  {
    $this->directMembersCount = $directMembersCount;
  }
  /**
   * @return string
   */
  public function getDirectMembersCount()
  {
    return $this->directMembersCount;
  }
  /**
   * The group's email address. If your account has multiple domains, select the
   * appropriate domain for the email address. The `email` must be unique. This
   * property is required when creating a group. Group email addresses are
   * subject to the same character usage rules as usernames, see the [help
   * center](https://support.google.com/a/answer/9193374) for details.
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
   * Read-only. The unique ID of a group. A group `id` can be used as a group
   * request URI's `groupKey`.
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
   * The type of the API resource. For Groups resources, the value is
   * `admin#directory#group`.
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
   * The group's display name.
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
  /**
   * Read-only. The list of the group's non-editable alias email addresses that
   * are outside of the account's primary domain or subdomains. These are
   * functioning email addresses used by the group. This is a read-only property
   * returned in the API's response for a group. If edited in a group's POST or
   * PUT request, the edit is ignored.
   *
   * @param string[] $nonEditableAliases
   */
  public function setNonEditableAliases($nonEditableAliases)
  {
    $this->nonEditableAliases = $nonEditableAliases;
  }
  /**
   * @return string[]
   */
  public function getNonEditableAliases()
  {
    return $this->nonEditableAliases;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Group::class, 'Google_Service_Directory_Group');
