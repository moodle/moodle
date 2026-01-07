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

class DeviceFeature extends \Google\Model
{
  /**
   * Name of the feature.
   *
   * @var string
   */
  public $featureName;
  /**
   * The feature version specified by android:glEsVersion or android:version in
   * in the AndroidManifest.
   *
   * @var int
   */
  public $featureVersion;

  /**
   * Name of the feature.
   *
   * @param string $featureName
   */
  public function setFeatureName($featureName)
  {
    $this->featureName = $featureName;
  }
  /**
   * @return string
   */
  public function getFeatureName()
  {
    return $this->featureName;
  }
  /**
   * The feature version specified by android:glEsVersion or android:version in
   * in the AndroidManifest.
   *
   * @param int $featureVersion
   */
  public function setFeatureVersion($featureVersion)
  {
    $this->featureVersion = $featureVersion;
  }
  /**
   * @return int
   */
  public function getFeatureVersion()
  {
    return $this->featureVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeviceFeature::class, 'Google_Service_AndroidPublisher_DeviceFeature');
