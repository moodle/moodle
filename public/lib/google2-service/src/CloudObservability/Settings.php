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

namespace Google\Service\CloudObservability;

class Settings extends \Google\Model
{
  /**
   * Optional. The location which should be used when any regional resources are
   * provisioned by GCP.
   *
   * @var string
   */
  public $defaultStorageLocation;
  /**
   * Optional. The resource name for the configured Cloud KMS key. KMS key name
   * format: "projects/[PROJECT_ID]/locations/[LOCATION]/keyRings/[KEYRING]/cryp
   * toKeys/[KEY]" For example: `"projects/my-project/locations/us-
   * central1/keyRings/my-ring/cryptoKeys/my-key"`
   *
   * @var string
   */
  public $kmsKeyName;
  /**
   * Identifier. The resource name of the settings.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The service account for the given resource container, such as
   * project or folder. This will be used by Cloud Observability to perform
   * actions in the container's project like access KMS keys or create Links.
   * Always the same service account per resource container regardless of
   * region.
   *
   * @var string
   */
  public $serviceAccountId;

  /**
   * Optional. The location which should be used when any regional resources are
   * provisioned by GCP.
   *
   * @param string $defaultStorageLocation
   */
  public function setDefaultStorageLocation($defaultStorageLocation)
  {
    $this->defaultStorageLocation = $defaultStorageLocation;
  }
  /**
   * @return string
   */
  public function getDefaultStorageLocation()
  {
    return $this->defaultStorageLocation;
  }
  /**
   * Optional. The resource name for the configured Cloud KMS key. KMS key name
   * format: "projects/[PROJECT_ID]/locations/[LOCATION]/keyRings/[KEYRING]/cryp
   * toKeys/[KEY]" For example: `"projects/my-project/locations/us-
   * central1/keyRings/my-ring/cryptoKeys/my-key"`
   *
   * @param string $kmsKeyName
   */
  public function setKmsKeyName($kmsKeyName)
  {
    $this->kmsKeyName = $kmsKeyName;
  }
  /**
   * @return string
   */
  public function getKmsKeyName()
  {
    return $this->kmsKeyName;
  }
  /**
   * Identifier. The resource name of the settings.
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
   * Output only. The service account for the given resource container, such as
   * project or folder. This will be used by Cloud Observability to perform
   * actions in the container's project like access KMS keys or create Links.
   * Always the same service account per resource container regardless of
   * region.
   *
   * @param string $serviceAccountId
   */
  public function setServiceAccountId($serviceAccountId)
  {
    $this->serviceAccountId = $serviceAccountId;
  }
  /**
   * @return string
   */
  public function getServiceAccountId()
  {
    return $this->serviceAccountId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Settings::class, 'Google_Service_CloudObservability_Settings');
