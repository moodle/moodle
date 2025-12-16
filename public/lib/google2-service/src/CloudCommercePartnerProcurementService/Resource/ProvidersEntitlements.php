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

namespace Google\Service\CloudCommercePartnerProcurementService\Resource;

use Google\Service\CloudCommercePartnerProcurementService\ApproveEntitlementPlanChangeRequest;
use Google\Service\CloudCommercePartnerProcurementService\ApproveEntitlementRequest;
use Google\Service\CloudCommercePartnerProcurementService\CloudcommerceprocurementEmpty;
use Google\Service\CloudCommercePartnerProcurementService\Entitlement;
use Google\Service\CloudCommercePartnerProcurementService\ListEntitlementsResponse;
use Google\Service\CloudCommercePartnerProcurementService\RejectEntitlementPlanChangeRequest;
use Google\Service\CloudCommercePartnerProcurementService\RejectEntitlementRequest;
use Google\Service\CloudCommercePartnerProcurementService\SuspendEntitlementRequest;

/**
 * The "entitlements" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudcommerceprocurementService = new Google\Service\CloudCommercePartnerProcurementService(...);
 *   $entitlements = $cloudcommerceprocurementService->providers_entitlements;
 *  </code>
 */
class ProvidersEntitlements extends \Google\Service\Resource
{
  /**
   * Approves an entitlement that is in the
   * EntitlementState.ENTITLEMENT_ACTIVATION_REQUESTED state. This method is
   * invoked by the provider to approve the creation of the entitlement resource.
   * (entitlements.approve)
   *
   * @param string $name Required. The resource name of the entitlement, with the
   * format `providers/{providerId}/entitlements/{entitlementId}`.
   * @param ApproveEntitlementRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CloudcommerceprocurementEmpty
   * @throws \Google\Service\Exception
   */
  public function approve($name, ApproveEntitlementRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('approve', [$params], CloudcommerceprocurementEmpty::class);
  }
  /**
   * Approves an entitlement plan change that is in the
   * EntitlementState.ENTITLEMENT_PENDING_PLAN_CHANGE_APPROVAL state. This method
   * is invoked by the provider to approve the plan change on the entitlement
   * resource. (entitlements.approvePlanChange)
   *
   * @param string $name Required. The resource name of the entitlement.
   * @param ApproveEntitlementPlanChangeRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CloudcommerceprocurementEmpty
   * @throws \Google\Service\Exception
   */
  public function approvePlanChange($name, ApproveEntitlementPlanChangeRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('approvePlanChange', [$params], CloudcommerceprocurementEmpty::class);
  }
  /**
   * Gets a requested Entitlement resource. (entitlements.get)
   *
   * @param string $name Required. The name of the entitlement to retrieve.
   * @param array $optParams Optional parameters.
   * @return Entitlement
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Entitlement::class);
  }
  /**
   * Lists Entitlements for which the provider has read access.
   * (entitlements.listProvidersEntitlements)
   *
   * @param string $parent Required. The parent resource name.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter The filter that can be used to limit the list
   * request. The filter is a query string that can match a selected set of
   * attributes with string values. For example `account=E-1234-5678-ABCD-EFGH`,
   * `state=pending_cancellation`, and `plan!=foo-plan`. Supported query
   * attributes are * `account` * `customer_billing_account` with value in the
   * format of: `billingAccounts/{id}` * `product_external_name` *
   * `quote_external_name` * `offer` * `new_pending_offer` * `plan` *
   * `newPendingPlan` or `new_pending_plan` * `state` * `services` *
   * `consumers.project` * `change_history.new_offer` Note that the consumers and
   * change_history.new_offer match works on repeated structures, so equality
   * (`consumers.project=projects/123456789`) is not supported. Set membership can
   * be expressed with the `:` operator. For example,
   * `consumers.project:projects/123456789` finds entitlements with at least one
   * consumer with project field equal to `projects/123456789`.
   * `change_history.new_offer` retrieves all entitlements that were once
   * associated or are currently active with the offer. Also note that the state
   * name match is case-insensitive and query can omit the prefix "ENTITLEMENT_".
   * For example, `state=active` is equivalent to `state=ENTITLEMENT_ACTIVE`. If
   * the query contains some special characters other than letters, underscore, or
   * digits, the phrase must be quoted with double quotes. For example,
   * `product="providerId:productId"`, where the product name needs to be quoted
   * because it contains special character colon. Queries can be combined with
   * `AND`, `OR`, and `NOT` to form more complex queries. They can also be grouped
   * to force a desired evaluation order. For example, `state=active AND
   * (account=E-1234 OR account=5678) AND NOT (product=foo-product)`. Connective
   * `AND` can be omitted between two predicates. For example `account=E-1234
   * state=active` is equivalent to `account=E-1234 AND state=active`.
   * @opt_param int pageSize The maximum number of entries that are requested. The
   * default page size is 200.
   * @opt_param string pageToken The token for fetching the next page.
   * @return ListEntitlementsResponse
   * @throws \Google\Service\Exception
   */
  public function listProvidersEntitlements($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListEntitlementsResponse::class);
  }
  /**
   * Updates an existing Entitlement. (entitlements.patch)
   *
   * @param string $name Required. The name of the entitlement to update.
   * @param Entitlement $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask The update mask that applies to the resource.
   * See the [FieldMask definition] (https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#fieldmask) for more details.
   * @return Entitlement
   * @throws \Google\Service\Exception
   */
  public function patch($name, Entitlement $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Entitlement::class);
  }
  /**
   * Rejects an entitlement that is in the
   * EntitlementState.ENTITLEMENT_ACTIVATION_REQUESTED state. This method is
   * invoked by the provider to reject the creation of the entitlement resource.
   * (entitlements.reject)
   *
   * @param string $name Required. The resource name of the entitlement.
   * @param RejectEntitlementRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CloudcommerceprocurementEmpty
   * @throws \Google\Service\Exception
   */
  public function reject($name, RejectEntitlementRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('reject', [$params], CloudcommerceprocurementEmpty::class);
  }
  /**
   * Rejects an entitlement plan change that is in the
   * EntitlementState.ENTITLEMENT_PENDING_PLAN_CHANGE_APPROVAL state. This method
   * is invoked by the provider to reject the plan change on the entitlement
   * resource. (entitlements.rejectPlanChange)
   *
   * @param string $name Required. The resource name of the entitlement.
   * @param RejectEntitlementPlanChangeRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CloudcommerceprocurementEmpty
   * @throws \Google\Service\Exception
   */
  public function rejectPlanChange($name, RejectEntitlementPlanChangeRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('rejectPlanChange', [$params], CloudcommerceprocurementEmpty::class);
  }
  /**
   * Requests suspension of an active Entitlement. This is not yet supported.
   * (entitlements.suspend)
   *
   * @param string $name Required. The name of the entitlement to suspend.
   * @param SuspendEntitlementRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CloudcommerceprocurementEmpty
   * @throws \Google\Service\Exception
   */
  public function suspend($name, SuspendEntitlementRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('suspend', [$params], CloudcommerceprocurementEmpty::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProvidersEntitlements::class, 'Google_Service_CloudCommercePartnerProcurementService_Resource_ProvidersEntitlements');
