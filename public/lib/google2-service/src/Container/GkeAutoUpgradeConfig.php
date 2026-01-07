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

namespace Google\Service\Container;

class GkeAutoUpgradeConfig extends \Google\Model
{
  /**
   * PATCH_MODE_UNSPECIFIED defaults to using the upgrade target from the
   * channel's patch upgrade targets as the upgrade target for the version.
   */
  public const PATCH_MODE_PATCH_MODE_UNSPECIFIED = 'PATCH_MODE_UNSPECIFIED';
  /**
   * ACCELERATED denotes that the latest patch build in the channel should be
   * used as the upgrade target for the version.
   */
  public const PATCH_MODE_ACCELERATED = 'ACCELERATED';
  /**
   * PatchMode specifies how auto upgrade patch builds should be selected.
   *
   * @var string
   */
  public $patchMode;

  /**
   * PatchMode specifies how auto upgrade patch builds should be selected.
   *
   * Accepted values: PATCH_MODE_UNSPECIFIED, ACCELERATED
   *
   * @param self::PATCH_MODE_* $patchMode
   */
  public function setPatchMode($patchMode)
  {
    $this->patchMode = $patchMode;
  }
  /**
   * @return self::PATCH_MODE_*
   */
  public function getPatchMode()
  {
    return $this->patchMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GkeAutoUpgradeConfig::class, 'Google_Service_Container_GkeAutoUpgradeConfig');
