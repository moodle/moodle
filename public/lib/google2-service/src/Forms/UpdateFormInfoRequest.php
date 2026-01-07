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

namespace Google\Service\Forms;

class UpdateFormInfoRequest extends \Google\Model
{
  protected $infoType = Info::class;
  protected $infoDataType = '';
  /**
   * Required. Only values named in this mask are changed. At least one field
   * must be specified. The root `info` is implied and should not be specified.
   * A single `"*"` can be used as short-hand for updating every field.
   *
   * @var string
   */
  public $updateMask;

  /**
   * The info to update.
   *
   * @param Info $info
   */
  public function setInfo(Info $info)
  {
    $this->info = $info;
  }
  /**
   * @return Info
   */
  public function getInfo()
  {
    return $this->info;
  }
  /**
   * Required. Only values named in this mask are changed. At least one field
   * must be specified. The root `info` is implied and should not be specified.
   * A single `"*"` can be used as short-hand for updating every field.
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
class_alias(UpdateFormInfoRequest::class, 'Google_Service_Forms_UpdateFormInfoRequest');
