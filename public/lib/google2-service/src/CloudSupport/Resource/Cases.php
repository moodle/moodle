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

use Google\Service\CloudSupport\CloseCaseRequest;
use Google\Service\CloudSupport\CloudsupportCase;
use Google\Service\CloudSupport\EscalateCaseRequest;
use Google\Service\CloudSupport\ListCasesResponse;
use Google\Service\CloudSupport\SearchCasesResponse;

/**
 * The "cases" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudsupportService = new Google\Service\CloudSupport(...);
 *   $cases = $cloudsupportService->cases;
 *  </code>
 */
class Cases extends \Google\Service\Resource
{
  /**
   * Close a case. EXAMPLES: cURL: ```shell case="projects/some-
   * project/cases/43595344" curl \ --request POST \ --header "Authorization:
   * Bearer $(gcloud auth print-access-token)" \
   * "https://cloudsupport.googleapis.com/v2/$case:close" ``` Python: ```python
   * import googleapiclient.discovery api_version = "v2" supportApiService =
   * googleapiclient.discovery.build( serviceName="cloudsupport",
   * version=api_version, discoveryServiceUrl=f"https://cloudsupport.googleapis.co
   * m/$discovery/rest?version={api_version}", ) request =
   * supportApiService.cases().close( name="projects/some-project/cases/43595344"
   * ) print(request.execute()) ``` (cases.close)
   *
   * @param string $name Required. The name of the case to close.
   * @param CloseCaseRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CloudsupportCase
   * @throws \Google\Service\Exception
   */
  public function close($name, CloseCaseRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('close', [$params], CloudsupportCase::class);
  }
  /**
   * Create a new case and associate it with a parent. It must have the following
   * fields set: `display_name`, `description`, `classification`, and `priority`.
   * If you're just testing the API and don't want to route your case to an agent,
   * set `testCase=true`. EXAMPLES: cURL: ```shell parent="projects/some-project"
   * curl \ --request POST \ --header "Authorization: Bearer $(gcloud auth print-
   * access-token)" \ --header 'Content-Type: application/json' \ --data '{
   * "display_name": "Test case created by me.", "description": "a random test
   * case, feel free to close", "classification": { "id": "100IK2AKCLHMGRJ9CDGMOCG
   * P8DM6UTB4BT262T31BT1M2T31DHNMENPO6KS36CPJ786L2TBFEHGN6NPI64R3CDHN8880G08I1H3M
   * URR7DHII0GRCDTQM8" }, "time_zone": "-07:00", "subscriber_email_addresses": [
   * "foo@domain.com", "bar@domain.com" ], "testCase": true, "priority": "P3" }' \
   * "https://cloudsupport.googleapis.com/v2/$parent/cases" ``` Python: ```python
   * import googleapiclient.discovery api_version = "v2" supportApiService =
   * googleapiclient.discovery.build( serviceName="cloudsupport",
   * version=api_version, discoveryServiceUrl=f"https://cloudsupport.googleapis.co
   * m/$discovery/rest?version={api_version}", ) request =
   * supportApiService.cases().create( parent="projects/some-project", body={
   * "displayName": "A Test Case", "description": "This is a test case.",
   * "testCase": True, "priority": "P2", "classification": { "id": "100IK2AKCLHMGR
   * J9CDGMOCGP8DM6UTB4BT262T31BT1M2T31DHNMENPO6KS36CPJ786L2TBFEHGN6NPI64R3CDHN888
   * 0G08I1H3MURR7DHII0GRCDTQM8" }, }, ) print(request.execute()) ```
   * (cases.create)
   *
   * @param string $parent Required. The name of the parent under which the case
   * should be created.
   * @param CloudsupportCase $postBody
   * @param array $optParams Optional parameters.
   * @return CloudsupportCase
   * @throws \Google\Service\Exception
   */
  public function create($parent, CloudsupportCase $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], CloudsupportCase::class);
  }
  /**
   * Escalate a case, starting the Google Cloud Support escalation management
   * process. This operation is only available for some support services. Go to
   * https://cloud.google.com/support and look for 'Technical support escalations'
   * in the feature list to find out which ones let you do that. EXAMPLES: cURL:
   * ```shell case="projects/some-project/cases/43595344" curl \ --request POST \
   * --header "Authorization: Bearer $(gcloud auth print-access-token)" \ --header
   * "Content-Type: application/json" \ --data '{ "escalation": { "reason":
   * "BUSINESS_IMPACT", "justification": "This is a test escalation." } }' \
   * "https://cloudsupport.googleapis.com/v2/$case:escalate" ``` Python: ```python
   * import googleapiclient.discovery api_version = "v2" supportApiService =
   * googleapiclient.discovery.build( serviceName="cloudsupport",
   * version=api_version, discoveryServiceUrl=f"https://cloudsupport.googleapis.co
   * m/$discovery/rest?version={api_version}", ) request =
   * supportApiService.cases().escalate( name="projects/some-
   * project/cases/43595344", body={ "escalation": { "reason": "BUSINESS_IMPACT",
   * "justification": "This is a test escalation.", }, }, )
   * print(request.execute()) ``` (cases.escalate)
   *
   * @param string $name Required. The name of the case to be escalated.
   * @param EscalateCaseRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CloudsupportCase
   * @throws \Google\Service\Exception
   */
  public function escalate($name, EscalateCaseRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('escalate', [$params], CloudsupportCase::class);
  }
  /**
   * Retrieve a case. EXAMPLES: cURL: ```shell case="projects/some-
   * project/cases/16033687" curl \ --header "Authorization: Bearer $(gcloud auth
   * print-access-token)" \ "https://cloudsupport.googleapis.com/v2/$case" ```
   * Python: ```python import googleapiclient.discovery api_version = "v2"
   * supportApiService = googleapiclient.discovery.build(
   * serviceName="cloudsupport", version=api_version, discoveryServiceUrl=f"https:
   * //cloudsupport.googleapis.com/$discovery/rest?version={api_version}", )
   * request = supportApiService.cases().get( name="projects/some-
   * project/cases/43595344", ) print(request.execute()) ``` (cases.get)
   *
   * @param string $name Required. The full name of a case to be retrieved.
   * @param array $optParams Optional parameters.
   * @return CloudsupportCase
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], CloudsupportCase::class);
  }
  /**
   * Retrieve all cases under a parent, but not its children. For example, listing
   * cases under an organization only returns the cases that are directly parented
   * by that organization. To retrieve cases under an organization and its
   * projects, use `cases.search`. EXAMPLES: cURL: ```shell parent="projects/some-
   * project" curl \ --header "Authorization: Bearer $(gcloud auth print-access-
   * token)" \ "https://cloudsupport.googleapis.com/v2/$parent/cases" ``` Python:
   * ```python import googleapiclient.discovery api_version = "v2"
   * supportApiService = googleapiclient.discovery.build(
   * serviceName="cloudsupport", version=api_version, discoveryServiceUrl=f"https:
   * //cloudsupport.googleapis.com/$discovery/rest?version={api_version}", )
   * request = supportApiService.cases().list(parent="projects/some-project")
   * print(request.execute()) ``` (cases.listCases)
   *
   * @param string $parent Required. The name of a parent to list cases under.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter An expression used to filter cases. If it's an empty
   * string, then no filtering happens. Otherwise, the endpoint returns the cases
   * that match the filter. Expressions use the following fields separated by
   * `AND` and specified with `=`: - `state`: Can be `OPEN` or `CLOSED`. -
   * `priority`: Can be `P0`, `P1`, `P2`, `P3`, or `P4`. You can specify multiple
   * values for priority using the `OR` operator. For example, `priority=P1 OR
   * priority=P2`. - `creator.email`: The email address of the case creator.
   * EXAMPLES: - `state=CLOSED` - `state=OPEN AND
   * creator.email="tester@example.com"` - `state=OPEN AND (priority=P0 OR
   * priority=P1)`
   * @opt_param int pageSize The maximum number of cases fetched with each
   * request. Defaults to 10.
   * @opt_param string pageToken A token identifying the page of results to
   * return. If unspecified, the first page is retrieved.
   * @return ListCasesResponse
   * @throws \Google\Service\Exception
   */
  public function listCases($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListCasesResponse::class);
  }
  /**
   * Update a case. Only some fields can be updated. EXAMPLES: cURL: ```shell
   * case="projects/some-project/cases/43595344" curl \ --request PATCH \ --header
   * "Authorization: Bearer $(gcloud auth print-access-token)" \ --header
   * "Content-Type: application/json" \ --data '{ "priority": "P1" }' \
   * "https://cloudsupport.googleapis.com/v2/$case?updateMask=priority" ```
   * Python: ```python import googleapiclient.discovery api_version = "v2"
   * supportApiService = googleapiclient.discovery.build(
   * serviceName="cloudsupport", version=api_version, discoveryServiceUrl=f"https:
   * //cloudsupport.googleapis.com/$discovery/rest?version={api_version}", )
   * request = supportApiService.cases().patch( name="projects/some-
   * project/cases/43112854", body={ "displayName": "This is Now a New Title",
   * "priority": "P2", }, ) print(request.execute()) ``` (cases.patch)
   *
   * @param string $name Identifier. The resource name for the case.
   * @param CloudsupportCase $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask A list of attributes of the case that should be
   * updated. Supported values are `priority`, `display_name`, and
   * `subscriber_email_addresses`. If no fields are specified, all supported
   * fields are updated. Be careful - if you do not provide a field mask, then you
   * might accidentally clear some fields. For example, if you leave the field
   * mask empty and do not provide a value for `subscriber_email_addresses`, then
   * `subscriber_email_addresses` is updated to empty.
   * @return CloudsupportCase
   * @throws \Google\Service\Exception
   */
  public function patch($name, CloudsupportCase $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], CloudsupportCase::class);
  }
  /**
   * Search for cases using a query. EXAMPLES: cURL: ```shell
   * parent="projects/some-project" curl \ --header "Authorization: Bearer
   * $(gcloud auth print-access-token)" \
   * "https://cloudsupport.googleapis.com/v2/$parent/cases:search" ``` Python:
   * ```python import googleapiclient.discovery api_version = "v2"
   * supportApiService = googleapiclient.discovery.build(
   * serviceName="cloudsupport", version=api_version, discoveryServiceUrl=f"https:
   * //cloudsupport.googleapis.com/$discovery/rest?version={api_version}", )
   * request = supportApiService.cases().search( parent="projects/some-project",
   * query="state=OPEN" ) print(request.execute()) ``` (cases.search)
   *
   * @param string $parent The name of the parent resource to search for cases
   * under.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of cases fetched with each
   * request. The default page size is 10.
   * @opt_param string pageToken A token identifying the page of results to
   * return. If unspecified, the first page is retrieved.
   * @opt_param string query An expression used to filter cases. Expressions use
   * the following fields separated by `AND` and specified with `=`: -
   * `organization`: An organization name in the form `organizations/`. -
   * `project`: A project name in the form `projects/`. - `state`: Can be `OPEN`
   * or `CLOSED`. - `priority`: Can be `P0`, `P1`, `P2`, `P3`, or `P4`. You can
   * specify multiple values for priority using the `OR` operator. For example,
   * `priority=P1 OR priority=P2`. - `creator.email`: The email address of the
   * case creator. You must specify either `organization` or `project`. To search
   * across `displayName`, `description`, and comments, use a global restriction
   * with no keyword or operator. For example, `"my search"`. To search only cases
   * updated after a certain date, use `update_time` restricted with that
   * particular date, time, and timezone in ISO datetime format. For example,
   * `update_time>"2020-01-01T00:00:00-05:00"`. `update_time` only supports the
   * greater than operator (`>`). Examples: -
   * `organization="organizations/123456789"` - `project="projects/my-project-id"`
   * - `project="projects/123456789"` - `organization="organizations/123456789"
   * AND state=CLOSED` - `project="projects/my-project-id" AND
   * creator.email="tester@example.com"` - `project="projects/my-project-id" AND
   * (priority=P0 OR priority=P1)`
   * @return SearchCasesResponse
   * @throws \Google\Service\Exception
   */
  public function search($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('search', [$params], SearchCasesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Cases::class, 'Google_Service_CloudSupport_Resource_Cases');
