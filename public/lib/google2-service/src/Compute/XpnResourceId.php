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

namespace Google\Service\Compute;

class XpnResourceId extends \Google\Model
{
  public const TYPE_PROJECT = 'PROJECT';
  public const TYPE_XPN_RESOURCE_TYPE_UNSPECIFIED = 'XPN_RESOURCE_TYPE_UNSPECIFIED';
  /**
   * The ID of the service resource. In the case of projects, this field
   * supports project id (e.g., my-project-123) and project number (e.g.
   * 12345678).
   *
   * @var string
   */
  public $id;
  /**
   * The type of the service resource.
   *
   * @var string
   */
  public $type;

  /**
   * The ID of the service resource. In the case of projects, this field
   * supports project id (e.g., my-project-123) and project number (e.g.
   * 12345678).
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
   * The type of the service resource.
   *
   * Accepted values: PROJECT, XPN_RESOURCE_TYPE_UNSPECIFIED
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XpnResourceId::class, 'Google_Service_Compute_XpnResourceId');
