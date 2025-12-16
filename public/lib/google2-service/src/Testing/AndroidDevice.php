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

namespace Google\Service\Testing;

class AndroidDevice extends \Google\Model
{
  /**
   * Required. The id of the Android device to be used. Use the
   * TestEnvironmentDiscoveryService to get supported options.
   *
   * @var string
   */
  public $androidModelId;
  /**
   * Required. The id of the Android OS version to be used. Use the
   * TestEnvironmentDiscoveryService to get supported options.
   *
   * @var string
   */
  public $androidVersionId;
  /**
   * Required. The locale the test device used for testing. Use the
   * TestEnvironmentDiscoveryService to get supported options.
   *
   * @var string
   */
  public $locale;
  /**
   * Required. How the device is oriented during the test. Use the
   * TestEnvironmentDiscoveryService to get supported options.
   *
   * @var string
   */
  public $orientation;

  /**
   * Required. The id of the Android device to be used. Use the
   * TestEnvironmentDiscoveryService to get supported options.
   *
   * @param string $androidModelId
   */
  public function setAndroidModelId($androidModelId)
  {
    $this->androidModelId = $androidModelId;
  }
  /**
   * @return string
   */
  public function getAndroidModelId()
  {
    return $this->androidModelId;
  }
  /**
   * Required. The id of the Android OS version to be used. Use the
   * TestEnvironmentDiscoveryService to get supported options.
   *
   * @param string $androidVersionId
   */
  public function setAndroidVersionId($androidVersionId)
  {
    $this->androidVersionId = $androidVersionId;
  }
  /**
   * @return string
   */
  public function getAndroidVersionId()
  {
    return $this->androidVersionId;
  }
  /**
   * Required. The locale the test device used for testing. Use the
   * TestEnvironmentDiscoveryService to get supported options.
   *
   * @param string $locale
   */
  public function setLocale($locale)
  {
    $this->locale = $locale;
  }
  /**
   * @return string
   */
  public function getLocale()
  {
    return $this->locale;
  }
  /**
   * Required. How the device is oriented during the test. Use the
   * TestEnvironmentDiscoveryService to get supported options.
   *
   * @param string $orientation
   */
  public function setOrientation($orientation)
  {
    $this->orientation = $orientation;
  }
  /**
   * @return string
   */
  public function getOrientation()
  {
    return $this->orientation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AndroidDevice::class, 'Google_Service_Testing_AndroidDevice');
