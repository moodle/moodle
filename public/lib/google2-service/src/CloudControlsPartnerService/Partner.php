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

namespace Google\Service\CloudControlsPartnerService;

class Partner extends \Google\Collection
{
  protected $collection_key = 'skus';
  /**
   * Output only. Time the resource was created
   *
   * @var string
   */
  public $createTime;
  protected $ekmSolutionsType = EkmMetadata::class;
  protected $ekmSolutionsDataType = 'array';
  /**
   * Identifier. The resource name of the partner. Format:
   * `organizations/{organization}/locations/{location}/partner` Example:
   * "organizations/123456/locations/us-central1/partner"
   *
   * @var string
   */
  public $name;
  /**
   * List of Google Cloud regions that the partner sells services to customers.
   * Valid Google Cloud regions found here:
   * https://cloud.google.com/compute/docs/regions-zones
   *
   * @var string[]
   */
  public $operatedCloudRegions;
  /**
   * Google Cloud project ID in the partner's Google Cloud organization for
   * receiving enhanced Logs for Partners.
   *
   * @var string
   */
  public $partnerProjectId;
  protected $skusType = Sku::class;
  protected $skusDataType = 'array';
  /**
   * Output only. The last time the resource was updated
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Time the resource was created
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * List of Google Cloud supported EKM partners supported by the partner
   *
   * @param EkmMetadata[] $ekmSolutions
   */
  public function setEkmSolutions($ekmSolutions)
  {
    $this->ekmSolutions = $ekmSolutions;
  }
  /**
   * @return EkmMetadata[]
   */
  public function getEkmSolutions()
  {
    return $this->ekmSolutions;
  }
  /**
   * Identifier. The resource name of the partner. Format:
   * `organizations/{organization}/locations/{location}/partner` Example:
   * "organizations/123456/locations/us-central1/partner"
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
   * List of Google Cloud regions that the partner sells services to customers.
   * Valid Google Cloud regions found here:
   * https://cloud.google.com/compute/docs/regions-zones
   *
   * @param string[] $operatedCloudRegions
   */
  public function setOperatedCloudRegions($operatedCloudRegions)
  {
    $this->operatedCloudRegions = $operatedCloudRegions;
  }
  /**
   * @return string[]
   */
  public function getOperatedCloudRegions()
  {
    return $this->operatedCloudRegions;
  }
  /**
   * Google Cloud project ID in the partner's Google Cloud organization for
   * receiving enhanced Logs for Partners.
   *
   * @param string $partnerProjectId
   */
  public function setPartnerProjectId($partnerProjectId)
  {
    $this->partnerProjectId = $partnerProjectId;
  }
  /**
   * @return string
   */
  public function getPartnerProjectId()
  {
    return $this->partnerProjectId;
  }
  /**
   * List of SKUs the partner is offering
   *
   * @param Sku[] $skus
   */
  public function setSkus($skus)
  {
    $this->skus = $skus;
  }
  /**
   * @return Sku[]
   */
  public function getSkus()
  {
    return $this->skus;
  }
  /**
   * Output only. The last time the resource was updated
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
class_alias(Partner::class, 'Google_Service_CloudControlsPartnerService_Partner');
