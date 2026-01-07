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

namespace Google\Service\PaymentsResellerSubscription\Resource;

use Google\Service\PaymentsResellerSubscription\CancelSubscriptionRequest;
use Google\Service\PaymentsResellerSubscription\CancelSubscriptionResponse;
use Google\Service\PaymentsResellerSubscription\EntitleSubscriptionRequest;
use Google\Service\PaymentsResellerSubscription\EntitleSubscriptionResponse;
use Google\Service\PaymentsResellerSubscription\ExtendSubscriptionRequest;
use Google\Service\PaymentsResellerSubscription\ExtendSubscriptionResponse;
use Google\Service\PaymentsResellerSubscription\ResumeSubscriptionRequest;
use Google\Service\PaymentsResellerSubscription\ResumeSubscriptionResponse;
use Google\Service\PaymentsResellerSubscription\Subscription;
use Google\Service\PaymentsResellerSubscription\SuspendSubscriptionRequest;
use Google\Service\PaymentsResellerSubscription\SuspendSubscriptionResponse;
use Google\Service\PaymentsResellerSubscription\UndoCancelSubscriptionRequest;
use Google\Service\PaymentsResellerSubscription\UndoCancelSubscriptionResponse;

/**
 * The "subscriptions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $paymentsresellersubscriptionService = new Google\Service\PaymentsResellerSubscription(...);
 *   $subscriptions = $paymentsresellersubscriptionService->partners_subscriptions;
 *  </code>
 */
class PartnersSubscriptions extends \Google\Service\Resource
{
  /**
   * Cancels a subscription service either immediately or by the end of the
   * current billing cycle for their customers. It should be called directly by
   * the partner using service accounts. (subscriptions.cancel)
   *
   * @param string $name Required. The name of the subscription resource to be
   * cancelled. It will have the format of
   * "partners/{partner_id}/subscriptions/{subscription_id}"
   * @param CancelSubscriptionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CancelSubscriptionResponse
   * @throws \Google\Service\Exception
   */
  public function cancel($name, CancelSubscriptionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params], CancelSubscriptionResponse::class);
  }
  /**
   * Used by partners to create a subscription for their customers. The created
   * subscription is associated with the end user inferred from the end user
   * credentials. This API must be authorized by the end user using OAuth.
   * (subscriptions.create)
   *
   * @param string $parent Required. The parent resource name, which is the
   * identifier of the partner. It will have the format of
   * "partners/{partner_id}".
   * @param Subscription $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string subscriptionId Required. Identifies the subscription
   * resource on the Partner side. The value is restricted to 63 ASCII characters
   * at the maximum. If a subscription was previously created with the same
   * subscription_id, we will directly return that one.
   * @return Subscription
   * @throws \Google\Service\Exception
   */
  public function create($parent, Subscription $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Subscription::class);
  }
  /**
   * Entitles a previously provisioned subscription to the current end user. The
   * end user identity is inferred from the authorized credential of the request.
   * This API must be authorized by the end user using OAuth.
   * (subscriptions.entitle)
   *
   * @param string $name Required. The name of the subscription resource that is
   * entitled to the current end user. It will have the format of
   * "partners/{partner_id}/subscriptions/{subscription_id}"
   * @param EntitleSubscriptionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return EntitleSubscriptionResponse
   * @throws \Google\Service\Exception
   */
  public function entitle($name, EntitleSubscriptionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('entitle', [$params], EntitleSubscriptionResponse::class);
  }
  /**
   * [Opt-in only] Most partners should be on auto-extend by default. Extends a
   * subscription service for their customers on an ongoing basis for the
   * subscription to remain active and renewable. It should be called directly by
   * the partner using service accounts. (subscriptions.extend)
   *
   * @param string $name Required. The name of the subscription resource to be
   * extended. It will have the format of
   * "partners/{partner_id}/subscriptions/{subscription_id}".
   * @param ExtendSubscriptionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return ExtendSubscriptionResponse
   * @throws \Google\Service\Exception
   */
  public function extend($name, ExtendSubscriptionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('extend', [$params], ExtendSubscriptionResponse::class);
  }
  /**
   * Gets a subscription by id. It should be called directly by the partner using
   * service accounts. (subscriptions.get)
   *
   * @param string $name Required. The name of the subscription resource to
   * retrieve. It will have the format of
   * "partners/{partner_id}/subscriptions/{subscription_id}"
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
   * Used by partners to provision a subscription for their customers. This
   * creates a subscription without associating it with the end user account.
   * EntitleSubscription must be called separately using OAuth in order for the
   * end user account to be associated with the subscription. It should be called
   * directly by the partner using service accounts. (subscriptions.provision)
   *
   * @param string $parent Required. The parent resource name, which is the
   * identifier of the partner. It will have the format of
   * "partners/{partner_id}".
   * @param Subscription $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param int cycleOptions.initialCycleDuration.count number of duration
   * units to be included.
   * @opt_param string cycleOptions.initialCycleDuration.unit The unit used for
   * the duration
   * @opt_param string subscriptionId Required. Identifies the subscription
   * resource on the Partner side. The value is restricted to 63 ASCII characters
   * at the maximum. If a subscription was previously created with the same
   * subscription_id, we will directly return that one.
   * @return Subscription
   * @throws \Google\Service\Exception
   */
  public function provision($parent, Subscription $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('provision', [$params], Subscription::class);
  }
  /**
   * Resumes a suspended subscription. The new billing cycle will start at the
   * time of the request. It should be called directly by the partner using
   * service accounts. (subscriptions.resume)
   *
   * @param string $name Required. The name of the subscription resource to be
   * resumed. It will have the format of
   * "partners/{partner_id}/subscriptions/{subscription_id}"
   * @param ResumeSubscriptionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return ResumeSubscriptionResponse
   * @throws \Google\Service\Exception
   */
  public function resume($name, ResumeSubscriptionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('resume', [$params], ResumeSubscriptionResponse::class);
  }
  /**
   * Suspends a subscription. Contract terms may dictate if a prorated refund will
   * be issued upon suspension. It should be called directly by the partner using
   * service accounts. (subscriptions.suspend)
   *
   * @param string $name Required. The name of the subscription resource to be
   * suspended. It will have the format of
   * "partners/{partner_id}/subscriptions/{subscription_id}"
   * @param SuspendSubscriptionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return SuspendSubscriptionResponse
   * @throws \Google\Service\Exception
   */
  public function suspend($name, SuspendSubscriptionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('suspend', [$params], SuspendSubscriptionResponse::class);
  }
  /**
   * Currently, it is used by **Google One, Play Pass** partners. Revokes the
   * pending cancellation of a subscription, which is currently in
   * `STATE_CANCEL_AT_END_OF_CYCLE` state. If the subscription is already
   * cancelled, the request will fail. It should be called directly by the partner
   * using service accounts. (subscriptions.undoCancel)
   *
   * @param string $name Required. The name of the subscription resource whose
   * pending cancellation needs to be undone. It will have the format of
   * "partners/{partner_id}/subscriptions/{subscription_id}"
   * @param UndoCancelSubscriptionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return UndoCancelSubscriptionResponse
   * @throws \Google\Service\Exception
   */
  public function undoCancel($name, UndoCancelSubscriptionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('undoCancel', [$params], UndoCancelSubscriptionResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PartnersSubscriptions::class, 'Google_Service_PaymentsResellerSubscription_Resource_PartnersSubscriptions');
