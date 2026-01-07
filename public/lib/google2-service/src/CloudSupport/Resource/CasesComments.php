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

namespace Google\Service\CloudSupport\Resource;

use Google\Service\CloudSupport\Comment;
use Google\Service\CloudSupport\ListCommentsResponse;

/**
 * The "comments" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudsupportService = new Google\Service\CloudSupport(...);
 *   $comments = $cloudsupportService->cases_comments;
 *  </code>
 */
class CasesComments extends \Google\Service\Resource
{
  /**
   * Add a new comment to a case. The comment must have the following fields set:
   * `body`. EXAMPLES: cURL: ```shell case="projects/some-project/cases/43591344"
   * curl \ --request POST \ --header "Authorization: Bearer $(gcloud auth print-
   * access-token)" \ --header 'Content-Type: application/json' \ --data '{
   * "body": "This is a test comment." }' \
   * "https://cloudsupport.googleapis.com/v2/$case/comments" ``` Python: ```python
   * import googleapiclient.discovery api_version = "v2" supportApiService =
   * googleapiclient.discovery.build( serviceName="cloudsupport",
   * version=api_version, discoveryServiceUrl=f"https://cloudsupport.googleapis.co
   * m/$discovery/rest?version={api_version}", ) request = (
   * supportApiService.cases() .comments() .create( parent="projects/some-
   * project/cases/43595344", body={"body": "This is a test comment."}, ) )
   * print(request.execute()) ``` (comments.create)
   *
   * @param string $parent Required. The name of the case to which the comment
   * should be added.
   * @param Comment $postBody
   * @param array $optParams Optional parameters.
   * @return Comment
   * @throws \Google\Service\Exception
   */
  public function create($parent, Comment $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Comment::class);
  }
  /**
   * List all the comments associated with a case. EXAMPLES: cURL: ```shell
   * case="projects/some-project/cases/43595344" curl \ --header "Authorization:
   * Bearer $(gcloud auth print-access-token)" \
   * "https://cloudsupport.googleapis.com/v2/$case/comments" ``` Python: ```python
   * import googleapiclient.discovery api_version = "v2" supportApiService =
   * googleapiclient.discovery.build( serviceName="cloudsupport",
   * version=api_version, discoveryServiceUrl=f"https://cloudsupport.googleapis.co
   * m/$discovery/rest?version={api_version}", ) request = (
   * supportApiService.cases() .comments() .list(parent="projects/some-
   * project/cases/43595344") ) print(request.execute()) ```
   * (comments.listCasesComments)
   *
   * @param string $parent Required. The name of the case for which to list
   * comments.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of comments to fetch. Defaults to
   * 10.
   * @opt_param string pageToken A token identifying the page of results to
   * return. If unspecified, the first page is returned.
   * @return ListCommentsResponse
   * @throws \Google\Service\Exception
   */
  public function listCasesComments($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListCommentsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CasesComments::class, 'Google_Service_CloudSupport_Resource_CasesComments');
