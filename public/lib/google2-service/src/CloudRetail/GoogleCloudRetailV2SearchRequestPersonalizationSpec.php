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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2SearchRequestPersonalizationSpec extends \Google\Model
{
  /**
   * Default value. In this case, server behavior defaults to Mode.AUTO.
   */
  public const MODE_MODE_UNSPECIFIED = 'MODE_UNSPECIFIED';
  /**
   * Let CRS decide whether to use personalization based on quality of user
   * event data.
   */
  public const MODE_AUTO = 'AUTO';
  /**
   * Disable personalization.
   */
  public const MODE_DISABLED = 'DISABLED';
  /**
   * Defaults to Mode.AUTO.
   *
   * @var string
   */
  public $mode;

  /**
   * Defaults to Mode.AUTO.
   *
   * Accepted values: MODE_UNSPECIFIED, AUTO, DISABLED
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2SearchRequestPersonalizationSpec::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2SearchRequestPersonalizationSpec');
