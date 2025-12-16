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

namespace Google\Service\Chromewebstore;

class SetPublishedDeployPercentageRequest extends \Google\Model
{
  /**
   * Required. Unscaled percentage value for the publised revision (nonnegative
   * number between 0 and 100). It must be larger than the existing target
   * percentage.
   *
   * @var int
   */
  public $deployPercentage;

  /**
   * Required. Unscaled percentage value for the publised revision (nonnegative
   * number between 0 and 100). It must be larger than the existing target
   * percentage.
   *
   * @param int $deployPercentage
   */
  public function setDeployPercentage($deployPercentage)
  {
    $this->deployPercentage = $deployPercentage;
  }
  /**
   * @return int
   */
  public function getDeployPercentage()
  {
    return $this->deployPercentage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SetPublishedDeployPercentageRequest::class, 'Google_Service_Chromewebstore_SetPublishedDeployPercentageRequest');
