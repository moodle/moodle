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

class GoogleCloudDatacatalogV1SerializedTaxonomy extends \Google\Collection
{
  protected $collection_key = 'policyTags';
  /**
   * A list of policy types that are activated per taxonomy.
   *
   * @var string[]
   */
  public $activatedPolicyTypes;
  /**
   * Description of the serialized taxonomy. At most 2000 bytes when encoded in
   * UTF-8. If not set, defaults to an empty description.
   *
   * @var string
   */
  public $description;
  /**
   * Required. Display name of the taxonomy. At most 200 bytes when encoded in
   * UTF-8.
   *
   * @var string
   */
  public $displayName;
  protected $policyTagsType = GoogleCloudDatacatalogV1SerializedPolicyTag::class;
  protected $policyTagsDataType = 'array';

  /**
   * A list of policy types that are activated per taxonomy.
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
   * Description of the serialized taxonomy. At most 2000 bytes when encoded in
   * UTF-8. If not set, defaults to an empty description.
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
   * Required. Display name of the taxonomy. At most 200 bytes when encoded in
   * UTF-8.
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
   * Top level policy tags associated with the taxonomy, if any.
   *
   * @param GoogleCloudDatacatalogV1SerializedPolicyTag[] $policyTags
   */
  public function setPolicyTags($policyTags)
  {
    $this->policyTags = $policyTags;
  }
  /**
   * @return GoogleCloudDatacatalogV1SerializedPolicyTag[]
   */
  public function getPolicyTags()
  {
    return $this->policyTags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1SerializedTaxonomy::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1SerializedTaxonomy');
