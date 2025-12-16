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

namespace Google\Service\NetworkSecurity\Resource;

use Google\Service\NetworkSecurity\DnsThreatDetector;
use Google\Service\NetworkSecurity\ListDnsThreatDetectorsResponse;
use Google\Service\NetworkSecurity\NetworksecurityEmpty;

/**
 * The "dnsThreatDetectors" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networksecurityService = new Google\Service\NetworkSecurity(...);
 *   $dnsThreatDetectors = $networksecurityService->projects_locations_dnsThreatDetectors;
 *  </code>
 */
class ProjectsLocationsDnsThreatDetectors extends \Google\Service\Resource
{
  /**
   * Creates a new DnsThreatDetector in a given project and location.
   * (dnsThreatDetectors.create)
   *
   * @param string $parent Required. The value for the parent of the
   * DnsThreatDetector resource.
   * @param DnsThreatDetector $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string dnsThreatDetectorId Optional. The ID of the requesting
   * DnsThreatDetector object. If this field is not supplied, the service
   * generates an identifier.
   * @return DnsThreatDetector
   * @throws \Google\Service\Exception
   */
  public function create($parent, DnsThreatDetector $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], DnsThreatDetector::class);
  }
  /**
   * Deletes a single DnsThreatDetector. (dnsThreatDetectors.delete)
   *
   * @param string $name Required. Name of the DnsThreatDetector resource.
   * @param array $optParams Optional parameters.
   * @return NetworksecurityEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], NetworksecurityEmpty::class);
  }
  /**
   * Gets the details of a single DnsThreatDetector. (dnsThreatDetectors.get)
   *
   * @param string $name Required. Name of the DnsThreatDetector resource.
   * @param array $optParams Optional parameters.
   * @return DnsThreatDetector
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], DnsThreatDetector::class);
  }
  /**
   * Lists DnsThreatDetectors in a given project and location.
   * (dnsThreatDetectors.listProjectsLocationsDnsThreatDetectors)
   *
   * @param string $parent Required. The parent value for
   * `ListDnsThreatDetectorsRequest`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The requested page size. The server may
   * return fewer items than requested. If unspecified, the server picks an
   * appropriate default.
   * @opt_param string pageToken Optional. A page token received from a previous
   * `ListDnsThreatDetectorsRequest` call. Provide this to retrieve the subsequent
   * page.
   * @return ListDnsThreatDetectorsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDnsThreatDetectors($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListDnsThreatDetectorsResponse::class);
  }
  /**
   * Updates a single DnsThreatDetector. (dnsThreatDetectors.patch)
   *
   * @param string $name Immutable. Identifier. Name of the DnsThreatDetector
   * resource.
   * @param DnsThreatDetector $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The field mask is used to specify the
   * fields to be overwritten in the DnsThreatDetector resource by the update. The
   * fields specified in the update_mask are relative to the resource, not the
   * full request. A field will be overwritten if it is in the mask. If the mask
   * is not provided then all fields present in the request will be overwritten.
   * @return DnsThreatDetector
   * @throws \Google\Service\Exception
   */
  public function patch($name, DnsThreatDetector $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], DnsThreatDetector::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDnsThreatDetectors::class, 'Google_Service_NetworkSecurity_Resource_ProjectsLocationsDnsThreatDetectors');
