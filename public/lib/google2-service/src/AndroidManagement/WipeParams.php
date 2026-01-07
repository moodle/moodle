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

namespace Google\Service\AndroidManagement;

class WipeParams extends \Google\Collection
{
  protected $collection_key = 'wipeDataFlags';
  /**
   * Optional. Flags to determine what data to wipe.
   *
   * @var string[]
   */
  public $wipeDataFlags;
  protected $wipeReasonType = UserFacingMessage::class;
  protected $wipeReasonDataType = '';

  /**
   * Optional. Flags to determine what data to wipe.
   *
   * @param string[] $wipeDataFlags
   */
  public function setWipeDataFlags($wipeDataFlags)
  {
    $this->wipeDataFlags = $wipeDataFlags;
  }
  /**
   * @return string[]
   */
  public function getWipeDataFlags()
  {
    return $this->wipeDataFlags;
  }
  /**
   * Optional. A short message displayed to the user before wiping the work
   * profile on personal devices. This has no effect on company owned devices.
   * The maximum message length is 200 characters.
   *
   * @param UserFacingMessage $wipeReason
   */
  public function setWipeReason(UserFacingMessage $wipeReason)
  {
    $this->wipeReason = $wipeReason;
  }
  /**
   * @return UserFacingMessage
   */
  public function getWipeReason()
  {
    return $this->wipeReason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WipeParams::class, 'Google_Service_AndroidManagement_WipeParams');
