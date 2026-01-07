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

namespace Google\Service\Eventarc;

class GoogleChannelConfig extends \Google\Model
{
  /**
   * Optional. Resource name of a KMS crypto key (managed by the user) used to
   * encrypt/decrypt their event data. It must match the pattern
   * `projects/locations/keyRings/cryptoKeys`.
   *
   * @var string
   */
  public $cryptoKeyName;
  /**
   * Optional. Resource labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. The resource name of the config. Must be in the format of,
   * `projects/{project}/locations/{location}/googleChannelConfig`. In API
   * responses, the config name always includes the projectID, regardless of
   * whether the projectID or projectNumber was provided.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The last-modified time.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Resource name of a KMS crypto key (managed by the user) used to
   * encrypt/decrypt their event data. It must match the pattern
   * `projects/locations/keyRings/cryptoKeys`.
   *
   * @param string $cryptoKeyName
   */
  public function setCryptoKeyName($cryptoKeyName)
  {
    $this->cryptoKeyName = $cryptoKeyName;
  }
  /**
   * @return string
   */
  public function getCryptoKeyName()
  {
    return $this->cryptoKeyName;
  }
  /**
   * Optional. Resource labels.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Required. The resource name of the config. Must be in the format of,
   * `projects/{project}/locations/{location}/googleChannelConfig`. In API
   * responses, the config name always includes the projectID, regardless of
   * whether the projectID or projectNumber was provided.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The last-modified time.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChannelConfig::class, 'Google_Service_Eventarc_GoogleChannelConfig');
