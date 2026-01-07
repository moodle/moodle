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

class BlockAction extends \Google\Model
{
  /**
   * Unspecified. Defaults to BLOCK_SCOPE_WORK_PROFILE.
   */
  public const BLOCK_SCOPE_BLOCK_SCOPE_UNSPECIFIED = 'BLOCK_SCOPE_UNSPECIFIED';
  /**
   * Block action is only applied to apps in the work profile. Apps in the
   * personal profile are unaffected.
   */
  public const BLOCK_SCOPE_BLOCK_SCOPE_WORK_PROFILE = 'BLOCK_SCOPE_WORK_PROFILE';
  /**
   * Block action is applied to the entire device, including apps in the
   * personal profile.
   */
  public const BLOCK_SCOPE_BLOCK_SCOPE_DEVICE = 'BLOCK_SCOPE_DEVICE';
  /**
   * Number of days the policy is non-compliant before the device or work
   * profile is blocked. To block access immediately, set to 0. blockAfterDays
   * must be less than wipeAfterDays.
   *
   * @var int
   */
  public $blockAfterDays;
  /**
   * Specifies the scope of this BlockAction. Only applicable to devices that
   * are company-owned.
   *
   * @var string
   */
  public $blockScope;

  /**
   * Number of days the policy is non-compliant before the device or work
   * profile is blocked. To block access immediately, set to 0. blockAfterDays
   * must be less than wipeAfterDays.
   *
   * @param int $blockAfterDays
   */
  public function setBlockAfterDays($blockAfterDays)
  {
    $this->blockAfterDays = $blockAfterDays;
  }
  /**
   * @return int
   */
  public function getBlockAfterDays()
  {
    return $this->blockAfterDays;
  }
  /**
   * Specifies the scope of this BlockAction. Only applicable to devices that
   * are company-owned.
   *
   * Accepted values: BLOCK_SCOPE_UNSPECIFIED, BLOCK_SCOPE_WORK_PROFILE,
   * BLOCK_SCOPE_DEVICE
   *
   * @param self::BLOCK_SCOPE_* $blockScope
   */
  public function setBlockScope($blockScope)
  {
    $this->blockScope = $blockScope;
  }
  /**
   * @return self::BLOCK_SCOPE_*
   */
  public function getBlockScope()
  {
    return $this->blockScope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BlockAction::class, 'Google_Service_AndroidManagement_BlockAction');
