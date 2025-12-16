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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1Taxonomy extends \Google\Collection
{
  protected $collection_key = 'activatedPolicyTypes';
  /**
   * Optional. A list of policy types that are activated for this taxonomy. If
   * not set, defaults to an empty list.
   *
   * @var string[]
   */
  public $activatedPolicyTypes;
  /**
   * Optional. Description of this taxonomy. If not set, defaults to empty. The
   * description must contain only Unicode characters, tabs, newlines, carriage
   * returns, and page breaks, and be at most 2000 bytes long when encoded in
   * UTF-8.
   *
   * @var string
   */
  public $description;
  /**
   * Required. User-defined name of this taxonomy. The name can't start or end
   * with spaces, must contain only Unicode letters, numbers, underscores,
   * dashes, and spaces, and be at most 200 bytes long when encoded in UTF-8.
   * The taxonomy display name must be unique within an organization.
   *
   * @var string
   */
  public $displayName;
  /**
   * Identifier. Resource name of this taxonomy in URL format. Note: Policy tag
   * manager generates unique taxonomy IDs.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Number of policy tags in this taxonomy.
   *
   * @var int
   */
  public $policyTagCount;
  protected $serviceType = GoogleCloudDatacatalogV1TaxonomyService::class;
  protected $serviceDataType = '';
  protected $taxonomyTimestampsType = GoogleCloudDatacatalogV1SystemTimestamps::class;
  protected $taxonomyTimestampsDataType = '';

  /**
   * Optional. A list of policy types that are activated for this taxonomy. If
   * not set, defaults to an empty list.
   *
   * @param string[] $activatedPolicyTypes
   */
  public function setActivatedPolicyTypes($activatedPolicyTypes)
  {
    $this->activatedPolicyTypes = $activatedPolicyTypes;
  }
  /**
   * @return string[]
   */
  public function getActivatedPolicyTypes()
  {
    return $this->activatedPolicyTypes;
  }
  /**
   * Optional. Description of this taxonomy. If not set, defaults to empty. The
   * description must contain only Unicode characters, tabs, newlines, carriage
   * returns, and page breaks, and be at most 2000 bytes long when encoded in
   * UTF-8.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. User-defined name of this taxonomy. The name can't start or end
   * with spaces, must contain only Unicode letters, numbers, underscores,
   * dashes, and spaces, and be at most 200 bytes long when encoded in UTF-8.
   * The taxonomy display name must be unique within an organization.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Identifier. Resource name of this taxonomy in URL format. Note: Policy tag
   * manager generates unique taxonomy IDs.
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
   * Output only. Number of policy tags in this taxonomy.
   *
   * @param int $policyTagCount
   */
  public function setPolicyTagCount($policyTagCount)
  {
    $this->policyTagCount = $policyTagCount;
  }
  /**
   * @return int
   */
  public function getPolicyTagCount()
  {
    return $this->policyTagCount;
  }
  /**
   * Output only. Identity of the service which owns the Taxonomy. This field is
   * only populated when the taxonomy is created by a Google Cloud service.
   * Currently only 'DATAPLEX' is supported.
   *
   * @param GoogleCloudDatacatalogV1TaxonomyService $service
   */
  public function setService(GoogleCloudDatacatalogV1TaxonomyService $service)
  {
    $this->service = $service;
  }
  /**
   * @return GoogleCloudDatacatalogV1TaxonomyService
   */
  public function getService()
  {
    return $this->service;
  }
  /**
   * Output only. Creation and modification timestamps of this taxonomy.
   *
   * @param GoogleCloudDatacatalogV1SystemTimestamps $taxonomyTimestamps
   */
  public function setTaxonomyTimestamps(GoogleCloudDatacatalogV1SystemTimestamps $taxonomyTimestamps)
  {
    $this->taxonomyTimestamps = $taxonomyTimestamps;
  }
  /**
   * @return GoogleCloudDatacatalogV1SystemTimestamps
   */
  public function getTaxonomyTimestamps()
  {
    return $this->taxonomyTimestamps;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1Taxonomy::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1Taxonomy');
