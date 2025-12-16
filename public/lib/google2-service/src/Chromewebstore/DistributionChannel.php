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

class DistributionChannel extends \Google\Model
{
  /**
   * The extension version provided in the manifest of the uploaded package.
   *
   * @var string
   */
  public $crxVersion;
  /**
   * The current deploy percentage for the release channel (nonnegative number
   * between 0 and 100).
   *
   * @var int
   */
  public $deployPercentage;

  /**
   * The extension version provided in the manifest of the uploaded package.
   *
   * @param string $crxVersion
   */
  public function setCrxVersion($crxVersion)
  {
    $this->crxVersion = $crxVersion;
  }
  /**
   * @return string
   */
  public function getCrxVersion()
  {
    return $this->crxVersion;
  }
  /**
   * The current deploy percentage for the release channel (nonnegative number
   * between 0 and 100).
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
class_alias(DistributionChannel::class, 'Google_Service_Chromewebstore_DistributionChannel');
