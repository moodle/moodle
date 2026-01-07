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
use Google\Service\CloudHealthcare\Operation;

/**
 * The "studies" collection of methods.
 * Typical usage is:
 *  <code>
 *   $healthcareService = new Google\Service\CloudHealthcare(...);
 *   $studies = $healthcareService->projects_locations_datasets_dicomStores_studies;
 *  </code>
 */
class ProjectsLocationsDatasetsDicomStoresStudies extends \Google\Service\Resource
{
  /**
   * DeleteStudy deletes all instances within the given study. Delete requests are
   * equivalent to the GET requests specified in the Retrieve transaction. The
   * method returns an Operation which will be marked successful when the deletion
   * is complete. Warning: Instances cannot be inserted into a study that is being
   * deleted by an operation until the operation completes. For samples that show
   * how to call DeleteStudy, see [Delete a study, series, or
   * instance](https://cloud.google.com/healthcare/docs/how-tos/dicomweb#delete-
   * dicom). (studies.delete)
   *
   * @param string $parent
   * @param string $dicomWebPath Required. The path of the DeleteStudy request.
   * For example, `studies/{study_uid}`.
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($parent, $dicomWebPath, $optParams = [])
  {
    $params = ['parent' => $parent, 'dicomWebPath' => $dicomWebPath];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * RetrieveStudyMetadata returns instance associated with the given study
   * presented as metadata. See [RetrieveTransaction] (https://dicom.nema.org/medi
   * cal/dicom/current/output/html/part18.html#sect_10.4). For details on the
   * implementation of RetrieveStudyMetadata, see [Metadata
   * resources](https://cloud.google.com/healthcare/docs/dicom#metadata_resources)
   * in the Cloud Healthcare API conformance statement. For samples that show how
   * to call RetrieveStudyMetadata, see [Retrieve
   * metadata](https://cloud.google.com/healthcare/docs/how-tos/dicomweb#retrieve-
   * metadata). (studies.retrieveMetadata)
   *
   * @param string $parent Required. The name of the DICOM store that is being
   * accessed. For example, `projects/{project_id}/locations/{location_id}/dataset
   * s/{dataset_id}/dicomStores/{dicom_store_id}`.
   * @param string $dicomWebPath Required. The path of the RetrieveStudyMetadata
   * DICOMweb request. For example, `studies/{study_uid}/metadata`.
   * @param array $optParams Optional parameters.
   * @return HttpBody
   * @throws \Google\Service\Exception
   */
  public function retrieveMetadata($parent, $dicomWebPath, $optParams = [])
  {
    $params = ['parent' => $parent, 'dicomWebPath' => $dicomWebPath];
    $params = array_merge($params, $optParams);
    return $this->call('retrieveMetadata', [$params], HttpBody::class);
  }
  /**
   * RetrieveStudy returns all instances within the given study. See
   * [RetrieveTransaction] (https://dicom.nema.org/medical/dicom/current/output/ht
   * ml/part18.html#sect_10.4). For details on the implementation of
   * RetrieveStudy, see [DICOM study/series/instances](https://cloud.google.com/he
   * althcare/docs/dicom#dicom_studyseriesinstances) in the Cloud Healthcare API
   * conformance statement. For samples that show how to call RetrieveStudy, see
   * [Retrieve DICOM data](https://cloud.google.com/healthcare/docs/how-
   * tos/dicomweb#retrieve-dicom). (studies.retrieveStudy)
   *
   * @param string $parent Required. The name of the DICOM store that is being
   * accessed. For example, `projects/{project_id}/locations/{location_id}/dataset
   * s/{dataset_id}/dicomStores/{dicom_store_id}`.
   * @param string $dicomWebPath Required. The path of the RetrieveStudy DICOMweb
   * request. For example, `studies/{study_uid}`.
   * @param array $optParams Optional parameters.
   * @return HttpBody
   * @throws \Google\Service\Exception
   */
  public function retrieveStudy($parent, $dicomWebPath, $optParams = [])
  {
    $params = ['parent' => $parent, 'dicomWebPath' => $dicomWebPath];
    $params = array_merge($params, $optParams);
    return $this->call('retrieveStudy', [$params], HttpBody::class);
  }
  /**
   * SearchForInstances returns a list of matching instances. See [Search
   * Transaction] (https://dicom.nema.org/medical/dicom/current/output/html/part18
   * .html#sect_10.6). For details on the implementation of SearchForInstances,
   * see [Search transaction](https://cloud.google.com/healthcare/docs/dicom#searc
   * h_transaction) in the Cloud Healthcare API conformance statement. For samples
   * that show how to call SearchForInstances, see [Search for DICOM
   * data](https://cloud.google.com/healthcare/docs/how-tos/dicomweb#search-
   * dicom). (studies.searchForInstances)
   *
   * @param string $parent Required. The name of the DICOM store that is being
   * accessed. For example, `projects/{project_id}/locations/{location_id}/dataset
   * s/{dataset_id}/dicomStores/{dicom_store_id}`.
   * @param string $dicomWebPath Required. The path of the
   * SearchForInstancesRequest DICOMweb request. For example, `instances`,
   * `studies/{study_uid}/series/{series_uid}/instances`, or
   * `studies/{study_uid}/instances`.
   * @param array $optParams Optional parameters.
   * @return HttpBody
   * @throws \Google\Service\Exception
   */
  public function searchForInstances($parent, $dicomWebPath, $optParams = [])
  {
    $params = ['parent' => $parent, 'dicomWebPath' => $dicomWebPath];
    $params = array_merge($params, $optParams);
    return $this->call('searchForInstances', [$params], HttpBody::class);
  }
  /**
   * SearchForSeries returns a list of matching series. See [Search Transaction] (
   * https://dicom.nema.org/medical/dicom/current/output/html/part18.html#sect_10.
   * 6). For details on the implementation of SearchForSeries, see [Search transac
   * tion](https://cloud.google.com/healthcare/docs/dicom#search_transaction) in
   * the Cloud Healthcare API conformance statement. For samples that show how to
   * call SearchForSeries, see [Search for DICOM
   * data](https://cloud.google.com/healthcare/docs/how-tos/dicomweb#search-
   * dicom). (studies.searchForSeries)
   *
   * @param string $parent Required. The name of the DICOM store that is being
   * accessed. For example, `projects/{project_id}/locations/{location_id}/dataset
   * s/{dataset_id}/dicomStores/{dicom_store_id}`.
   * @param string $dicomWebPath Required. The path of the SearchForSeries
   * DICOMweb request. For example, `series` or `studies/{study_uid}/series`.
   * @param array $optParams Optional parameters.
   * @return HttpBody
   * @throws \Google\Service\Exception
   */
  public function searchForSeries($parent, $dicomWebPath, $optParams = [])
  {
    $params = ['parent' => $parent, 'dicomWebPath' => $dicomWebPath];
    $params = array_merge($params, $optParams);
    return $this->call('searchForSeries', [$params], HttpBody::class);
  }
  /**
   * StoreInstances stores DICOM instances associated with study instance unique
   * identifiers (SUID). See [Store Transaction] (https://dicom.nema.org/medical/d
   * icom/current/output/html/part18.html#sect_10.5). For details on the
   * implementation of StoreInstances, see [Store transaction](https://cloud.googl
   * e.com/healthcare/docs/dicom#store_transaction) in the Cloud Healthcare API
   * conformance statement. For samples that show how to call StoreInstances, see
   * [Store DICOM data](https://cloud.google.com/healthcare/docs/how-
   * tos/dicomweb#store-dicom). (studies.storeInstances)
   *
   * @param string $parent Required. The name of the DICOM store that is being
   * accessed. For example, `projects/{project_id}/locations/{location_id}/dataset
   * s/{dataset_id}/dicomStores/{dicom_store_id}`.
   * @param string $dicomWebPath Required. The path of the StoreInstances DICOMweb
   * request. For example, `studies/[{study_uid}]`. Note that the `study_uid` is
   * optional.
   * @param HttpBody $postBody
   * @param array $optParams Optional parameters.
   * @return HttpBody
   * @throws \Google\Service\Exception
   */
  public function storeInstances($parent, $dicomWebPath, HttpBody $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'dicomWebPath' => $dicomWebPath, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('storeInstances', [$params], HttpBody::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDatasetsDicomStoresStudies::class, 'Google_Service_CloudHealthcare_Resource_ProjectsLocationsDatasetsDicomStoresStudies');
