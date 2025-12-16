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
 * The "bulkdata" collection of methods.
 * Typical usage is:
 *  <code>
 *   $healthcareService = new Google\Service\CloudHealthcare(...);
 *   $bulkdata = $healthcareService->projects_locations_datasets_dicomStores_studies_series_instances_bulkdata;
 *  </code>
 */
class ProjectsLocationsDatasetsDicomStoresStudiesSeriesInstancesBulkdata extends \Google\Service\Resource
{
  /**
   * Returns uncompressed, unencoded bytes representing the referenced bulkdata
   * tag from an instance. See [Retrieve Transaction](https://dicom.nema.org/medic
   * al/dicom/current/output/html/part18.html#sect_10.4). For details on the
   * implementation of RetrieveBulkdata, see [Bulkdata
   * resources](https://cloud.google.com/healthcare/docs/dicom#bulkdata-resources)
   * in the Cloud Healthcare API conformance statement. For samples that show how
   * to call RetrieveBulkdata, see [Retrieve
   * bulkdata](https://cloud.google.com/healthcare/docs/how-tos/dicomweb#retrieve-
   * bulkdata). (bulkdata.retrieveBulkdata)
   *
   * @param string $parent Required. The name of the DICOM store that is being
   * accessed. For example, `projects/{project_id}/locations/{location_id}/dataset
   * s/{dataset_id}/dicomStores/{dicom_store_id}`.
   * @param string $dicomWebPath Required. The path for the `RetrieveBulkdata`
   * DICOMweb request. For example, `studies/{study_uid}/series/{series_uid}/insta
   * nces/{instance_uid}/bukdata/{bulkdata_uri}`.
   * @param array $optParams Optional parameters.
   * @return HttpBody
   * @throws \Google\Service\Exception
   */
  public function retrieveBulkdata($parent, $dicomWebPath, $optParams = [])
  {
    $params = ['parent' => $parent, 'dicomWebPath' => $dicomWebPath];
    $params = array_merge($params, $optParams);
    return $this->call('retrieveBulkdata', [$params], HttpBody::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDatasetsDicomStoresStudiesSeriesInstancesBulkdata::class, 'Google_Service_CloudHealthcare_Resource_ProjectsLocationsDatasetsDicomStoresStudiesSeriesInstancesBulkdata');
