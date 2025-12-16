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

class AdminConsents extends \Google\Collection
{
  protected $collection_key = 'names';
  /**
   * Optional. The versioned names of the admin Consent resource(s), in the
   * format `projects/{project_id}/locations/{location}/datasets/{dataset_id}/fh
   * irStores/{fhir_store_id}/fhir/Consent/{resource_id}/_history/{version_id}`.
   * For FHIR stores with `disable_resource_versioning=true`, the format is `pro
   * jects/{project_id}/locations/{location}/datasets/{dataset_id}/fhirStores/{f
   * hir_store_id}/fhir/Consent/{resource_id}`.
   *
   * @var string[]
   */
  public $names;

  /**
   * Optional. The versioned names of the admin Consent resource(s), in the
   * format `projects/{project_id}/locations/{location}/datasets/{dataset_id}/fh
   * irStores/{fhir_store_id}/fhir/Consent/{resource_id}/_history/{version_id}`.
   * For FHIR stores with `disable_resource_versioning=true`, the format is `pro
   * jects/{project_id}/locations/{location}/datasets/{dataset_id}/fhirStores/{f
   * hir_store_id}/fhir/Consent/{resource_id}`.
   *
   * @param string[] $names
   */
  public function setNames($names)
  {
    $this->names = $names;
  }
  /**
   * @return string[]
   */
  public function getNames()
  {
    return $this->names;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdminConsents::class, 'Google_Service_CloudHealthcare_AdminConsents');
