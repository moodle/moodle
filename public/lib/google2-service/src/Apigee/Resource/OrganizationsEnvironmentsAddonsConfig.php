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

namespace Google\Service\Apigee\Resource;

use Google\Service\Apigee\GoogleCloudApigeeV1SetAddonEnablementRequest;
use Google\Service\Apigee\GoogleLongrunningOperation;

/**
 * The "addonsConfig" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apigeeService = new Google\Service\Apigee(...);
 *   $addonsConfig = $apigeeService->organizations_environments_addonsConfig;
 *  </code>
 */
class OrganizationsEnvironmentsAddonsConfig extends \Google\Service\Resource
{
  /**
   * Updates an add-on enablement status of an environment.
   * (addonsConfig.setAddonEnablement)
   *
   * @param string $name Required. Name of the add-ons config. Must be in the
   * format of `/organizations/{org}/environments/{env}/addonsConfig`
   * @param GoogleCloudApigeeV1SetAddonEnablementRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function setAddonEnablement($name, GoogleCloudApigeeV1SetAddonEnablementRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setAddonEnablement', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsEnvironmentsAddonsConfig::class, 'Google_Service_Apigee_Resource_OrganizationsEnvironmentsAddonsConfig');
