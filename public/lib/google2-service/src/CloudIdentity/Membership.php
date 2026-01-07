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

namespace Google\Service\CloudIdentity;

class Membership extends \Google\Collection
{
  /**
   * Default. Should not be used.
   */
  public const DELIVERY_SETTING_DELIVERY_SETTING_UNSPECIFIED = 'DELIVERY_SETTING_UNSPECIFIED';
  /**
   * Represents each mail should be delivered
   */
  public const DELIVERY_SETTING_ALL_MAIL = 'ALL_MAIL';
  /**
   * Represents 1 email for every 25 messages.
   */
  public const DELIVERY_SETTING_DIGEST = 'DIGEST';
  /**
   * Represents daily summary of messages.
   */
  public const DELIVERY_SETTING_DAILY = 'DAILY';
  /**
   * Represents no delivery.
   */
  public const DELIVERY_SETTING_NONE = 'NONE';
  /**
   * Represents disabled state.
   */
  public const DELIVERY_SETTING_DISABLED = 'DISABLED';
  /**
   * Default. Should not be used.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Represents user type.
   */
  public const TYPE_USER = 'USER';
  /**
   * Represents service account type.
   */
  public const TYPE_SERVICE_ACCOUNT = 'SERVICE_ACCOUNT';
  /**
   * Represents group type.
   */
  public const TYPE_GROUP = 'GROUP';
  /**
   * Represents Shared drive.
   */
  public const TYPE_SHARED_DRIVE = 'SHARED_DRIVE';
  /**
   * Represents a CBCM-managed Chrome Browser type.
   */
  public const TYPE_CBCM_BROWSER = 'CBCM_BROWSER';
  /**
   * Represents other type.
   */
  public const TYPE_OTHER = 'OTHER';
  protected $collection_key = 'roles';
  /**
   * Output only. The time when the `Membership` was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Delivery setting associated with the membership.
   *
   * @var string
   */
  public $deliverySetting;
  /**
   * Output only. The [resource
   * name](https://cloud.google.com/apis/design/resource_names) of the
   * `Membership`. Shall be of the form
   * `groups/{group}/memberships/{membership}`.
   *
   * @var string
   */
  public $name;
  protected $preferredMemberKeyType = EntityKey::class;
  protected $preferredMemberKeyDataType = '';
  protected $rolesType = MembershipRole::class;
  protected $rolesDataType = 'array';
  /**
   * Output only. The type of the membership.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. The time when the `Membership` was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time when the `Membership` was created.
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
   * Output only. Delivery setting associated with the membership.
   *
   * Accepted values: DELIVERY_SETTING_UNSPECIFIED, ALL_MAIL, DIGEST, DAILY,
   * NONE, DISABLED
   *
   * @param self::DELIVERY_SETTING_* $deliverySetting
   */
  public function setDeliverySetting($deliverySetting)
  {
    $this->deliverySetting = $deliverySetting;
  }
  /**
   * @return self::DELIVERY_SETTING_*
   */
  public function getDeliverySetting()
  {
    return $this->deliverySetting;
  }
  /**
   * Output only. The [resource
   * name](https://cloud.google.com/apis/design/resource_names) of the
   * `Membership`. Shall be of the form
   * `groups/{group}/memberships/{membership}`.
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
   * Required. Immutable. The `EntityKey` of the member.
   *
   * @param EntityKey $preferredMemberKey
   */
  public function setPreferredMemberKey(EntityKey $preferredMemberKey)
  {
    $this->preferredMemberKey = $preferredMemberKey;
  }
  /**
   * @return EntityKey
   */
  public function getPreferredMemberKey()
  {
    return $this->preferredMemberKey;
  }
  /**
   * The `MembershipRole`s that apply to the `Membership`. If unspecified,
   * defaults to a single `MembershipRole` with `name` `MEMBER`. Must not
   * contain duplicate `MembershipRole`s with the same `name`.
   *
   * @param MembershipRole[] $roles
   */
  public function setRoles($roles)
  {
    $this->roles = $roles;
  }
  /**
   * @return MembershipRole[]
   */
  public function getRoles()
  {
    return $this->roles;
  }
  /**
   * Output only. The type of the membership.
   *
   * Accepted values: TYPE_UNSPECIFIED, USER, SERVICE_ACCOUNT, GROUP,
   * SHARED_DRIVE, CBCM_BROWSER, OTHER
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. The time when the `Membership` was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Membership::class, 'Google_Service_CloudIdentity_Membership');
