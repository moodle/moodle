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

namespace Google\Service\Storage;

class BucketAccessControl extends \Google\Model
{
  /**
   * The name of the bucket.
   *
   * @var string
   */
  public $bucket;
  /**
   * The domain associated with the entity, if any.
   *
   * @var string
   */
  public $domain;
  /**
   * The email address associated with the entity, if any.
   *
   * @var string
   */
  public $email;
  /**
   * The entity holding the permission, in one of the following forms: - user-
   * userId  - user-email  - group-groupId  - group-email  - domain-domain  -
   * project-team-projectId  - allUsers  - allAuthenticatedUsers Examples:  -
   * The user liz@example.com would be user-liz@example.com.  - The group
   * example@googlegroups.com would be group-example@googlegroups.com.  - To
   * refer to all members of the Google Apps for Business domain example.com,
   * the entity would be domain-example.com.
   *
   * @var string
   */
  public $entity;
  /**
   * The ID for the entity, if any.
   *
   * @var string
   */
  public $entityId;
  /**
   * HTTP 1.1 Entity tag for the access-control entry.
   *
   * @var string
   */
  public $etag;
  /**
   * The ID of the access-control entry.
   *
   * @var string
   */
  public $id;
  /**
   * The kind of item this is. For bucket access control entries, this is always
   * storage#bucketAccessControl.
   *
   * @var string
   */
  public $kind;
  protected $projectTeamType = BucketAccessControlProjectTeam::class;
  protected $projectTeamDataType = '';
  /**
   * The access permission for the entity.
   *
   * @var string
   */
  public $role;
  /**
   * The link to this access-control entry.
   *
   * @var string
   */
  public $selfLink;

  /**
   * The name of the bucket.
   *
   * @param string $bucket
   */
  public function setBucket($bucket)
  {
    $this->bucket = $bucket;
  }
  /**
   * @return string
   */
  public function getBucket()
  {
    return $this->bucket;
  }
  /**
   * The domain associated with the entity, if any.
   *
   * @param string $domain
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * The email address associated with the entity, if any.
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
   * The entity holding the permission, in one of the following forms: - user-
   * userId  - user-email  - group-groupId  - group-email  - domain-domain  -
   * project-team-projectId  - allUsers  - allAuthenticatedUsers Examples:  -
   * The user liz@example.com would be user-liz@example.com.  - The group
   * example@googlegroups.com would be group-example@googlegroups.com.  - To
   * refer to all members of the Google Apps for Business domain example.com,
   * the entity would be domain-example.com.
   *
   * @param string $entity
   */
  public function setEntity($entity)
  {
    $this->entity = $entity;
  }
  /**
   * @return string
   */
  public function getEntity()
  {
    return $this->entity;
  }
  /**
   * The ID for the entity, if any.
   *
   * @param string $entityId
   */
  public function setEntityId($entityId)
  {
    $this->entityId = $entityId;
  }
  /**
   * @return string
   */
  public function getEntityId()
  {
    return $this->entityId;
  }
  /**
   * HTTP 1.1 Entity tag for the access-control entry.
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
   * The ID of the access-control entry.
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
   * The kind of item this is. For bucket access control entries, this is always
   * storage#bucketAccessControl.
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
   * The project team associated with the entity, if any.
   *
   * @param BucketAccessControlProjectTeam $projectTeam
   */
  public function setProjectTeam(BucketAccessControlProjectTeam $projectTeam)
  {
    $this->projectTeam = $projectTeam;
  }
  /**
   * @return BucketAccessControlProjectTeam
   */
  public function getProjectTeam()
  {
    return $this->projectTeam;
  }
  /**
   * The access permission for the entity.
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
   * The link to this access-control entry.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BucketAccessControl::class, 'Google_Service_Storage_BucketAccessControl');
