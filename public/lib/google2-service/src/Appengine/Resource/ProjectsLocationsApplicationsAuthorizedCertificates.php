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

namespace Google\Service\Appengine\Resource;

use Google\Service\Appengine\AppengineEmpty;
use Google\Service\Appengine\AuthorizedCertificate;
use Google\Service\Appengine\ListAuthorizedCertificatesResponse;

/**
 * The "authorizedCertificates" collection of methods.
 * Typical usage is:
 *  <code>
 *   $appengineService = new Google\Service\Appengine(...);
 *   $authorizedCertificates = $appengineService->projects_locations_applications_authorizedCertificates;
 *  </code>
 */
class ProjectsLocationsApplicationsAuthorizedCertificates extends \Google\Service\Resource
{
  /**
   * Uploads the specified SSL certificate. (authorizedCertificates.create)
   *
   * @param string $projectsId Part of `parent`. Required. Name of the parent
   * Application resource. Example: apps/myapp.
   * @param string $locationsId Part of `parent`. See documentation of
   * `projectsId`.
   * @param string $applicationsId Part of `parent`. See documentation of
   * `projectsId`.
   * @param AuthorizedCertificate $postBody
   * @param array $optParams Optional parameters.
   * @return AuthorizedCertificate
   * @throws \Google\Service\Exception
   */
  public function create($projectsId, $locationsId, $applicationsId, AuthorizedCertificate $postBody, $optParams = [])
  {
    $params = ['projectsId' => $projectsId, 'locationsId' => $locationsId, 'applicationsId' => $applicationsId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], AuthorizedCertificate::class);
  }
  /**
   * Deletes the specified SSL certificate. (authorizedCertificates.delete)
   *
   * @param string $projectsId Part of `name`. Required. Name of the resource to
   * delete. Example: apps/myapp/authorizedCertificates/12345.
   * @param string $locationsId Part of `name`. See documentation of `projectsId`.
   * @param string $applicationsId Part of `name`. See documentation of
   * `projectsId`.
   * @param string $authorizedCertificatesId Part of `name`. See documentation of
   * `projectsId`.
   * @param array $optParams Optional parameters.
   * @return AppengineEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($projectsId, $locationsId, $applicationsId, $authorizedCertificatesId, $optParams = [])
  {
    $params = ['projectsId' => $projectsId, 'locationsId' => $locationsId, 'applicationsId' => $applicationsId, 'authorizedCertificatesId' => $authorizedCertificatesId];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], AppengineEmpty::class);
  }
  /**
   * Gets the specified SSL certificate. (authorizedCertificates.get)
   *
   * @param string $projectsId Part of `name`. Required. Name of the resource
   * requested. Example: apps/myapp/authorizedCertificates/12345.
   * @param string $locationsId Part of `name`. See documentation of `projectsId`.
   * @param string $applicationsId Part of `name`. See documentation of
   * `projectsId`.
   * @param string $authorizedCertificatesId Part of `name`. See documentation of
   * `projectsId`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string view Controls the set of fields returned in the GET
   * response.
   * @return AuthorizedCertificate
   * @throws \Google\Service\Exception
   */
  public function get($projectsId, $locationsId, $applicationsId, $authorizedCertificatesId, $optParams = [])
  {
    $params = ['projectsId' => $projectsId, 'locationsId' => $locationsId, 'applicationsId' => $applicationsId, 'authorizedCertificatesId' => $authorizedCertificatesId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], AuthorizedCertificate::class);
  }
  /**
   * Lists all SSL certificates the user is authorized to administer. (authorizedC
   * ertificates.listProjectsLocationsApplicationsAuthorizedCertificates)
   *
   * @param string $projectsId Part of `parent`. Required. Name of the parent
   * Application resource. Example: apps/myapp.
   * @param string $locationsId Part of `parent`. See documentation of
   * `projectsId`.
   * @param string $applicationsId Part of `parent`. See documentation of
   * `projectsId`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum results to return per page.
   * @opt_param string pageToken Continuation token for fetching the next page of
   * results.
   * @opt_param string view Controls the set of fields returned in the LIST
   * response.
   * @return ListAuthorizedCertificatesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsApplicationsAuthorizedCertificates($projectsId, $locationsId, $applicationsId, $optParams = [])
  {
    $params = ['projectsId' => $projectsId, 'locationsId' => $locationsId, 'applicationsId' => $applicationsId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAuthorizedCertificatesResponse::class);
  }
  /**
   * Updates the specified SSL certificate. To renew a certificate and maintain
   * its existing domain mappings, update certificate_data with a new certificate.
   * The new certificate must be applicable to the same domains as the original
   * certificate. The certificate display_name may also be updated.
   * (authorizedCertificates.patch)
   *
   * @param string $projectsId Part of `name`. Required. Name of the resource to
   * update. Example: apps/myapp/authorizedCertificates/12345.
   * @param string $locationsId Part of `name`. See documentation of `projectsId`.
   * @param string $applicationsId Part of `name`. See documentation of
   * `projectsId`.
   * @param string $authorizedCertificatesId Part of `name`. See documentation of
   * `projectsId`.
   * @param AuthorizedCertificate $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Standard field mask for the set of fields to be
   * updated. Updates are only supported on the certificate_raw_data and
   * display_name fields.
   * @return AuthorizedCertificate
   * @throws \Google\Service\Exception
   */
  public function patch($projectsId, $locationsId, $applicationsId, $authorizedCertificatesId, AuthorizedCertificate $postBody, $optParams = [])
  {
    $params = ['projectsId' => $projectsId, 'locationsId' => $locationsId, 'applicationsId' => $applicationsId, 'authorizedCertificatesId' => $authorizedCertificatesId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], AuthorizedCertificate::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsApplicationsAuthorizedCertificates::class, 'Google_Service_Appengine_Resource_ProjectsLocationsApplicationsAuthorizedCertificates');
