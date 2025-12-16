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

namespace Google\Service\Storagetransfer\Resource;

use Google\Service\Storagetransfer\CancelOperationRequest;
use Google\Service\Storagetransfer\ListOperationsResponse;
use Google\Service\Storagetransfer\Operation;
use Google\Service\Storagetransfer\PauseTransferOperationRequest;
use Google\Service\Storagetransfer\ResumeTransferOperationRequest;
use Google\Service\Storagetransfer\StoragetransferEmpty;

/**
 * The "transferOperations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $storagetransferService = new Google\Service\Storagetransfer(...);
 *   $transferOperations = $storagetransferService->transferOperations;
 *  </code>
 */
class TransferOperations extends \Google\Service\Resource
{
  /**
   * Cancels a transfer. Use the transferOperations.get method to check if the
   * cancellation succeeded or if the operation completed despite the `cancel`
   * request. When you cancel an operation, the currently running transfer is
   * interrupted. For recurring transfer jobs, the next instance of the transfer
   * job will still run. For example, if your job is configured to run every day
   * at 1pm and you cancel Monday's operation at 1:05pm, Monday's transfer will
   * stop. However, a transfer job will still be attempted on Tuesday. This
   * applies only to currently running operations. If an operation is not
   * currently running, `cancel` does nothing. *Caution:* Canceling a transfer job
   * can leave your data in an unknown state. We recommend that you restore the
   * state at both the destination and the source after the `cancel` request
   * completes so that your data is in a consistent state. When you cancel a job,
   * the next job computes a delta of files and may repair any inconsistent state.
   * For instance, if you run a job every day, and today's job found 10 new files
   * and transferred five files before you canceled the job, tomorrow's transfer
   * operation will compute a new delta with the five files that were not copied
   * today plus any new files discovered tomorrow. (transferOperations.cancel)
   *
   * @param string $name The name of the operation resource to be cancelled.
   * @param CancelOperationRequest $postBody
   * @param array $optParams Optional parameters.
   * @return StoragetransferEmpty
   * @throws \Google\Service\Exception
   */
  public function cancel($name, CancelOperationRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params], StoragetransferEmpty::class);
  }
  /**
   * Gets the latest state of a long-running operation. Clients can use this
   * method to poll the operation result at intervals as recommended by the API
   * service. (transferOperations.get)
   *
   * @param string $name The name of the operation resource.
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Operation::class);
  }
  /**
   * Lists transfer operations. Operations are ordered by their creation time in
   * reverse chronological order. (transferOperations.listTransferOperations)
   *
   * @param string $name Required. The name of the type being listed; must be
   * `transferOperations`.
   * @param string $filter Required. A list of query parameters specified as JSON
   * text in the form of: `{"projectId":"my_project_id",
   * "jobNames":["jobid1","jobid2",...], "jobNamePattern": "job_name_pattern",
   * "operationNames":["opid1","opid2",...], "operationNamePattern":
   * "operation_name_pattern", "minCreationTime": "min_creation_time",
   * "maxCreationTime": "max_creation_time",
   * "transferStatuses":["status1","status2",...]}` Since `jobNames`,
   * `operationNames`, and `transferStatuses` support multiple values, they must
   * be specified with array notation. `projectId` is the only argument that is
   * required. If specified, `jobNamePattern` and `operationNamePattern` must
   * match the full job or operation name respectively. '*' is a wildcard matching
   * 0 or more characters. `minCreationTime` and `maxCreationTime` should be
   * timestamps encoded as a string in the [RFC
   * 3339](https://www.ietf.org/rfc/rfc3339.txt) format. The valid values for
   * `transferStatuses` are case-insensitive: IN_PROGRESS, PAUSED, SUCCESS,
   * FAILED, and ABORTED.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The list page size. The max allowed value is 256.
   * @opt_param string pageToken The list page token.
   * @opt_param bool returnPartialSuccess When set to `true`, operations that are
   * reachable are returned as normal, and those that are unreachable are returned
   * in the ListOperationsResponse.unreachable field. This can only be `true` when
   * reading across collections. For example, when `parent` is set to
   * `"projects/example/locations/-"`. This field is not supported by default and
   * will result in an `UNIMPLEMENTED` error if set unless explicitly documented
   * otherwise in service or product specific documentation.
   * @return ListOperationsResponse
   * @throws \Google\Service\Exception
   */
  public function listTransferOperations($name, $filter, $optParams = [])
  {
    $params = ['name' => $name, 'filter' => $filter];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListOperationsResponse::class);
  }
  /**
   * Pauses a transfer operation. (transferOperations.pause)
   *
   * @param string $name Required. The name of the transfer operation.
   * @param PauseTransferOperationRequest $postBody
   * @param array $optParams Optional parameters.
   * @return StoragetransferEmpty
   * @throws \Google\Service\Exception
   */
  public function pause($name, PauseTransferOperationRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('pause', [$params], StoragetransferEmpty::class);
  }
  /**
   * Resumes a transfer operation that is paused. (transferOperations.resume)
   *
   * @param string $name Required. The name of the transfer operation.
   * @param ResumeTransferOperationRequest $postBody
   * @param array $optParams Optional parameters.
   * @return StoragetransferEmpty
   * @throws \Google\Service\Exception
   */
  public function resume($name, ResumeTransferOperationRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('resume', [$params], StoragetransferEmpty::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TransferOperations::class, 'Google_Service_Storagetransfer_Resource_TransferOperations');
