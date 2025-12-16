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

namespace Google\Service\Drive;

class AccessProposalRoleAndView extends \Google\Model
{
  /**
   * The role that was proposed by the requester. The supported values are: *
   * `writer` * `commenter` * `reader`
   *
   * @var string
   */
  public $role;
  /**
   * Indicates the view for this access proposal. Only populated for proposals
   * that belong to a view. Only `published` is supported.
   *
   * @var string
   */
  public $view;

  /**
   * The role that was proposed by the requester. The supported values are: *
   * `writer` * `commenter` * `reader`
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
   * Indicates the view for this access proposal. Only populated for proposals
   * that belong to a view. Only `published` is supported.
   *
   * @param string $view
   */
  public function setView($view)
  {
    $this->view = $view;
  }
  /**
   * @return string
   */
  public function getView()
  {
    return $this->view;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccessProposalRoleAndView::class, 'Google_Service_Drive_AccessProposalRoleAndView');
