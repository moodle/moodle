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

namespace Google\Service\CertificateManager\Resource;

use Google\Service\CertificateManager\ListTrustConfigsResponse;
use Google\Service\CertificateManager\Operation;
use Google\Service\CertificateManager\TrustConfig;

/**
 * The "trustConfigs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $certificatemanagerService = new Google\Service\CertificateManager(...);
 *   $trustConfigs = $certificatemanagerService->projects_locations_trustConfigs;
 *  </code>
 */
class ProjectsLocationsTrustConfigs extends \Google\Service\Resource
{
  /**
   * Creates a new TrustConfig in a given project and location.
   * (trustConfigs.create)
   *
   * @param string $parent Required. The parent resource of the TrustConfig. Must
   * be in the format `projects/locations`.
   * @param TrustConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string trustConfigId Required. A user-provided name of the
   * TrustConfig. Must match the regexp `[a-z0-9-]{1,63}`.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, TrustConfig $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single TrustConfig. (trustConfigs.delete)
   *
   * @param string $name Required. A name of the TrustConfig to delete. Must be in
   * the format `projects/locations/trustConfigs`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. The current etag of the TrustConfig. If an
   * etag is provided and does not match the current etag of the resource,
   * deletion will be blocked and an ABORTED error will be returned.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Gets details of a single TrustConfig. (trustConfigs.get)
   *
   * @param string $name Required. A name of the TrustConfig to describe. Must be
   * in the format `projects/locations/trustConfigs`.
   * @param array $optParams Optional parameters.
   * @return TrustConfig
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], TrustConfig::class);
  }
  /**
   * Lists TrustConfigs in a given project and location.
   * (trustConfigs.listProjectsLocationsTrustConfigs)
   *
   * @param string $parent Required. The project and location from which the
   * TrustConfigs should be listed, specified in the format `projects/locations`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filter expression to restrict the
   * TrustConfigs returned.
   * @opt_param string orderBy Optional. A list of TrustConfig field names used to
   * specify the order of the returned results. The default sorting order is
   * ascending. To specify descending order for a field, add a suffix `" desc"`.
   * @opt_param int pageSize Optional. Maximum number of TrustConfigs to return
   * per call.
   * @opt_param string pageToken Optional. The value returned by the last
   * `ListTrustConfigsResponse`. Indicates that this is a continuation of a prior
   * `ListTrustConfigs` call, and that the system should return the next page of
   * data.
   * @return ListTrustConfigsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsTrustConfigs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListTrustConfigsResponse::class);
  }
  /**
   * Updates a TrustConfig. (trustConfigs.patch)
   *
   * @param string $name Identifier. A user-defined name of the trust config.
   * TrustConfig names must be unique globally and match pattern
   * `projects/locations/trustConfigs`.
   * @param TrustConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The update mask applies to the
   * resource. For the `FieldMask` definition, see
   * https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#fieldmask.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, TrustConfig $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsTrustConfigs::class, 'Google_Service_CertificateManager_Resource_ProjectsLocationsTrustConfigs');
