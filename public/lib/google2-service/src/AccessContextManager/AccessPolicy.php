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

namespace Google\Service\AccessContextManager;

class AccessPolicy extends \Google\Collection
{
  protected $collection_key = 'scopes';
  /**
   * Output only. An opaque identifier for the current version of the
   * `AccessPolicy`. This will always be a strongly validated etag, meaning that
   * two Access Policies will be identical if and only if their etags are
   * identical. Clients should not expect this to be in any specific format.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. Identifier. Resource name of the `AccessPolicy`. Format:
   * `accessPolicies/{access_policy}`
   *
   * @var string
   */
  public $name;
  /**
   * Required. The parent of this `AccessPolicy` in the Cloud Resource
   * Hierarchy. Currently immutable once created. Format:
   * `organizations/{organization_id}`
   *
   * @var string
   */
  public $parent;
  /**
   * The scopes of the AccessPolicy. Scopes define which resources a policy can
   * restrict and where its resources can be referenced. For example, policy A
   * with `scopes=["folders/123"]` has the following behavior: -
   * ServicePerimeter can only restrict projects within `folders/123`. -
   * ServicePerimeter within policy A can only reference access levels defined
   * within policy A. - Only one policy can include a given scope; thus,
   * attempting to create a second policy which includes `folders/123` will
   * result in an error. If no scopes are provided, then any resource within the
   * organization can be restricted. Scopes cannot be modified after a policy is
   * created. Policies can only have a single scope. Format: list of
   * `folders/{folder_number}` or `projects/{project_number}`
   *
   * @var string[]
   */
  public $scopes;
  /**
   * Required. Human readable title. Does not affect behavior.
   *
   * @var string
   */
  public $title;

  /**
   * Output only. An opaque identifier for the current version of the
   * `AccessPolicy`. This will always be a strongly validated etag, meaning that
   * two Access Policies will be identical if and only if their etags are
   * identical. Clients should not expect this to be in any specific format.
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
   * Output only. Identifier. Resource name of the `AccessPolicy`. Format:
   * `accessPolicies/{access_policy}`
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
   * Required. The parent of this `AccessPolicy` in the Cloud Resource
   * Hierarchy. Currently immutable once created. Format:
   * `organizations/{organization_id}`
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
   * The scopes of the AccessPolicy. Scopes define which resources a policy can
   * restrict and where its resources can be referenced. For example, policy A
   * with `scopes=["folders/123"]` has the following behavior: -
   * ServicePerimeter can only restrict projects within `folders/123`. -
   * ServicePerimeter within policy A can only reference access levels defined
   * within policy A. - Only one policy can include a given scope; thus,
   * attempting to create a second policy which includes `folders/123` will
   * result in an error. If no scopes are provided, then any resource within the
   * organization can be restricted. Scopes cannot be modified after a policy is
   * created. Policies can only have a single scope. Format: list of
   * `folders/{folder_number}` or `projects/{project_number}`
   *
   * @param string[] $scopes
   */
  public function setScopes($scopes)
  {
    $this->scopes = $scopes;
  }
  /**
   * @return string[]
   */
  public function getScopes()
  {
    return $this->scopes;
  }
  /**
   * Required. Human readable title. Does not affect behavior.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccessPolicy::class, 'Google_Service_AccessContextManager_AccessPolicy');
