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

namespace Google\Service\CloudDataplex\Resource;

use Google\Service\CloudDataplex\DataplexEmpty;
use Google\Service\CloudDataplex\GoogleCloudDataplexV1CancelMetadataJobRequest;
use Google\Service\CloudDataplex\GoogleCloudDataplexV1ListMetadataJobsResponse;
use Google\Service\CloudDataplex\GoogleCloudDataplexV1MetadataJob;
use Google\Service\CloudDataplex\GoogleLongrunningOperation;

/**
 * The "metadataJobs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dataplexService = new Google\Service\CloudDataplex(...);
 *   $metadataJobs = $dataplexService->projects_locations_metadataJobs;
 *  </code>
 */
class ProjectsLocationsMetadataJobs extends \Google\Service\Resource
{
  /**
   * Cancels a metadata job.If you cancel a metadata import job that is in
   * progress, the changes in the job might be partially applied. We recommend
   * that you reset the state of the entry groups in your project by running
   * another metadata job that reverts the changes from the canceled job.
   * (metadataJobs.cancel)
   *
   * @param string $name Required. The resource name of the job, in the format pro
   * jects/{project_id_or_number}/locations/{location_id}/metadataJobs/{metadata_j
   * ob_id}
   * @param GoogleCloudDataplexV1CancelMetadataJobRequest $postBody
   * @param array $optParams Optional parameters.
   * @return DataplexEmpty
   * @throws \Google\Service\Exception
   */
  public function cancel($name, GoogleCloudDataplexV1CancelMetadataJobRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params], DataplexEmpty::class);
  }
  /**
   * Creates a metadata job. For example, use a metadata job to import metadata
   * from a third-party system into Dataplex Universal Catalog.
   * (metadataJobs.create)
   *
   * @param string $parent Required. The resource name of the parent location, in
   * the format projects/{project_id_or_number}/locations/{location_id}
   * @param GoogleCloudDataplexV1MetadataJob $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string metadataJobId Optional. The metadata job ID. If not
   * provided, a unique ID is generated with the prefix metadata-job-.
   * @opt_param bool validateOnly Optional. The service validates the request
   * without performing any mutations. The default is false.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDataplexV1MetadataJob $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Gets a metadata job. (metadataJobs.get)
   *
   * @param string $name Required. The resource name of the metadata job, in the
   * format projects/{project_id_or_number}/locations/{location_id}/metadataJobs/{
   * metadata_job_id}.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDataplexV1MetadataJob
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDataplexV1MetadataJob::class);
  }
  /**
   * Lists metadata jobs. (metadataJobs.listProjectsLocationsMetadataJobs)
   *
   * @param string $parent Required. The resource name of the parent location, in
   * the format projects/{project_id_or_number}/locations/{location_id}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filter request. Filters are case-
   * sensitive. The service supports the following formats: labels.key1 = "value1"
   * labels:key1 name = "value"You can combine filters with AND, OR, and NOT
   * operators.
   * @opt_param string orderBy Optional. The field to sort the results by, either
   * name or create_time. If not specified, the ordering is undefined.
   * @opt_param int pageSize Optional. The maximum number of metadata jobs to
   * return. The service might return fewer jobs than this value. If unspecified,
   * at most 10 jobs are returned. The maximum value is 1,000.
   * @opt_param string pageToken Optional. The page token received from a previous
   * ListMetadataJobs call. Provide this token to retrieve the subsequent page of
   * results. When paginating, all other parameters that are provided to the
   * ListMetadataJobs request must match the call that provided the page token.
   * @return GoogleCloudDataplexV1ListMetadataJobsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsMetadataJobs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDataplexV1ListMetadataJobsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsMetadataJobs::class, 'Google_Service_CloudDataplex_Resource_ProjectsLocationsMetadataJobs');
