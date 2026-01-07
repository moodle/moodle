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

namespace Google\Service\BigtableAdmin;

class CreateAuthorizedViewRequest extends \Google\Model
{
  protected $authorizedViewType = AuthorizedView::class;
  protected $authorizedViewDataType = '';
  /**
   * Required. The id of the AuthorizedView to create. This AuthorizedView must
   * not already exist. The `authorized_view_id` appended to `parent` forms the
   * full AuthorizedView name of the form `projects/{project}/instances/{instanc
   * e}/tables/{table}/authorizedView/{authorized_view}`.
   *
   * @var string
   */
  public $authorizedViewId;
  /**
   * Required. This is the name of the table the AuthorizedView belongs to.
   * Values are of the form
   * `projects/{project}/instances/{instance}/tables/{table}`.
   *
   * @var string
   */
  public $parent;

  /**
   * Required. The AuthorizedView to create.
   *
   * @param AuthorizedView $authorizedView
   */
  public function setAuthorizedView(AuthorizedView $authorizedView)
  {
    $this->authorizedView = $authorizedView;
  }
  /**
   * @return AuthorizedView
   */
  public function getAuthorizedView()
  {
    return $this->authorizedView;
  }
  /**
   * Required. The id of the AuthorizedView to create. This AuthorizedView must
   * not already exist. The `authorized_view_id` appended to `parent` forms the
   * full AuthorizedView name of the form `projects/{project}/instances/{instanc
   * e}/tables/{table}/authorizedView/{authorized_view}`.
   *
   * @param string $authorizedViewId
   */
  public function setAuthorizedViewId($authorizedViewId)
  {
    $this->authorizedViewId = $authorizedViewId;
  }
  /**
   * @return string
   */
  public function getAuthorizedViewId()
  {
    return $this->authorizedViewId;
  }
  /**
   * Required. This is the name of the table the AuthorizedView belongs to.
   * Values are of the form
   * `projects/{project}/instances/{instance}/tables/{table}`.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateAuthorizedViewRequest::class, 'Google_Service_BigtableAdmin_CreateAuthorizedViewRequest');
