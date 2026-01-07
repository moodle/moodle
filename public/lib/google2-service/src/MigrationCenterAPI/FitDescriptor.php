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

namespace Google\Service\MigrationCenterAPI;

class FitDescriptor extends \Google\Model
{
  /**
   * Not enough information.
   */
  public const FIT_LEVEL_FIT_LEVEL_UNSPECIFIED = 'FIT_LEVEL_UNSPECIFIED';
  /**
   * Fit.
   */
  public const FIT_LEVEL_FIT = 'FIT';
  /**
   * No Fit.
   */
  public const FIT_LEVEL_NO_FIT = 'NO_FIT';
  /**
   * Fit with effort.
   */
  public const FIT_LEVEL_REQUIRES_EFFORT = 'REQUIRES_EFFORT';
  /**
   * Output only. Fit level.
   *
   * @var string
   */
  public $fitLevel;

  /**
   * Output only. Fit level.
   *
   * Accepted values: FIT_LEVEL_UNSPECIFIED, FIT, NO_FIT, REQUIRES_EFFORT
   *
   * @param self::FIT_LEVEL_* $fitLevel
   */
  public function setFitLevel($fitLevel)
  {
    $this->fitLevel = $fitLevel;
  }
  /**
   * @return self::FIT_LEVEL_*
   */
  public function getFitLevel()
  {
    return $this->fitLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FitDescriptor::class, 'Google_Service_MigrationCenterAPI_FitDescriptor');
