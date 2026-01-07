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

class MemberRelation extends \Google\Collection
{
  /**
   * The relation type is undefined or undetermined.
   */
  public const RELATION_TYPE_RELATION_TYPE_UNSPECIFIED = 'RELATION_TYPE_UNSPECIFIED';
  /**
   * The two entities have only a direct membership with each other.
   */
  public const RELATION_TYPE_DIRECT = 'DIRECT';
  /**
   * The two entities have only an indirect membership with each other.
   */
  public const RELATION_TYPE_INDIRECT = 'INDIRECT';
  /**
   * The two entities have both a direct and an indirect membership with each
   * other.
   */
  public const RELATION_TYPE_DIRECT_AND_INDIRECT = 'DIRECT_AND_INDIRECT';
  protected $collection_key = 'roles';
  /**
   * Resource name for this member.
   *
   * @var string
   */
  public $member;
  protected $preferredMemberKeyType = EntityKey::class;
  protected $preferredMemberKeyDataType = 'array';
  /**
   * The relation between the group and the transitive member.
   *
   * @var string
   */
  public $relationType;
  protected $rolesType = TransitiveMembershipRole::class;
  protected $rolesDataType = 'array';

  /**
   * Resource name for this member.
   *
   * @param string $member
   */
  public function setMember($member)
  {
    $this->member = $member;
  }
  /**
   * @return string
   */
  public function getMember()
  {
    return $this->member;
  }
  /**
   * Entity key has an id and a namespace. In case of discussion forums, the id
   * will be an email address without a namespace.
   *
   * @param EntityKey[] $preferredMemberKey
   */
  public function setPreferredMemberKey($preferredMemberKey)
  {
    $this->preferredMemberKey = $preferredMemberKey;
  }
  /**
   * @return EntityKey[]
   */
  public function getPreferredMemberKey()
  {
    return $this->preferredMemberKey;
  }
  /**
   * The relation between the group and the transitive member.
   *
   * Accepted values: RELATION_TYPE_UNSPECIFIED, DIRECT, INDIRECT,
   * DIRECT_AND_INDIRECT
   *
   * @param self::RELATION_TYPE_* $relationType
   */
  public function setRelationType($relationType)
  {
    $this->relationType = $relationType;
  }
  /**
   * @return self::RELATION_TYPE_*
   */
  public function getRelationType()
  {
    return $this->relationType;
  }
  /**
   * The membership role details (i.e name of role and expiry time).
   *
   * @param TransitiveMembershipRole[] $roles
   */
  public function setRoles($roles)
  {
    $this->roles = $roles;
  }
  /**
   * @return TransitiveMembershipRole[]
   */
  public function getRoles()
  {
    return $this->roles;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MemberRelation::class, 'Google_Service_CloudIdentity_MemberRelation');
