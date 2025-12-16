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

namespace Google\Service\CloudHealthcare\Resource;

use Google\Service\CloudHealthcare\HttpBody;

/**
 * The "operations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $healthcareService = new Google\Service\CloudHealthcare(...);
 *   $operations = $healthcareService->projects_locations_datasets_fhirStores_operations;
 *  </code>
 */
class ProjectsLocationsDatasetsFhirStoresOperations extends \Google\Service\Resource
{
  /**
   * Deletes operations as defined in the FHIR specification. Implements the FHIR
   * implementation guide [bulk data delete
   * request](https://build.fhir.org/ig/HL7/bulk-data/export.html#bulk-data-
   * delete-request). Returns success if the operation was successfully cancelled.
   * If the operation is complete, or has already been cancelled, returns an error
   * response. (operations.deleteFhirOperation)
   *
   * @param string $name Required. Name of the operation to be deleted, in the
   * format `projects/{project_id}/locations/{location_id}/datasets/{dataset_id}/f
   * hirStores/{fhir_store_id}/operations/{operation_id}`.
   * @param array $optParams Optional parameters.
   * @return HttpBody
   * @throws \Google\Service\Exception
   */
  public function deleteFhirOperation($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete-fhir-operation', [$params], HttpBody::class);
  }
  /**
   * Gets the status of operations as defined in the FHIR specification.
   * Implements the FHIR implementation guide [bulk data status
   * request](https://build.fhir.org/ig/HL7/bulk-data/export.html#bulk-data-
   * status-request). Operations can have one of these states: * in-progress:
   * response status code is `202` and `X-Progress` header is set to `in
   * progress`. * complete: response status code is `200` and the body is a JSON-
   * encoded operation response as defined by the spec. For a bulk export, this
   * response is defined in https://build.fhir.org/ig/HL7/bulk-
   * data/export.html#response---complete-status. * error: response status code is
   * `5XX`, and the body is a JSON-encoded `OperationOutcome` resource describing
   * the reason for the error. (operations.getFhirOperationStatus)
   *
   * @param string $name Required. Name of the operation to query, in the format `
   * projects/{project_id}/locations/{location_id}/datasets/{dataset_id}/fhirStore
   * s/{fhir_store_id}/operations/{operation_id}`.
   * @param array $optParams Optional parameters.
   * @return HttpBody
   * @throws \Google\Service\Exception
   */
  public function getFhirOperationStatus($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get-fhir-operation-status', [$params], HttpBody::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDatasetsFhirStoresOperations::class, 'Google_Service_CloudHealthcare_Resource_ProjectsLocationsDatasetsFhirStoresOperations');
