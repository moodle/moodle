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

class AndroidMatrix extends \Google\Collection
{
  protected $collection_key = 'orientations';
  /**
   * Required. The ids of the set of Android device to be used. Use the
   * TestEnvironmentDiscoveryService to get supported options.
   *
   * @var string[]
   */
  public $androidModelIds;
  /**
   * Required. The ids of the set of Android OS version to be used. Use the
   * TestEnvironmentDiscoveryService to get supported options.
   *
   * @var string[]
   */
  public $androidVersionIds;
  /**
   * Required. The set of locales the test device will enable for testing. Use
   * the TestEnvironmentDiscoveryService to get supported options.
   *
   * @var string[]
   */
  public $locales;
  /**
   * Required. The set of orientations to test with. Use the
   * TestEnvironmentDiscoveryService to get supported options.
   *
   * @var string[]
   */
  public $orientations;

  /**
   * Required. The ids of the set of Android device to be used. Use the
   * TestEnvironmentDiscoveryService to get supported options.
   *
   * @param string[] $androidModelIds
   */
  public function setAndroidModelIds($androidModelIds)
  {
    $this->androidModelIds = $androidModelIds;
  }
  /**
   * @return string[]
   */
  public function getAndroidModelIds()
  {
    return $this->androidModelIds;
  }
  /**
   * Required. The ids of the set of Android OS version to be used. Use the
   * TestEnvironmentDiscoveryService to get supported options.
   *
   * @param string[] $androidVersionIds
   */
  public function setAndroidVersionIds($androidVersionIds)
  {
    $this->androidVersionIds = $androidVersionIds;
  }
  /**
   * @return string[]
   */
  public function getAndroidVersionIds()
  {
    return $this->androidVersionIds;
  }
  /**
   * Required. The set of locales the test device will enable for testing. Use
   * the TestEnvironmentDiscoveryService to get supported options.
   *
   * @param string[] $locales
   */
  public function setLocales($locales)
  {
    $this->locales = $locales;
  }
  /**
   * @return string[]
   */
  public function getLocales()
  {
    return $this->locales;
  }
  /**
   * Required. The set of orientations to test with. Use the
   * TestEnvironmentDiscoveryService to get supported options.
   *
   * @param string[] $orientations
   */
  public function setOrientations($orientations)
  {
    $this->orientations = $orientations;
  }
  /**
   * @return string[]
   */
  public function getOrientations()
  {
    return $this->orientations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AndroidMatrix::class, 'Google_Service_Testing_AndroidMatrix');
