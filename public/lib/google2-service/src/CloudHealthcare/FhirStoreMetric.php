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

namespace Google\Service\CloudHealthcare;

class FhirStoreMetric extends \Google\Model
{
  /**
   * The total count of FHIR resources in the store of this resource type.
   *
   * @var string
   */
  public $count;
  /**
   * The FHIR resource type this metric applies to.
   *
   * @var string
   */
  public $resourceType;
  /**
   * The total amount of structured storage used by FHIR resources of this
   * resource type in the store.
   *
   * @var string
   */
  public $structuredStorageSizeBytes;
  /**
   * The total amount of versioned storage used by versioned FHIR resources of
   * this resource type in the store.
   *
   * @var string
   */
  public $versionedStorageSizeBytes;

  /**
   * The total count of FHIR resources in the store of this resource type.
   *
   * @param string $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return string
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * The FHIR resource type this metric applies to.
   *
   * @param string $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return string
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
  /**
   * The total amount of structured storage used by FHIR resources of this
   * resource type in the store.
   *
   * @param string $structuredStorageSizeBytes
   */
  public function setStructuredStorageSizeBytes($structuredStorageSizeBytes)
  {
    $this->structuredStorageSizeBytes = $structuredStorageSizeBytes;
  }
  /**
   * @return string
   */
  public function getStructuredStorageSizeBytes()
  {
    return $this->structuredStorageSizeBytes;
  }
  /**
   * The total amount of versioned storage used by versioned FHIR resources of
   * this resource type in the store.
   *
   * @param string $versionedStorageSizeBytes
   */
  public function setVersionedStorageSizeBytes($versionedStorageSizeBytes)
  {
    $this->versionedStorageSizeBytes = $versionedStorageSizeBytes;
  }
  /**
   * @return string
   */
  public function getVersionedStorageSizeBytes()
  {
    return $this->versionedStorageSizeBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FhirStoreMetric::class, 'Google_Service_CloudHealthcare_FhirStoreMetric');
