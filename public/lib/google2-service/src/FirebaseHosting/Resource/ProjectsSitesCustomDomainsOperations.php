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

namespace Google\Service\FirebaseHosting\Resource;

use Google\Service\FirebaseHosting\CancelOperationRequest;
use Google\Service\FirebaseHosting\FirebasehostingEmpty;

/**
 * The "operations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $firebasehostingService = new Google\Service\FirebaseHosting(...);
 *   $operations = $firebasehostingService->projects_sites_customDomains_operations;
 *  </code>
 */
class ProjectsSitesCustomDomainsOperations extends \Google\Service\Resource
{
  /**
   * CancelOperation is a part of the google.longrunning.Operations interface, but
   * is not implemented for CustomDomain resources. (operations.cancel)
   *
   * @param string $name The name of the operation resource to be cancelled.
   * @param CancelOperationRequest $postBody
   * @param array $optParams Optional parameters.
   * @return FirebasehostingEmpty
   * @throws \Google\Service\Exception
   */
  public function cancel($name, CancelOperationRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params], FirebasehostingEmpty::class);
  }
  /**
   * DeleteOperation is a part of the google.longrunning.Operations interface, but
   * is not implemented for CustomDomain resources. (operations.delete)
   *
   * @param string $name The name of the operation resource to be deleted.
   * @param array $optParams Optional parameters.
   * @return FirebasehostingEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], FirebasehostingEmpty::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsSitesCustomDomainsOperations::class, 'Google_Service_FirebaseHosting_Resource_ProjectsSitesCustomDomainsOperations');
