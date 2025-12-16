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

namespace Google\Service\DeveloperConnect\Resource;

use Google\Service\DeveloperConnect\DeveloperconnectEmpty;
use Google\Service\DeveloperConnect\FetchGitRefsResponse;
use Google\Service\DeveloperConnect\FetchReadTokenRequest;
use Google\Service\DeveloperConnect\FetchReadTokenResponse;
use Google\Service\DeveloperConnect\FetchReadWriteTokenRequest;
use Google\Service\DeveloperConnect\FetchReadWriteTokenResponse;
use Google\Service\DeveloperConnect\GitRepositoryLink;
use Google\Service\DeveloperConnect\ListGitRepositoryLinksResponse;
use Google\Service\DeveloperConnect\Operation;
use Google\Service\DeveloperConnect\ProcessBitbucketCloudWebhookRequest;
use Google\Service\DeveloperConnect\ProcessBitbucketDataCenterWebhookRequest;
use Google\Service\DeveloperConnect\ProcessGitLabEnterpriseWebhookRequest;
use Google\Service\DeveloperConnect\ProcessGitLabWebhookRequest;

/**
 * The "gitRepositoryLinks" collection of methods.
 * Typical usage is:
 *  <code>
 *   $developerconnectService = new Google\Service\DeveloperConnect(...);
 *   $gitRepositoryLinks = $developerconnectService->projects_locations_connections_gitRepositoryLinks;
 *  </code>
 */
class ProjectsLocationsConnectionsGitRepositoryLinks extends \Google\Service\Resource
{
  /**
   * Creates a GitRepositoryLink. Upon linking a Git Repository, Developer Connect
   * will configure the Git Repository to send webhook events to Developer
   * Connect. Connections that use Firebase GitHub Application will have events
   * forwarded to the Firebase service. Connections that use Gemini Code Assist
   * will have events forwarded to Gemini Code Assist service. All other
   * Connections will have events forwarded to Cloud Build.
   * (gitRepositoryLinks.create)
   *
   * @param string $parent Required. Value for parent.
   * @param GitRepositoryLink $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string gitRepositoryLinkId Required. The ID to use for the
   * repository, which will become the final component of the repository's
   * resource name. This ID should be unique in the connection. Allows
   * alphanumeric characters and any of -._~%!$&'()*+,;=@.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes since the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param bool validateOnly Optional. If set, validate the request, but do
   * not actually post it.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GitRepositoryLink $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single GitRepositoryLink. (gitRepositoryLinks.delete)
   *
   * @param string $name Required. Name of the resource
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. This checksum is computed by the server
   * based on the value of other fields, and may be sent on update and delete
   * requests to ensure the client has an up-to-date value before proceeding.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes after the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param bool validateOnly Optional. If set, validate the request, but do
   * not actually post it.
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
   * Fetch the list of branches or tags for a given repository.
   * (gitRepositoryLinks.fetchGitRefs)
   *
   * @param string $gitRepositoryLink Required. The resource name of
   * GitRepositoryLink in the format
   * `projects/locations/connections/gitRepositoryLinks`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. Number of results to return in the list.
   * Default to 20.
   * @opt_param string pageToken Optional. Page start.
   * @opt_param string refType Required. Type of refs to fetch.
   * @return FetchGitRefsResponse
   * @throws \Google\Service\Exception
   */
  public function fetchGitRefs($gitRepositoryLink, $optParams = [])
  {
    $params = ['gitRepositoryLink' => $gitRepositoryLink];
    $params = array_merge($params, $optParams);
    return $this->call('fetchGitRefs', [$params], FetchGitRefsResponse::class);
  }
  /**
   * Fetches read token of a given gitRepositoryLink.
   * (gitRepositoryLinks.fetchReadToken)
   *
   * @param string $gitRepositoryLink Required. The resource name of the
   * gitRepositoryLink in the format
   * `projects/locations/connections/gitRepositoryLinks`.
   * @param FetchReadTokenRequest $postBody
   * @param array $optParams Optional parameters.
   * @return FetchReadTokenResponse
   * @throws \Google\Service\Exception
   */
  public function fetchReadToken($gitRepositoryLink, FetchReadTokenRequest $postBody, $optParams = [])
  {
    $params = ['gitRepositoryLink' => $gitRepositoryLink, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('fetchReadToken', [$params], FetchReadTokenResponse::class);
  }
  /**
   * Fetches read/write token of a given gitRepositoryLink.
   * (gitRepositoryLinks.fetchReadWriteToken)
   *
   * @param string $gitRepositoryLink Required. The resource name of the
   * gitRepositoryLink in the format
   * `projects/locations/connections/gitRepositoryLinks`.
   * @param FetchReadWriteTokenRequest $postBody
   * @param array $optParams Optional parameters.
   * @return FetchReadWriteTokenResponse
   * @throws \Google\Service\Exception
   */
  public function fetchReadWriteToken($gitRepositoryLink, FetchReadWriteTokenRequest $postBody, $optParams = [])
  {
    $params = ['gitRepositoryLink' => $gitRepositoryLink, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('fetchReadWriteToken', [$params], FetchReadWriteTokenResponse::class);
  }
  /**
   * Gets details of a single GitRepositoryLink. (gitRepositoryLinks.get)
   *
   * @param string $name Required. Name of the resource
   * @param array $optParams Optional parameters.
   * @return GitRepositoryLink
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GitRepositoryLink::class);
  }
  /**
   * Lists GitRepositoryLinks in a given project, location, and connection.
   * (gitRepositoryLinks.listProjectsLocationsConnectionsGitRepositoryLinks)
   *
   * @param string $parent Required. Parent value for
   * ListGitRepositoryLinksRequest
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results
   * @opt_param string orderBy Optional. Hint for how to order the results
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will pick an appropriate
   * default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListGitRepositoryLinksResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsConnectionsGitRepositoryLinks($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListGitRepositoryLinksResponse::class);
  }
  /**
   * ProcessBitbucketCloudWebhook is called by the external Bitbucket Cloud
   * instances for notifying events.
   * (gitRepositoryLinks.processBitbucketCloudWebhook)
   *
   * @param string $name Required. The GitRepositoryLink where the webhook will be
   * received. Format: `projects/locations/connections/gitRepositoryLinks`.
   * @param ProcessBitbucketCloudWebhookRequest $postBody
   * @param array $optParams Optional parameters.
   * @return DeveloperconnectEmpty
   * @throws \Google\Service\Exception
   */
  public function processBitbucketCloudWebhook($name, ProcessBitbucketCloudWebhookRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('processBitbucketCloudWebhook', [$params], DeveloperconnectEmpty::class);
  }
  /**
   * ProcessBitbucketDataCenterWebhook is called by the external Bitbucket Data
   * Center instances for notifying events.
   * (gitRepositoryLinks.processBitbucketDataCenterWebhook)
   *
   * @param string $name Required. The GitRepositoryLink where the webhook will be
   * received. Format: `projects/locations/connections/gitRepositoryLinks`.
   * @param ProcessBitbucketDataCenterWebhookRequest $postBody
   * @param array $optParams Optional parameters.
   * @return DeveloperconnectEmpty
   * @throws \Google\Service\Exception
   */
  public function processBitbucketDataCenterWebhook($name, ProcessBitbucketDataCenterWebhookRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('processBitbucketDataCenterWebhook', [$params], DeveloperconnectEmpty::class);
  }
  /**
   * ProcessGitLabEnterpriseWebhook is called by the external GitLab Enterprise
   * instances for notifying events.
   * (gitRepositoryLinks.processGitLabEnterpriseWebhook)
   *
   * @param string $name Required. The GitRepositoryLink resource where the
   * webhook will be received. Format:
   * `projects/locations/connections/gitRepositoryLinks`.
   * @param ProcessGitLabEnterpriseWebhookRequest $postBody
   * @param array $optParams Optional parameters.
   * @return DeveloperconnectEmpty
   * @throws \Google\Service\Exception
   */
  public function processGitLabEnterpriseWebhook($name, ProcessGitLabEnterpriseWebhookRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('processGitLabEnterpriseWebhook', [$params], DeveloperconnectEmpty::class);
  }
  /**
   * ProcessGitLabWebhook is called by the GitLab.com for notifying events.
   * (gitRepositoryLinks.processGitLabWebhook)
   *
   * @param string $name Required. The GitRepositoryLink resource where the
   * webhook will be received. Format:
   * `projects/locations/connections/gitRepositoryLinks`.
   * @param ProcessGitLabWebhookRequest $postBody
   * @param array $optParams Optional parameters.
   * @return DeveloperconnectEmpty
   * @throws \Google\Service\Exception
   */
  public function processGitLabWebhook($name, ProcessGitLabWebhookRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('processGitLabWebhook', [$params], DeveloperconnectEmpty::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsConnectionsGitRepositoryLinks::class, 'Google_Service_DeveloperConnect_Resource_ProjectsLocationsConnectionsGitRepositoryLinks');
