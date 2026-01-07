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

class ResolveAccessProposalRequest extends \Google\Collection
{
  /**
   * Unspecified action
   */
  public const ACTION_ACTION_UNSPECIFIED = 'ACTION_UNSPECIFIED';
  /**
   * The user accepts the access proposal. Note: If this action is used, the
   * `role` field must have at least one value.
   */
  public const ACTION_ACCEPT = 'ACCEPT';
  /**
   * The user denies the access proposal.
   */
  public const ACTION_DENY = 'DENY';
  protected $collection_key = 'role';
  /**
   * Required. The action to take on the access proposal.
   *
   * @var string
   */
  public $action;
  /**
   * Optional. The roles that the approver has allowed, if any. For more
   * information, see [Roles and
   * permissions](https://developers.google.com/workspace/drive/api/guides/ref-
   * roles). Note: This field is required for the `ACCEPT` action.
   *
   * @var string[]
   */
  public $role;
  /**
   * Optional. Whether to send an email to the requester when the access
   * proposal is denied or accepted.
   *
   * @var bool
   */
  public $sendNotification;
  /**
   * Optional. Indicates the view for this access proposal. This should only be
   * set when the proposal belongs to a view. Only `published` is supported.
   *
   * @var string
   */
  public $view;

  /**
   * Required. The action to take on the access proposal.
   *
   * Accepted values: ACTION_UNSPECIFIED, ACCEPT, DENY
   *
   * @param self::ACTION_* $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return self::ACTION_*
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Optional. The roles that the approver has allowed, if any. For more
   * information, see [Roles and
   * permissions](https://developers.google.com/workspace/drive/api/guides/ref-
   * roles). Note: This field is required for the `ACCEPT` action.
   *
   * @param string[] $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return string[]
   */
  public function getRole()
  {
    return $this->role;
  }
  /**
   * Optional. Whether to send an email to the requester when the access
   * proposal is denied or accepted.
   *
   * @param bool $sendNotification
   */
  public function setSendNotification($sendNotification)
  {
    $this->sendNotification = $sendNotification;
  }
  /**
   * @return bool
   */
  public function getSendNotification()
  {
    return $this->sendNotification;
  }
  /**
   * Optional. Indicates the view for this access proposal. This should only be
   * set when the proposal belongs to a view. Only `published` is supported.
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
class_alias(ResolveAccessProposalRequest::class, 'Google_Service_Drive_ResolveAccessProposalRequest');
