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

namespace Google\Service\Dataproc\Resource;

use Google\Service\Dataproc\AnalyzeBatchRequest;
use Google\Service\Dataproc\Batch;
use Google\Service\Dataproc\DataprocEmpty;
use Google\Service\Dataproc\ListBatchesResponse;
use Google\Service\Dataproc\Operation;

/**
 * The "batches" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dataprocService = new Google\Service\Dataproc(...);
 *   $batches = $dataprocService->projects_locations_batches;
 *  </code>
 */
class ProjectsLocationsBatches extends \Google\Service\Resource
{
  /**
   * Analyze a Batch for possible recommendations and insights. (batches.analyze)
   *
   * @param string $name Required. The fully qualified name of the batch to
   * analyze in the format
   * "projects/PROJECT_ID/locations/DATAPROC_REGION/batches/BATCH_ID"
   * @param AnalyzeBatchRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function analyze($name, AnalyzeBatchRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('analyze', [$params], Operation::class);
  }
  /**
   * Creates a batch workload that executes asynchronously. (batches.create)
   *
   * @param string $parent Required. The parent resource where this batch will be
   * created.
   * @param Batch $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string batchId Optional. The ID to use for the batch, which will
   * become the final component of the batch's resource name.This value must be
   * 4-63 characters. Valid characters are /[a-z][0-9]-/.
   * @opt_param string requestId Optional. A unique ID used to identify the
   * request. If the service receives two CreateBatchRequest (https://cloud.google
   * .com/dataproc/docs/reference/rpc/google.cloud.dataproc.v1#google.cloud.datapr
   * oc.v1.CreateBatchRequest)s with the same request_id, the second request is
   * ignored and the Operation that corresponds to the first Batch created and
   * stored in the backend is returned.Recommendation: Set this value to a UUID
   * (https://en.wikipedia.org/wiki/Universally_unique_identifier).The value must
   * contain only letters (a-z, A-Z), numbers (0-9), underscores (_), and hyphens
   * (-). The maximum length is 40 characters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Batch $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes the batch workload resource. If the batch is not in a CANCELLED,
   * SUCCEEDED or FAILED State, the delete operation fails and the response
   * returns FAILED_PRECONDITION. (batches.delete)
   *
   * @param string $name Required. The fully qualified name of the batch to
   * retrieve in the format
   * "projects/PROJECT_ID/locations/DATAPROC_REGION/batches/BATCH_ID"
   * @param array $optParams Optional parameters.
   * @return DataprocEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], DataprocEmpty::class);
  }
  /**
   * Gets the batch workload resource representation. (batches.get)
   *
   * @param string $name Required. The fully qualified name of the batch to
   * retrieve in the format
   * "projects/PROJECT_ID/locations/DATAPROC_REGION/batches/BATCH_ID"
   * @param array $optParams Optional parameters.
   * @return Batch
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Batch::class);
  }
  /**
   * Lists batch workloads. (batches.listProjectsLocationsBatches)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * batches.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. A filter for the batches to return in the
   * response.A filter is a logical expression constraining the values of various
   * fields in each batch resource. Filters are case sensitive, and may contain
   * multiple clauses combined with logical operators (AND/OR). Supported fields
   * are batch_id, batch_uuid, state, create_time, and labels.e.g. state = RUNNING
   * and create_time < "2023-01-01T00:00:00Z" filters for batches in state RUNNING
   * that were created before 2023-01-01. state = RUNNING and
   * labels.environment=production filters for batches in state in a RUNNING state
   * that have a production environment label.See
   * https://google.aip.dev/assets/misc/ebnf-filtering.txt for a detailed
   * description of the filter syntax and a list of supported comparisons.
   * @opt_param string orderBy Optional. Field(s) on which to sort the list of
   * batches.Currently the only supported sort orders are unspecified (empty) and
   * create_time desc to sort by most recently created batches first.See
   * https://google.aip.dev/132#ordering for more details.
   * @opt_param int pageSize Optional. The maximum number of batches to return in
   * each response. The service may return fewer than this value. The default page
   * size is 20; the maximum page size is 1000.
   * @opt_param string pageToken Optional. A page token received from a previous
   * ListBatches call. Provide this token to retrieve the subsequent page.
   * @return ListBatchesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsBatches($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListBatchesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsBatches::class, 'Google_Service_Dataproc_Resource_ProjectsLocationsBatches');
