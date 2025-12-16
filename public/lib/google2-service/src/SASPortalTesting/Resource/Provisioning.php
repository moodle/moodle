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

namespace Google\Service\SASPortalTesting\Resource;

use Google\Service\SASPortalTesting\SasPortalProvisionDeploymentRequest;
use Google\Service\SASPortalTesting\SasPortalProvisionDeploymentResponse;

/**
 * The "provisioning" collection of methods.
 * Typical usage is:
 *  <code>
 *   $prod_tt_sasportalService = new Google\Service\SASPortalTesting(...);
 *   $provisioning = $prod_tt_sasportalService->provisioning;
 *  </code>
 */
class Provisioning extends \Google\Service\Resource
{
  /**
   * Creates a new SAS deployment through the GCP workflow. Creates a SAS
   * organization if an organization match is not found.
   * (provisioning.provisionDeployment)
   *
   * @param SasPortalProvisionDeploymentRequest $postBody
   * @param array $optParams Optional parameters.
   * @return SasPortalProvisionDeploymentResponse
   */
  public function provisionDeployment(SasPortalProvisionDeploymentRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('provisionDeployment', [$params], SasPortalProvisionDeploymentResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Provisioning::class, 'Google_Service_SASPortalTesting_Resource_Provisioning');
