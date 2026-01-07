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

class GroupAlias extends \Google\Model
{
  /**
   * The alias email address.
   *
   * @var string
   */
  public $alias;
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * The unique ID of the group.
   *
   * @var string
   */
  public $id;
  /**
   * The type of the API resource. For Alias resources, the value is
   * `admin#directory#alias`.
   *
   * @var string
   */
  public $kind;
  /**
   * The primary email address of the group.
   *
   * @var string
   */
  public $primaryEmail;

  /**
   * The alias email address.
   *
   * @param string $alias
   */
  public function setAlias($alias)
  {
    $this->alias = $alias;
  }
  /**
   * @return string
   */
  public function getAlias()
  {
    return $this->alias;
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
   * The unique ID of the group.
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
   * The type of the API resource. For Alias resources, the value is
   * `admin#directory#alias`.
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
   * The primary email address of the group.
   *
   * @param string $primaryEmail
   */
  public function setPrimaryEmail($primaryEmail)
  {
    $this->primaryEmail = $primaryEmail;
  }
  /**
   * @return string
   */
  public function getPrimaryEmail()
  {
    return $this->primaryEmail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GroupAlias::class, 'Google_Service_Directory_GroupAlias');
