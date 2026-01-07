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

namespace Google\Service\GKEHub;

class ConfigManagementOciConfig extends \Google\Model
{
  /**
   * Optional. The Google Cloud Service Account Email used for auth when
   * secret_type is gcpServiceAccount.
   *
   * @var string
   */
  public $gcpServiceAccountEmail;
  /**
   * Optional. The absolute path of the directory that contains the local
   * resources. Default: the root directory of the image.
   *
   * @var string
   */
  public $policyDir;
  /**
   * Required. Type of secret configured for access to the OCI repo. Must be one
   * of gcenode, gcpserviceaccount, k8sserviceaccount or none. The validation of
   * this is case-sensitive.
   *
   * @var string
   */
  public $secretType;
  /**
   * Required. The OCI image repository URL for the package to sync from. e.g.
   * `LOCATION-docker.pkg.dev/PROJECT_ID/REPOSITORY_NAME/PACKAGE_NAME`.
   *
   * @var string
   */
  public $syncRepo;
  /**
   * Optional. Period in seconds between consecutive syncs. Default: 15.
   *
   * @var string
   */
  public $syncWaitSecs;

  /**
   * Optional. The Google Cloud Service Account Email used for auth when
   * secret_type is gcpServiceAccount.
   *
   * @param string $gcpServiceAccountEmail
   */
  public function setGcpServiceAccountEmail($gcpServiceAccountEmail)
  {
    $this->gcpServiceAccountEmail = $gcpServiceAccountEmail;
  }
  /**
   * @return string
   */
  public function getGcpServiceAccountEmail()
  {
    return $this->gcpServiceAccountEmail;
  }
  /**
   * Optional. The absolute path of the directory that contains the local
   * resources. Default: the root directory of the image.
   *
   * @param string $policyDir
   */
  public function setPolicyDir($policyDir)
  {
    $this->policyDir = $policyDir;
  }
  /**
   * @return string
   */
  public function getPolicyDir()
  {
    return $this->policyDir;
  }
  /**
   * Required. Type of secret configured for access to the OCI repo. Must be one
   * of gcenode, gcpserviceaccount, k8sserviceaccount or none. The validation of
   * this is case-sensitive.
   *
   * @param string $secretType
   */
  public function setSecretType($secretType)
  {
    $this->secretType = $secretType;
  }
  /**
   * @return string
   */
  public function getSecretType()
  {
    return $this->secretType;
  }
  /**
   * Required. The OCI image repository URL for the package to sync from. e.g.
   * `LOCATION-docker.pkg.dev/PROJECT_ID/REPOSITORY_NAME/PACKAGE_NAME`.
   *
   * @param string $syncRepo
   */
  public function setSyncRepo($syncRepo)
  {
    $this->syncRepo = $syncRepo;
  }
  /**
   * @return string
   */
  public function getSyncRepo()
  {
    return $this->syncRepo;
  }
  /**
   * Optional. Period in seconds between consecutive syncs. Default: 15.
   *
   * @param string $syncWaitSecs
   */
  public function setSyncWaitSecs($syncWaitSecs)
  {
    $this->syncWaitSecs = $syncWaitSecs;
  }
  /**
   * @return string
   */
  public function getSyncWaitSecs()
  {
    return $this->syncWaitSecs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfigManagementOciConfig::class, 'Google_Service_GKEHub_ConfigManagementOciConfig');
