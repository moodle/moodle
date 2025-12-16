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

class UpdateAuthorizedViewRequest extends \Google\Model
{
  protected $authorizedViewType = AuthorizedView::class;
  protected $authorizedViewDataType = '';
  /**
   * Optional. If true, ignore the safety checks when updating the
   * AuthorizedView.
   *
   * @var bool
   */
  public $ignoreWarnings;
  /**
   * Optional. The list of fields to update. A mask specifying which fields in
   * the AuthorizedView resource should be updated. This mask is relative to the
   * AuthorizedView resource, not to the request message. A field will be
   * overwritten if it is in the mask. If empty, all fields set in the request
   * will be overwritten. A special value `*` means to overwrite all fields
   * (including fields not set in the request).
   *
   * @var string
   */
  public $updateMask;

  /**
   * Required. The AuthorizedView to update. The `name` in `authorized_view` is
   * used to identify the AuthorizedView. AuthorizedView name must in this
   * format: `projects/{project}/instances/{instance}/tables/{table}/authorizedV
   * iews/{authorized_view}`.
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
   * Optional. If true, ignore the safety checks when updating the
   * AuthorizedView.
   *
   * @param bool $ignoreWarnings
   */
  public function setIgnoreWarnings($ignoreWarnings)
  {
    $this->ignoreWarnings = $ignoreWarnings;
  }
  /**
   * @return bool
   */
  public function getIgnoreWarnings()
  {
    return $this->ignoreWarnings;
  }
  /**
   * Optional. The list of fields to update. A mask specifying which fields in
   * the AuthorizedView resource should be updated. This mask is relative to the
   * AuthorizedView resource, not to the request message. A field will be
   * overwritten if it is in the mask. If empty, all fields set in the request
   * will be overwritten. A special value `*` means to overwrite all fields
   * (including fields not set in the request).
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateAuthorizedViewRequest::class, 'Google_Service_BigtableAdmin_UpdateAuthorizedViewRequest');
