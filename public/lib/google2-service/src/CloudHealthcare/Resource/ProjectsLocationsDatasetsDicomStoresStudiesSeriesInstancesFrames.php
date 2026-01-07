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
 * The "frames" collection of methods.
 * Typical usage is:
 *  <code>
 *   $healthcareService = new Google\Service\CloudHealthcare(...);
 *   $frames = $healthcareService->projects_locations_datasets_dicomStores_studies_series_instances_frames;
 *  </code>
 */
class ProjectsLocationsDatasetsDicomStoresStudiesSeriesInstancesFrames extends \Google\Service\Resource
{
  /**
   * RetrieveFrames returns instances associated with the given study, series, SOP
   * Instance UID and frame numbers. See [RetrieveTransaction] (https://dicom.nema
   * .org/medical/dicom/current/output/html/part18.html#sect_10.4}. For details on
   * the implementation of RetrieveFrames, see [DICOM
   * frames](https://cloud.google.com/healthcare/docs/dicom#dicom_frames) in the
   * Cloud Healthcare API conformance statement. For samples that show how to call
   * RetrieveFrames, see [Retrieve DICOM
   * data](https://cloud.google.com/healthcare/docs/how-tos/dicomweb#retrieve-
   * dicom). (frames.retrieveFrames)
   *
   * @param string $parent Required. The name of the DICOM store that is being
   * accessed. For example, `projects/{project_id}/locations/{location_id}/dataset
   * s/{dataset_id}/dicomStores/{dicom_store_id}`.
   * @param string $dicomWebPath Required. The path of the RetrieveFrames DICOMweb
   * request. For example, `studies/{study_uid}/series/{series_uid}/instances/{ins
   * tance_uid}/frames/{frame_list}`.
   * @param array $optParams Optional parameters.
   * @return HttpBody
   * @throws \Google\Service\Exception
   */
  public function retrieveFrames($parent, $dicomWebPath, $optParams = [])
  {
    $params = ['parent' => $parent, 'dicomWebPath' => $dicomWebPath];
    $params = array_merge($params, $optParams);
    return $this->call('retrieveFrames', [$params], HttpBody::class);
  }
  /**
   * RetrieveRenderedFrames returns instances associated with the given study,
   * series, SOP Instance UID and frame numbers in an acceptable Rendered Media
   * Type. See [RetrieveTransaction] (https://dicom.nema.org/medical/dicom/current
   * /output/html/part18.html#sect_10.4). For details on the implementation of
   * RetrieveRenderedFrames, see [Rendered
   * resources](https://cloud.google.com/healthcare/docs/dicom#rendered_resources)
   * in the Cloud Healthcare API conformance statement. For samples that show how
   * to call RetrieveRenderedFrames, see [Retrieve consumer image
   * formats](https://cloud.google.com/healthcare/docs/how-tos/dicomweb#retrieve-
   * consumer). (frames.retrieveRendered)
   *
   * @param string $parent Required. The name of the DICOM store that is being
   * accessed. For example, `projects/{project_id}/locations/{location_id}/dataset
   * s/{dataset_id}/dicomStores/{dicom_store_id}`.
   * @param string $dicomWebPath Required. The path of the RetrieveRenderedFrames
   * DICOMweb request. For example, `studies/{study_uid}/series/{series_uid}/insta
   * nces/{instance_uid}/frames/{frame_list}/rendered`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string viewport Optional. The viewport setting to use as specified
   * in https://dicom.nema.org/medical/dicom/current/output/chtml/part18/sect_8.3.
   * 5.html#sect_8.3.5.1.3
   * @return HttpBody
   * @throws \Google\Service\Exception
   */
  public function retrieveRendered($parent, $dicomWebPath, $optParams = [])
  {
    $params = ['parent' => $parent, 'dicomWebPath' => $dicomWebPath];
    $params = array_merge($params, $optParams);
    return $this->call('retrieveRendered', [$params], HttpBody::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDatasetsDicomStoresStudiesSeriesInstancesFrames::class, 'Google_Service_CloudHealthcare_Resource_ProjectsLocationsDatasetsDicomStoresStudiesSeriesInstancesFrames');
