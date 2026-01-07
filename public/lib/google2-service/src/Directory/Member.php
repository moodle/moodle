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

class Member extends \Google\Model
{
  protected $internal_gapi_mappings = [
        "deliverySettings" => "delivery_settings",
  ];
  /**
   * Defines mail delivery preferences of member. This field is only supported
   * by `insert`, `update`, and `get` methods.
   *
   * @var string
   */
  public $deliverySettings;
  /**
   * The member's email address. A member can be a user or another group. This
   * property is required when adding a member to a group. The `email` must be
   * unique and cannot be an alias of another group. If the email address is
   * changed, the API automatically reflects the email address changes.
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
   * The unique ID of the group member. A member `id` can be used as a member
   * request URI's `memberKey`.
   *
   * @var string
   */
  public $id;
  /**
   * The type of the API resource. For Members resources, the value is
   * `admin#directory#member`.
   *
   * @var string
   */
  public $kind;
  /**
   * The member's role in a group. The API returns an error for cycles in group
   * memberships. For example, if `group1` is a member of `group2`, `group2`
   * cannot be a member of `group1`. For more information about a member's role,
   * see the [administration help
   * center](https://support.google.com/a/answer/167094).
   *
   * @var string
   */
  public $role;
  /**
   * Status of member (Immutable)
   *
   * @var string
   */
  public $status;
  /**
   * The type of group member.
   *
   * @var string
   */
  public $type;

  /**
   * Defines mail delivery preferences of member. This field is only supported
   * by `insert`, `update`, and `get` methods.
   *
   * @param string $deliverySettings
   */
  public function setDeliverySettings($deliverySettings)
  {
    $this->deliverySettings = $deliverySettings;
  }
  /**
   * @return string
   */
  public function getDeliverySettings()
  {
    return $this->deliverySettings;
  }
  /**
   * The member's email address. A member can be a user or another group. This
   * property is required when adding a member to a group. The `email` must be
   * unique and cannot be an alias of another group. If the email address is
   * changed, the API automatically reflects the email address changes.
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
   * The unique ID of the group member. A member `id` can be used as a member
   * request URI's `memberKey`.
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
   * The type of the API resource. For Members resources, the value is
   * `admin#directory#member`.
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
   * The member's role in a group. The API returns an error for cycles in group
   * memberships. For example, if `group1` is a member of `group2`, `group2`
   * cannot be a member of `group1`. For more information about a member's role,
   * see the [administration help
   * center](https://support.google.com/a/answer/167094).
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
   * Status of member (Immutable)
   *
   * @param string $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return string
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * The type of group member.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Member::class, 'Google_Service_Directory_Member');
