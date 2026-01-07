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

namespace Google\Service\CloudResourceManager;

class Lien extends \Google\Collection
{
  protected $collection_key = 'restrictions';
  /**
   * The creation time of this Lien.
   *
   * @var string
   */
  public $createTime;
  /**
   * A system-generated unique identifier for this Lien. Example:
   * `liens/1234abcd`
   *
   * @var string
   */
  public $name;
  /**
   * A stable, user-visible/meaningful string identifying the origin of the
   * Lien, intended to be inspected programmatically. Maximum length of 200
   * characters. Example: 'compute.googleapis.com'
   *
   * @var string
   */
  public $origin;
  /**
   * A reference to the resource this Lien is attached to. The server will
   * validate the parent against those for which Liens are supported. Example:
   * `projects/1234`
   *
   * @var string
   */
  public $parent;
  /**
   * Concise user-visible strings indicating why an action cannot be performed
   * on a resource. Maximum length of 200 characters. Example: 'Holds production
   * API key'
   *
   * @var string
   */
  public $reason;
  /**
   * The types of operations which should be blocked as a result of this Lien.
   * Each value should correspond to an IAM permission. The server will validate
   * the permissions against those for which Liens are supported. An empty list
   * is meaningless and will be rejected. Example:
   * ['resourcemanager.projects.delete']
   *
   * @var string[]
   */
  public $restrictions;

  /**
   * The creation time of this Lien.
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
   * A system-generated unique identifier for this Lien. Example:
   * `liens/1234abcd`
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
   * A stable, user-visible/meaningful string identifying the origin of the
   * Lien, intended to be inspected programmatically. Maximum length of 200
   * characters. Example: 'compute.googleapis.com'
   *
   * @param string $origin
   */
  public function setOrigin($origin)
  {
    $this->origin = $origin;
  }
  /**
   * @return string
   */
  public function getOrigin()
  {
    return $this->origin;
  }
  /**
   * A reference to the resource this Lien is attached to. The server will
   * validate the parent against those for which Liens are supported. Example:
   * `projects/1234`
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * Concise user-visible strings indicating why an action cannot be performed
   * on a resource. Maximum length of 200 characters. Example: 'Holds production
   * API key'
   *
   * @param string $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return string
   */
  public function getReason()
  {
    return $this->reason;
  }
  /**
   * The types of operations which should be blocked as a result of this Lien.
   * Each value should correspond to an IAM permission. The server will validate
   * the permissions against those for which Liens are supported. An empty list
   * is meaningless and will be rejected. Example:
   * ['resourcemanager.projects.delete']
   *
   * @param string[] $restrictions
   */
  public function setRestrictions($restrictions)
  {
    $this->restrictions = $restrictions;
  }
  /**
   * @return string[]
   */
  public function getRestrictions()
  {
    return $this->restrictions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Lien::class, 'Google_Service_CloudResourceManager_Lien');
