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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2DiscoveryFileStoreConditions extends \Google\Model
{
  protected $cloudStorageConditionsType = GooglePrivacyDlpV2DiscoveryCloudStorageConditions::class;
  protected $cloudStorageConditionsDataType = '';
  /**
   * Optional. File store must have been created after this date. Used to avoid
   * backfilling.
   *
   * @var string
   */
  public $createdAfter;
  /**
   * Optional. Minimum age a file store must have. If set, the value must be 1
   * hour or greater.
   *
   * @var string
   */
  public $minAge;

  /**
   * Optional. Cloud Storage conditions.
   *
   * @param GooglePrivacyDlpV2DiscoveryCloudStorageConditions $cloudStorageConditions
   */
  public function setCloudStorageConditions(GooglePrivacyDlpV2DiscoveryCloudStorageConditions $cloudStorageConditions)
  {
    $this->cloudStorageConditions = $cloudStorageConditions;
  }
  /**
   * @return GooglePrivacyDlpV2DiscoveryCloudStorageConditions
   */
  public function getCloudStorageConditions()
  {
    return $this->cloudStorageConditions;
  }
  /**
   * Optional. File store must have been created after this date. Used to avoid
   * backfilling.
   *
   * @param string $createdAfter
   */
  public function setCreatedAfter($createdAfter)
  {
    $this->createdAfter = $createdAfter;
  }
  /**
   * @return string
   */
  public function getCreatedAfter()
  {
    return $this->createdAfter;
  }
  /**
   * Optional. Minimum age a file store must have. If set, the value must be 1
   * hour or greater.
   *
   * @param string $minAge
   */
  public function setMinAge($minAge)
  {
    $this->minAge = $minAge;
  }
  /**
   * @return string
   */
  public function getMinAge()
  {
    return $this->minAge;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DiscoveryFileStoreConditions::class, 'Google_Service_DLP_GooglePrivacyDlpV2DiscoveryFileStoreConditions');
