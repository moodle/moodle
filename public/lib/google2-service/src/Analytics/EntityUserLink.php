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

namespace Google\Service\Analytics;

class EntityUserLink extends \Google\Model
{
  protected $entityType = EntityUserLinkEntity::class;
  protected $entityDataType = '';
  /**
   * Entity user link ID
   *
   * @var string
   */
  public $id;
  /**
   * Resource type for entity user link.
   *
   * @var string
   */
  public $kind;
  protected $permissionsType = EntityUserLinkPermissions::class;
  protected $permissionsDataType = '';
  /**
   * Self link for this resource.
   *
   * @var string
   */
  public $selfLink;
  protected $userRefType = UserRef::class;
  protected $userRefDataType = '';

  /**
   * Entity for this link. It can be an account, a web property, or a view
   * (profile).
   *
   * @param EntityUserLinkEntity $entity
   */
  public function setEntity(EntityUserLinkEntity $entity)
  {
    $this->entity = $entity;
  }
  /**
   * @return EntityUserLinkEntity
   */
  public function getEntity()
  {
    return $this->entity;
  }
  /**
   * Entity user link ID
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
   * Resource type for entity user link.
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
   * Permissions the user has for this entity.
   *
   * @param EntityUserLinkPermissions $permissions
   */
  public function setPermissions(EntityUserLinkPermissions $permissions)
  {
    $this->permissions = $permissions;
  }
  /**
   * @return EntityUserLinkPermissions
   */
  public function getPermissions()
  {
    return $this->permissions;
  }
  /**
   * Self link for this resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * User reference.
   *
   * @param UserRef $userRef
   */
  public function setUserRef(UserRef $userRef)
  {
    $this->userRef = $userRef;
  }
  /**
   * @return UserRef
   */
  public function getUserRef()
  {
    return $this->userRef;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EntityUserLink::class, 'Google_Service_Analytics_EntityUserLink');
