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

namespace Google\Service\AnalyticsHub\Resource;

use Google\Service\AnalyticsHub\GetIamPolicyRequest;
use Google\Service\AnalyticsHub\ListSubscriptionsResponse;
use Google\Service\AnalyticsHub\Operation;
use Google\Service\AnalyticsHub\Policy;
use Google\Service\AnalyticsHub\RefreshSubscriptionRequest;
use Google\Service\AnalyticsHub\RevokeSubscriptionRequest;
use Google\Service\AnalyticsHub\RevokeSubscriptionResponse;
use Google\Service\AnalyticsHub\SetIamPolicyRequest;
use Google\Service\AnalyticsHub\Subscription;

/**
 * The "subscriptions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $analyticshubService = new Google\Service\AnalyticsHub(...);
 *   $subscriptions = $analyticshubService->projects_locations_subscriptions;
 *  </code>
 */
class ProjectsLocationsSubscriptions extends \Google\Service\Resource
{
  /**
   * Deletes a subscription. (subscriptions.delete)
   *
   * @param string $name Required. Resource name of the subscription to delete.
   * e.g. projects/123/locations/us/subscriptions/456
   * @param array $optParams Optional parameters.
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
   * Gets the details of a Subscription. (subscriptions.get)
   *
   * @param string $name Required. Resource name of the subscription. e.g.
   * projects/123/locations/us/subscriptions/456
   * @param array $optParams Optional parameters.
   * @return Subscription
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Subscription::class);
  }
  /**
   * Gets the IAM policy. (subscriptions.getIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param GetIamPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function getIamPolicy($resource, GetIamPolicyRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('getIamPolicy', [$params], Policy::class);
  }
  /**
   * Lists all subscriptions in a given project and location.
   * (subscriptions.listProjectsLocationsSubscriptions)
   *
   * @param string $parent Required. The parent resource path of the subscription.
   * e.g. projects/myproject/locations/us
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter An expression for filtering the results of the
   * request. Eligible fields for filtering are: + `listing` + `data_exchange`
   * Alternatively, a literal wrapped in double quotes may be provided. This will
   * be checked for an exact match against both fields above. In all cases, the
   * full Data Exchange or Listing resource name must be provided. Some example of
   * using filters: +
   * data_exchange="projects/myproject/locations/us/dataExchanges/123" +
   * listing="projects/123/locations/us/dataExchanges/456/listings/789" +
   * "projects/myproject/locations/us/dataExchanges/123"
   * @opt_param int pageSize The maximum number of results to return in a single
   * response page.
   * @opt_param string pageToken Page token, returned by a previous call.
   * @return ListSubscriptionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsSubscriptions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListSubscriptionsResponse::class);
  }
  /**
   * Refreshes a Subscription to a Data Exchange. A Data Exchange can become stale
   * when a publisher adds or removes data. This is a long-running operation as it
   * may create many linked datasets. (subscriptions.refresh)
   *
   * @param string $name Required. Resource name of the Subscription to refresh.
   * e.g. `projects/subscriberproject/locations/us/subscriptions/123`
   * @param RefreshSubscriptionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function refresh($name, RefreshSubscriptionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('refresh', [$params], Operation::class);
  }
  /**
   * Revokes a given subscription. (subscriptions.revoke)
   *
   * @param string $name Required. Resource name of the subscription to revoke.
   * e.g. projects/123/locations/us/subscriptions/456
   * @param RevokeSubscriptionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return RevokeSubscriptionResponse
   * @throws \Google\Service\Exception
   */
  public function revoke($name, RevokeSubscriptionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('revoke', [$params], RevokeSubscriptionResponse::class);
  }
  /**
   * Sets the IAM policy. (subscriptions.setIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * specified. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param SetIamPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function setIamPolicy($resource, SetIamPolicyRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setIamPolicy', [$params], Policy::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsSubscriptions::class, 'Google_Service_AnalyticsHub_Resource_ProjectsLocationsSubscriptions');
