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

namespace Google\Service\ChecksService\Resource;

use Google\Service\ChecksService\ChecksEmpty;
use Google\Service\ChecksService\ListOperationsResponse;
use Google\Service\ChecksService\Operation;

/**
 * The "operations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $checksService = new Google\Service\ChecksService(...);
 *   $operations = $checksService->projects_privacypolicy_operations;
 *  </code>
 */
class ProjectsPrivacypolicyOperations extends \Google\Service\Resource
{
  /**
   * Starts asynchronous cancellation on a long-running operation. The server
   * makes a best effort to cancel the operation, but success is not guaranteed.
   * If the server doesn't support this method, it returns
   * `google.rpc.Code.UNIMPLEMENTED`. Clients can use Operations.GetOperation or
   * other methods to check whether the cancellation succeeded or whether the
   * operation completed despite cancellation. On successful cancellation, the
   * operation is not deleted; instead, it becomes an operation with an
   * Operation.error value with a google.rpc.Status.code of 1, corresponding to
   * `Code.CANCELLED`. (operations.cancel)
   *
   * @param string $name The name of the operation resource to be cancelled.
   * @param array $optParams Optional parameters.
   * @return ChecksEmpty
   */
  public function cancel($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params], ChecksEmpty::class);
  }
  /**
   * Deletes a long-running operation. This method indicates that the client is no
   * longer interested in the operation result. It does not cancel the operation.
   * If the server doesn't support this method, it returns
   * `google.rpc.Code.UNIMPLEMENTED`. (operations.delete)
   *
   * @param string $name The name of the operation resource to be deleted.
   * @param array $optParams Optional parameters.
   * @return ChecksEmpty
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], ChecksEmpty::class);
  }
  /**
   * Gets the latest state of a long-running operation. Clients can use this
   * method to poll the operation result at intervals as recommended by the API
   * service. (operations.get)
   *
   * @param string $name The name of the operation resource.
   * @param array $optParams Optional parameters.
   * @return Operation
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Operation::class);
  }
  /**
   * Lists operations that match the specified filter in the request. If the
   * server doesn't support this method, it returns `UNIMPLEMENTED`.
   * (operations.listProjectsPrivacypolicyOperations)
   *
   * @param string $name The name of the operation's parent resource.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter The standard list filter.
   * @opt_param int pageSize The standard list page size.
   * @opt_param string pageToken The standard list page token.
   * @return ListOperationsResponse
   */
  public function listProjectsPrivacypolicyOperations($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListOperationsResponse::class);
  }
  /**
   * Waits until the specified long-running operation is done or reaches at most a
   * specified timeout, returning the latest state. If the operation is already
   * done, the latest state is immediately returned. If the timeout specified is
   * greater than the default HTTP/RPC timeout, the HTTP/RPC timeout is used. If
   * the server does not support this method, it returns
   * `google.rpc.Code.UNIMPLEMENTED`. Note that this method is on a best-effort
   * basis. It may return the latest state before the specified timeout (including
   * immediately), meaning even an immediate response is no guarantee that the
   * operation is done. (operations.wait)
   *
   * @param string $name The name of the operation resource to wait on.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string timeout The maximum duration to wait before timing out. If
   * left blank, the wait will be at most the time permitted by the underlying
   * HTTP/RPC protocol. If RPC context deadline is also specified, the shorter one
   * will be used.
   * @return Operation
   */
  public function wait($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('wait', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsPrivacypolicyOperations::class, 'Google_Service_ChecksService_Resource_ProjectsPrivacypolicyOperations');
