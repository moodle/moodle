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

namespace Google\Service\Storage\Resource;

use Google\Service\Storage\AdvanceRelocateBucketOperationRequest;
use Google\Service\Storage\GoogleLongrunningListOperationsResponse;
use Google\Service\Storage\GoogleLongrunningOperation;

/**
 * The "operations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $storageService = new Google\Service\Storage(...);
 *   $operations = $storageService->operations;
 *  </code>
 */
class Operations extends \Google\Service\Resource
{
  /**
   * Starts asynchronous advancement of the relocate bucket operation in the case
   * of required write downtime, to allow it to lock the bucket at the source
   * location, and proceed with the bucket location swap. The server makes a best
   * effort to advance the relocate bucket operation, but success is not
   * guaranteed. (operations.advanceRelocateBucket)
   *
   * @param string $bucket Name of the bucket to advance the relocate for.
   * @param string $operationId ID of the operation resource.
   * @param AdvanceRelocateBucketOperationRequest $postBody
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function advanceRelocateBucket($bucket, $operationId, AdvanceRelocateBucketOperationRequest $postBody, $optParams = [])
  {
    $params = ['bucket' => $bucket, 'operationId' => $operationId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('advanceRelocateBucket', [$params]);
  }
  /**
   * Starts asynchronous cancellation on a long-running operation. The server
   * makes a best effort to cancel the operation, but success is not guaranteed.
   * (operations.cancel)
   *
   * @param string $bucket The parent bucket of the operation resource.
   * @param string $operationId The ID of the operation resource.
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function cancel($bucket, $operationId, $optParams = [])
  {
    $params = ['bucket' => $bucket, 'operationId' => $operationId];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params]);
  }
  /**
   * Gets the latest state of a long-running operation. (operations.get)
   *
   * @param string $bucket The parent bucket of the operation resource.
   * @param string $operationId The ID of the operation resource.
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function get($bucket, $operationId, $optParams = [])
  {
    $params = ['bucket' => $bucket, 'operationId' => $operationId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Lists operations that match the specified filter in the request.
   * (operations.listOperations)
   *
   * @param string $bucket Name of the bucket in which to look for operations.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter A filter to narrow down results to a preferred
   * subset. The filtering language is documented in more detail in
   * [AIP-160](https://google.aip.dev/160).
   * @opt_param int pageSize Maximum number of items to return in a single page of
   * responses. Fewer total results may be returned than requested. The service
   * uses this parameter or 100 items, whichever is smaller.
   * @opt_param string pageToken A previously-returned page token representing
   * part of the larger set of results to view.
   * @return GoogleLongrunningListOperationsResponse
   * @throws \Google\Service\Exception
   */
  public function listOperations($bucket, $optParams = [])
  {
    $params = ['bucket' => $bucket];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleLongrunningListOperationsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Operations::class, 'Google_Service_Storage_Resource_Operations');
