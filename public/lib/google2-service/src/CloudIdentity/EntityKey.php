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

class EntityKey extends \Google\Model
{
  /**
   * The ID of the entity. For Google-managed entities, the `id` should be the
   * email address of an existing group or user. Email addresses need to adhere
   * to [name guidelines for users and
   * groups](https://support.google.com/a/answer/9193374). For external-
   * identity-mapped entities, the `id` must be a string conforming to the
   * Identity Source's requirements. Must be unique within a `namespace`.
   *
   * @var string
   */
  public $id;
  /**
   * The namespace in which the entity exists. If not specified, the `EntityKey`
   * represents a Google-managed entity such as a Google user or a Google Group.
   * If specified, the `EntityKey` represents an external-identity-mapped group.
   * The namespace must correspond to an identity source created in Admin
   * Console and must be in the form of `identitysources/{identity_source}`.
   *
   * @var string
   */
  public $namespace;

  /**
   * The ID of the entity. For Google-managed entities, the `id` should be the
   * email address of an existing group or user. Email addresses need to adhere
   * to [name guidelines for users and
   * groups](https://support.google.com/a/answer/9193374). For external-
   * identity-mapped entities, the `id` must be a string conforming to the
   * Identity Source's requirements. Must be unique within a `namespace`.
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
   * The namespace in which the entity exists. If not specified, the `EntityKey`
   * represents a Google-managed entity such as a Google user or a Google Group.
   * If specified, the `EntityKey` represents an external-identity-mapped group.
   * The namespace must correspond to an identity source created in Admin
   * Console and must be in the form of `identitysources/{identity_source}`.
   *
   * @param string $namespace
   */
  public function setNamespace($namespace)
  {
    $this->namespace = $namespace;
  }
  /**
   * @return string
   */
  public function getNamespace()
  {
    return $this->namespace;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EntityKey::class, 'Google_Service_CloudIdentity_EntityKey');
