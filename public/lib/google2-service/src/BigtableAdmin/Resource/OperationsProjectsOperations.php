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

namespace Google\Service\BigtableAdmin\Resource;

use Google\Service\BigtableAdmin\ListOperationsResponse;

/**
 * The "operations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $bigtableadminService = new Google\Service\BigtableAdmin(...);
 *   $operations = $bigtableadminService->operations_projects_operations;
 *  </code>
 */
class OperationsProjectsOperations extends \Google\Service\Resource
{
  /**
   * Lists operations that match the specified filter in the request. If the
   * server doesn't support this method, it returns `UNIMPLEMENTED`.
   * (operations.listOperationsProjectsOperations)
   *
   * @param string $name The name of the operation's parent resource.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter The standard list filter.
   * @opt_param int pageSize The standard list page size.
   * @opt_param string pageToken The standard list page token.
   * @opt_param bool returnPartialSuccess When set to `true`, operations that are
   * reachable are returned as normal, and those that are unreachable are returned
   * in the [ListOperationsResponse.unreachable] field. This can only be `true`
   * when reading across collections e.g. when `parent` is set to
   * `"projects/example/locations/-"`. This field is not by default supported and
   * will result in an `UNIMPLEMENTED` error if set unless explicitly documented
   * otherwise in service or product specific documentation.
   * @return ListOperationsResponse
   * @throws \Google\Service\Exception
   */
  public function listOperationsProjectsOperations($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListOperationsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OperationsProjectsOperations::class, 'Google_Service_BigtableAdmin_Resource_OperationsProjectsOperations');
