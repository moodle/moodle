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

namespace Google\Service\AndroidPublisher;

class RegionsVersion extends \Google\Model
{
  /**
   * Required. A string representing the version of available regions being used
   * for the specified resource. Regional prices and latest supported version
   * for the resource have to be specified according to the information
   * published in [this article](https://support.google.com/googleplay/android-
   * developer/answer/10532353). Each time the supported locations substantially
   * change, the version will be incremented. Using this field will ensure that
   * creating and updating the resource with an older region's version and set
   * of regional prices and currencies will succeed even though a new version is
   * available.
   *
   * @var string
   */
  public $version;

  /**
   * Required. A string representing the version of available regions being used
   * for the specified resource. Regional prices and latest supported version
   * for the resource have to be specified according to the information
   * published in [this article](https://support.google.com/googleplay/android-
   * developer/answer/10532353). Each time the supported locations substantially
   * change, the version will be incremented. Using this field will ensure that
   * creating and updating the resource with an older region's version and set
   * of regional prices and currencies will succeed even though a new version is
   * available.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RegionsVersion::class, 'Google_Service_AndroidPublisher_RegionsVersion');
