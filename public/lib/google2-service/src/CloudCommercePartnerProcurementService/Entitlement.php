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

namespace Google\Service\CloudCommercePartnerProcurementService;

class Entitlement extends \Google\Collection
{
  /**
   * Default state of the entitlement. It's only set to this value when the
   * entitlement is first created and has not been initialized.
   */
  public const STATE_ENTITLEMENT_STATE_UNSPECIFIED = 'ENTITLEMENT_STATE_UNSPECIFIED';
  /**
   * Indicates that the entitlement has been created, but it hasn't yet become
   * active. The entitlement remains in this state until it becomes active. If
   * the entitlement requires provider approval, a notification is sent to the
   * provider for the activation approval. If the provider doesn't approve, the
   * entitlement is removed. If approved, the entitlement transitions to the
   * EntitlementState.ENTITLEMENT_ACTIVE state after either a short processing
   * delay or, if applicable, at the scheduled start time of the purchased
   * offer. Plan changes aren't allowed in this state. Instead, customers are
   * expected to cancel the corresponding order and place a new order.
   */
  public const STATE_ENTITLEMENT_ACTIVATION_REQUESTED = 'ENTITLEMENT_ACTIVATION_REQUESTED';
  /**
   * Indicates that the entitlement is active. The procured item is now usable
   * and any associated billing events will start occurring. Entitlements in
   * this state WILL renew. The analogous state for an unexpired but non-
   * renewing entitlement is ENTITLEMENT_PENDING_CANCELLATION. In this state,
   * the customer can decide to cancel the entitlement, which would change the
   * state to EntitlementState.ENTITLEMENT_PENDING_CANCELLATION, and then
   * EntitlementState.ENTITLEMENT_CANCELLED. The user can also request a change
   * of plan, which will transition the state to
   * EntitlementState.ENTITLEMENT_PENDING_PLAN_CHANGE, and then back to
   * EntitlementState.ENTITLEMENT_ACTIVE.
   */
  public const STATE_ENTITLEMENT_ACTIVE = 'ENTITLEMENT_ACTIVE';
  /**
   * Indicates that the entitlement will expire at the end of its term. This
   * could mean the customer has elected not to renew this entitlement or the
   * customer elected to cancel an entitlement that only expires at term end.
   * The entitlement typically stays in this state if the entitlement/plan
   * allows use of the underlying resource until the end of the current billing
   * cycle. Once the billing cycle completes, the resource will transition to
   * EntitlementState.ENTITLEMENT_CANCELLED state. The resource cannot be
   * modified during this state.
   */
  public const STATE_ENTITLEMENT_PENDING_CANCELLATION = 'ENTITLEMENT_PENDING_CANCELLATION';
  /**
   * Indicates that the entitlement was cancelled. The entitlement can now be
   * deleted.
   */
  public const STATE_ENTITLEMENT_CANCELLED = 'ENTITLEMENT_CANCELLED';
  /**
   * Indicates that the entitlement is currently active, but there is a pending
   * plan change that is requested by the customer. The entitlement typically
   * stays in this state, if the entitlement/plan requires the completion of the
   * current billing cycle before the plan can be changed. Once the billing
   * cycle completes, the resource will transition to
   * EntitlementState.ENTITLEMENT_ACTIVE, with its plan changed.
   */
  public const STATE_ENTITLEMENT_PENDING_PLAN_CHANGE = 'ENTITLEMENT_PENDING_PLAN_CHANGE';
  /**
   * Indicates that the entitlement is currently active, but there is a plan
   * change request pending provider approval. If the provider approves the plan
   * change, then the entitlement will transition either to
   * EntitlementState.ENTITLEMENT_ACTIVE or
   * EntitlementState.ENTITLEMENT_PENDING_PLAN_CHANGE depending on whether
   * current plan requires that the billing cycle completes. If the provider
   * rejects the plan change, then the pending plan change request is removed
   * and the entitlement stays in EntitlementState.ENTITLEMENT_ACTIVE state with
   * the old plan.
   */
  public const STATE_ENTITLEMENT_PENDING_PLAN_CHANGE_APPROVAL = 'ENTITLEMENT_PENDING_PLAN_CHANGE_APPROVAL';
  /**
   * Indicates that the entitlement is suspended either by Google or provider
   * request. This can be triggered for various external reasons (e.g.
   * expiration of credit card on the billing account, violation of terms-of-
   * service of the provider etc.). As such, any remediating action needs to be
   * taken externally, before the entitlement can be activated. This is not yet
   * supported.
   */
  public const STATE_ENTITLEMENT_SUSPENDED = 'ENTITLEMENT_SUSPENDED';
  protected $collection_key = 'entitlementBenefitIds';
  /**
   * Output only. The resource name of the account that this entitlement is
   * based on, if any.
   *
   * @var string
   */
  public $account;
  /**
   * Output only. The reason the entitlement was cancelled. If this entitlement
   * wasn't cancelled, this field is empty. Possible values include "unknown",
   * "expired", "user-cancelled", "account-closed", "billing-disabled" (if the
   * customer has manually disabled billing to their resources), "user-aborted",
   * and "migrated" (if the entitlement has migrated across products). Values of
   * this field are subject to change, and we recommend that you don't build
   * your technical integration to rely on these fields.
   *
   * @var string
   */
  public $cancellationReason;
  protected $consumersType = Consumer::class;
  protected $consumersDataType = 'array';
  /**
   * Output only. The creation timestamp.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The entitlement benefit IDs associated with the purchase.
   *
   * @var string[]
   */
  public $entitlementBenefitIds;
  /**
   * Output only. The custom properties that were collected from the user to
   * create this entitlement.
   *
   * @deprecated
   * @var array[]
   */
  public $inputProperties;
  /**
   * Provider-supplied message that is displayed to the end user. Currently this
   * is used to communicate progress and ETA for provisioning. This field can be
   * updated only when a user is waiting for an action from the provider, i.e.
   * entitlement state is EntitlementState.ENTITLEMENT_ACTIVATION_REQUESTED or
   * EntitlementState.ENTITLEMENT_PENDING_PLAN_CHANGE_APPROVAL. This field is
   * cleared automatically when the entitlement state changes.
   *
   * @var string
   */
  public $messageToUser;
  /**
   * Output only. The resource name of the entitlement. Entitlement names have
   * the form `providers/{provider_id}/entitlements/{entitlement_id}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The end time of the new offer, determined from the offer's
   * specified end date. If the offer des not have a specified end date then
   * this field is not set. This field is populated even if the entitlement
   * isn't active yet. If there's no upcoming offer, the field is empty. * If
   * the entitlement is in the state ENTITLEMENT_ACTIVATION_REQUESTED,
   * ENTITLEMENT_ACTIVE, or ENTITLEMENT_PENDING_CANCELLATION, then this field is
   * empty. * If the entitlement is in the state
   * ENTITLEMENT_PENDING_PLAN_CHANGE_APPROVAL or
   * ENTITLEMENT_PENDING_PLAN_CHANGE, and the upcoming offer has a specified end
   * date, then this field is populated with the expected end time of the
   * upcoming offer, in the future. Otherwise, this field is empty. * If the
   * entitlement is in the state ENTITLEMENT_CANCELLED, then this field is
   * empty.
   *
   * @var string
   */
  public $newOfferEndTime;
  /**
   * Output only. The timestamp when the new offer becomes effective. This field
   * is populated even if the entitlement isn't active yet. If there's no
   * upcoming offer, the field is empty. * If the entitlement is in the state
   * ENTITLEMENT_ACTIVATION_REQUESTED, this field isn't populated when the
   * entitlement isn't yet approved. After the entitlement is approved, this
   * field is populated with the effective time of the upcoming offer. * If the
   * entitlement is in the state ENTITLEMENT_ACTIVE or
   * ENTITLEMENT_PENDING_CANCELLATION, this field isn't populated. * If the
   * entitlement is in the state ENTITLEMENT_PENDING_PLAN_CHANGE_APPROVAL, this
   * field isn't populated, because the entitlement change is waiting on
   * approval. * If the entitlement is in the state
   * ENTITLEMENT_PENDING_PLAN_CHANGE, this field is populated with the expected
   * effective time of the upcoming offer, which is in the future. * If the
   * entitlement is in the state ENTITLEMENT_CANCELLED, then this field is
   * empty.
   *
   * @var string
   */
  public $newOfferStartTime;
  /**
   * Output only. Upon a pending plan change, the name of the offer that the
   * entitlement is switching to. Only exists if the pending plan change is
   * moving to an offer. This field isn't populated for entitlements which
   * aren't active yet. Format:
   * 'projects/{project}/services/{service}/privateOffers/{offer}' OR
   * 'projects/{project}/services/{service}/standardOffers/{offer}', depending
   * on whether the offer is private or public. The {service} in the name is the
   * listing service of the offer. It could be either the product service that
   * the offer is referencing, or a generic private offer parent service. We
   * recommend that you don't build your integration to rely on the meaning of
   * this {service} part. * If the entitlement is in the state
   * ENTITLEMENT_ACTIVATION_REQUESTED, ENTITLEMENT_ACTIVE or
   * ENTITLEMENT_PENDING_CANCELLATION, then this field is empty. * If the
   * entitlement is in the state ENTITLEMENT_PENDING_PLAN_CHANGE_APPROVAL or
   * ENTITLEMENT_PENDING_PLAN_CHANGE, then this field is populated with the
   * upcoming offer. * If the entitlement is in the state ENTITLEMENT_CANCELLED,
   * then this is empty.
   *
   * @var string
   */
  public $newPendingOffer;
  /**
   * Output only. The duration of the new offer, in ISO 8601 duration format.
   * This field is populated for pending offer changes. It isn't populated for
   * entitlements which aren't active yet. If the offer has a specified end date
   * instead of a duration, this field is empty. * If the entitlement is in the
   * state ENTITLEMENT_ACTIVATION_REQUESTED, ENTITLEMENT_ACTIVE, or
   * ENTITLEMENT_PENDING_CANCELLATION, this field is empty. * If the entitlement
   * is in the state ENTITLEMENT_PENDING_PLAN_CHANGE_APPROVAL or
   * ENTITLEMENT_PENDING_PLAN_CHANGE, and the upcoming offer doesn't have a
   * specified end date, then this field is populated with the duration of the
   * upcoming offer. Otherwise, this field is empty. * If the entitlement is in
   * the state ENTITLEMENT_CANCELLED, then this field is empty.
   *
   * @var string
   */
  public $newPendingOfferDuration;
  /**
   * Output only. The identifier of the pending new plan. Required if the
   * product has plans and the entitlement has a pending plan change.
   *
   * @var string
   */
  public $newPendingPlan;
  /**
   * Output only. The name of the offer that was procured. Field is empty if
   * order wasn't made using an offer. Format:
   * 'projects/{project}/services/{service}/privateOffers/{offer}' OR
   * 'projects/{project}/services/{service}/standardOffers/{offer}', depending
   * on whether the offer is private or public. The {service} in the name is the
   * listing service of the offer. It could be either the product service that
   * the offer is referencing, or a generic private offer parent service. We
   * recommend that you don't build your integration to rely on the meaning of
   * this {service} part. * If the entitlement is in the state
   * ENTITLEMENT_ACTIVATION_REQUESTED, this field is populated with the upcoming
   * offer. * If the entitlement is in the state ENTITLEMENT_ACTIVE,
   * ENTITLEMENT_PENDING_CANCELLATION, ENTITLEMENT_PENDING_PLAN_CHANGE, or
   * ENTITLEMENT_PENDING_PLAN_CHANGE_APPROVAL, this field is populated with the
   * current offer. * If the entitlement is in the state ENTITLEMENT_CANCELLED,
   * then this field is populated with the latest offer that the order was
   * associated with.
   *
   * @var string
   */
  public $offer;
  /**
   * Output only. The offer duration of the current offer, in ISO 8601 duration
   * format. This is empty if the entitlement wasn't made using an offer, or if
   * the offer has a specified end date instead of a duration. * If the
   * entitlement is in the state ENTITLEMENT_ACTIVATION_REQUESTED, and the
   * upcoming offer doesn't have a specified end date, then this field is
   * populated with the duration of the upcoming offer. Otherwise, this field is
   * empty. * If the entitlement is in the state ENTITLEMENT_ACTIVE,
   * ENTITLEMENT_PENDING_CANCELLATION, ENTITLEMENT_PENDING_PLAN_CHANGE, or
   * ENTITLEMENT_PENDING_PLAN_CHANGE_APPROVAL, and the current offer doesn't
   * have a specified end date, then this field contains the duration of the
   * current offer. Otherwise, this field is empty. * If the entitlement is in
   * the state ENTITLEMENT_CANCELLED, and the offer doesn't have a specified end
   * date, then this field is populated with the duration of the latest offer
   * that the order was associated with. Otherwise, this field is empty.
   *
   * @var string
   */
  public $offerDuration;
  /**
   * Output only. End time for the current term of the Offer associated with
   * this entitlement. The value of this field can change naturally over time
   * due to auto-renewal, even if the offer isn't changed. * If the entitlement
   * is in the state ENTITLEMENT_ACTIVATION_REQUESTED, then: * If the
   * entitlement isn't approved yet approved, and the offer has a specified end
   * date, then this field is populated with the expected end time of the
   * upcoming offer, in the future. Otherwise, this field is empty. * If the
   * entitlement is approved, then this field is populated with the expected end
   * time of the upcoming offer, in the future. This means that this field and
   * the field offer_duration can both exist. * If the entitlement is in the
   * state ENTITLEMENT_ACTIVE or ENTITLEMENT_PENDING_CANCELLATION, then this
   * field is populated with the expected end time of the current offer, in the
   * future. This field's value is set regardless of whether the offer has a
   * specific end date or a duration. This means that this field and the field
   * offer_duration can both exist. * If the entitlement is in the state
   * ENTITLEMENT_PENDING_PLAN_CHANGE_APPROVAL or
   * ENTITLEMENT_PENDING_PLAN_CHANGE: * If the entitlement's pricing model is
   * usage based and the associated offer is a private offer whose term has
   * ended, then this field reflects the ACTUAL end time of the entitlement's
   * associated offer (in the past), even though the entitlement associated with
   * this private offer does not terminate at the end of that private offer's
   * term. * Otherwise, this is the expected end date of the current offer, in
   * the future. * If the entitlement is in the state ENTITLEMENT_CANCELLED,
   * then this field is populated with the end time, in the past, of the latest
   * offer that the order was associated with. If the entitlement was cancelled
   * before any offer started, then this field is empty.
   *
   * @var string
   */
  public $offerEndTime;
  /**
   * Output only. The order ID of this entitlement, without any `orders/`
   * resource name prefix.
   *
   * @var string
   */
  public $orderId;
  /**
   * Output only. The identifier of the plan that was procured. Required if the
   * product has plans.
   *
   * @var string
   */
  public $plan;
  /**
   * Output only. The identifier of the entity that was purchased. This may
   * actually represent a product, quote, or offer. We strongly recommend that
   * you use the following more explicit fields: productExternalName,
   * quoteExternalName, or offer.
   *
   * @deprecated
   * @var string
   */
  public $product;
  /**
   * Output only. The identifier of the product that was procured.
   *
   * @var string
   */
  public $productExternalName;
  /**
   * Output only. The identifier of the service provider that this entitlement
   * was created against. Each service provider is assigned a unique provider
   * value when they onboard with Cloud Commerce platform.
   *
   * @var string
   */
  public $provider;
  /**
   * Output only. The identifier of the quote that was used to procure. Empty if
   * the order is not purchased using a quote.
   *
   * @var string
   */
  public $quoteExternalName;
  /**
   * Output only. The state of the entitlement.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. End time for the subscription corresponding to this
   * entitlement.
   *
   * @var string
   */
  public $subscriptionEndTime;
  /**
   * Output only. The last update timestamp.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Output only. The consumerId to use when reporting usage through the Service
   * Control API. See the consumerId field at [Reporting
   * Metrics](https://cloud.google.com/service-control/reporting-metrics) for
   * more details. This field is present only if the product has usage-based
   * billing configured.
   *
   * @var string
   */
  public $usageReportingId;

  /**
   * Output only. The resource name of the account that this entitlement is
   * based on, if any.
   *
   * @param string $account
   */
  public function setAccount($account)
  {
    $this->account = $account;
  }
  /**
   * @return string
   */
  public function getAccount()
  {
    return $this->account;
  }
  /**
   * Output only. The reason the entitlement was cancelled. If this entitlement
   * wasn't cancelled, this field is empty. Possible values include "unknown",
   * "expired", "user-cancelled", "account-closed", "billing-disabled" (if the
   * customer has manually disabled billing to their resources), "user-aborted",
   * and "migrated" (if the entitlement has migrated across products). Values of
   * this field are subject to change, and we recommend that you don't build
   * your technical integration to rely on these fields.
   *
   * @param string $cancellationReason
   */
  public function setCancellationReason($cancellationReason)
  {
    $this->cancellationReason = $cancellationReason;
  }
  /**
   * @return string
   */
  public function getCancellationReason()
  {
    return $this->cancellationReason;
  }
  /**
   * Output only. The resources using this entitlement, if applicable.
   *
   * @param Consumer[] $consumers
   */
  public function setConsumers($consumers)
  {
    $this->consumers = $consumers;
  }
  /**
   * @return Consumer[]
   */
  public function getConsumers()
  {
    return $this->consumers;
  }
  /**
   * Output only. The creation timestamp.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. The entitlement benefit IDs associated with the purchase.
   *
   * @param string[] $entitlementBenefitIds
   */
  public function setEntitlementBenefitIds($entitlementBenefitIds)
  {
    $this->entitlementBenefitIds = $entitlementBenefitIds;
  }
  /**
   * @return string[]
   */
  public function getEntitlementBenefitIds()
  {
    return $this->entitlementBenefitIds;
  }
  /**
   * Output only. The custom properties that were collected from the user to
   * create this entitlement.
   *
   * @deprecated
   * @param array[] $inputProperties
   */
  public function setInputProperties($inputProperties)
  {
    $this->inputProperties = $inputProperties;
  }
  /**
   * @deprecated
   * @return array[]
   */
  public function getInputProperties()
  {
    return $this->inputProperties;
  }
  /**
   * Provider-supplied message that is displayed to the end user. Currently this
   * is used to communicate progress and ETA for provisioning. This field can be
   * updated only when a user is waiting for an action from the provider, i.e.
   * entitlement state is EntitlementState.ENTITLEMENT_ACTIVATION_REQUESTED or
   * EntitlementState.ENTITLEMENT_PENDING_PLAN_CHANGE_APPROVAL. This field is
   * cleared automatically when the entitlement state changes.
   *
   * @param string $messageToUser
   */
  public function setMessageToUser($messageToUser)
  {
    $this->messageToUser = $messageToUser;
  }
  /**
   * @return string
   */
  public function getMessageToUser()
  {
    return $this->messageToUser;
  }
  /**
   * Output only. The resource name of the entitlement. Entitlement names have
   * the form `providers/{provider_id}/entitlements/{entitlement_id}`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The end time of the new offer, determined from the offer's
   * specified end date. If the offer des not have a specified end date then
   * this field is not set. This field is populated even if the entitlement
   * isn't active yet. If there's no upcoming offer, the field is empty. * If
   * the entitlement is in the state ENTITLEMENT_ACTIVATION_REQUESTED,
   * ENTITLEMENT_ACTIVE, or ENTITLEMENT_PENDING_CANCELLATION, then this field is
   * empty. * If the entitlement is in the state
   * ENTITLEMENT_PENDING_PLAN_CHANGE_APPROVAL or
   * ENTITLEMENT_PENDING_PLAN_CHANGE, and the upcoming offer has a specified end
   * date, then this field is populated with the expected end time of the
   * upcoming offer, in the future. Otherwise, this field is empty. * If the
   * entitlement is in the state ENTITLEMENT_CANCELLED, then this field is
   * empty.
   *
   * @param string $newOfferEndTime
   */
  public function setNewOfferEndTime($newOfferEndTime)
  {
    $this->newOfferEndTime = $newOfferEndTime;
  }
  /**
   * @return string
   */
  public function getNewOfferEndTime()
  {
    return $this->newOfferEndTime;
  }
  /**
   * Output only. The timestamp when the new offer becomes effective. This field
   * is populated even if the entitlement isn't active yet. If there's no
   * upcoming offer, the field is empty. * If the entitlement is in the state
   * ENTITLEMENT_ACTIVATION_REQUESTED, this field isn't populated when the
   * entitlement isn't yet approved. After the entitlement is approved, this
   * field is populated with the effective time of the upcoming offer. * If the
   * entitlement is in the state ENTITLEMENT_ACTIVE or
   * ENTITLEMENT_PENDING_CANCELLATION, this field isn't populated. * If the
   * entitlement is in the state ENTITLEMENT_PENDING_PLAN_CHANGE_APPROVAL, this
   * field isn't populated, because the entitlement change is waiting on
   * approval. * If the entitlement is in the state
   * ENTITLEMENT_PENDING_PLAN_CHANGE, this field is populated with the expected
   * effective time of the upcoming offer, which is in the future. * If the
   * entitlement is in the state ENTITLEMENT_CANCELLED, then this field is
   * empty.
   *
   * @param string $newOfferStartTime
   */
  public function setNewOfferStartTime($newOfferStartTime)
  {
    $this->newOfferStartTime = $newOfferStartTime;
  }
  /**
   * @return string
   */
  public function getNewOfferStartTime()
  {
    return $this->newOfferStartTime;
  }
  /**
   * Output only. Upon a pending plan change, the name of the offer that the
   * entitlement is switching to. Only exists if the pending plan change is
   * moving to an offer. This field isn't populated for entitlements which
   * aren't active yet. Format:
   * 'projects/{project}/services/{service}/privateOffers/{offer}' OR
   * 'projects/{project}/services/{service}/standardOffers/{offer}', depending
   * on whether the offer is private or public. The {service} in the name is the
   * listing service of the offer. It could be either the product service that
   * the offer is referencing, or a generic private offer parent service. We
   * recommend that you don't build your integration to rely on the meaning of
   * this {service} part. * If the entitlement is in the state
   * ENTITLEMENT_ACTIVATION_REQUESTED, ENTITLEMENT_ACTIVE or
   * ENTITLEMENT_PENDING_CANCELLATION, then this field is empty. * If the
   * entitlement is in the state ENTITLEMENT_PENDING_PLAN_CHANGE_APPROVAL or
   * ENTITLEMENT_PENDING_PLAN_CHANGE, then this field is populated with the
   * upcoming offer. * If the entitlement is in the state ENTITLEMENT_CANCELLED,
   * then this is empty.
   *
   * @param string $newPendingOffer
   */
  public function setNewPendingOffer($newPendingOffer)
  {
    $this->newPendingOffer = $newPendingOffer;
  }
  /**
   * @return string
   */
  public function getNewPendingOffer()
  {
    return $this->newPendingOffer;
  }
  /**
   * Output only. The duration of the new offer, in ISO 8601 duration format.
   * This field is populated for pending offer changes. It isn't populated for
   * entitlements which aren't active yet. If the offer has a specified end date
   * instead of a duration, this field is empty. * If the entitlement is in the
   * state ENTITLEMENT_ACTIVATION_REQUESTED, ENTITLEMENT_ACTIVE, or
   * ENTITLEMENT_PENDING_CANCELLATION, this field is empty. * If the entitlement
   * is in the state ENTITLEMENT_PENDING_PLAN_CHANGE_APPROVAL or
   * ENTITLEMENT_PENDING_PLAN_CHANGE, and the upcoming offer doesn't have a
   * specified end date, then this field is populated with the duration of the
   * upcoming offer. Otherwise, this field is empty. * If the entitlement is in
   * the state ENTITLEMENT_CANCELLED, then this field is empty.
   *
   * @param string $newPendingOfferDuration
   */
  public function setNewPendingOfferDuration($newPendingOfferDuration)
  {
    $this->newPendingOfferDuration = $newPendingOfferDuration;
  }
  /**
   * @return string
   */
  public function getNewPendingOfferDuration()
  {
    return $this->newPendingOfferDuration;
  }
  /**
   * Output only. The identifier of the pending new plan. Required if the
   * product has plans and the entitlement has a pending plan change.
   *
   * @param string $newPendingPlan
   */
  public function setNewPendingPlan($newPendingPlan)
  {
    $this->newPendingPlan = $newPendingPlan;
  }
  /**
   * @return string
   */
  public function getNewPendingPlan()
  {
    return $this->newPendingPlan;
  }
  /**
   * Output only. The name of the offer that was procured. Field is empty if
   * order wasn't made using an offer. Format:
   * 'projects/{project}/services/{service}/privateOffers/{offer}' OR
   * 'projects/{project}/services/{service}/standardOffers/{offer}', depending
   * on whether the offer is private or public. The {service} in the name is the
   * listing service of the offer. It could be either the product service that
   * the offer is referencing, or a generic private offer parent service. We
   * recommend that you don't build your integration to rely on the meaning of
   * this {service} part. * If the entitlement is in the state
   * ENTITLEMENT_ACTIVATION_REQUESTED, this field is populated with the upcoming
   * offer. * If the entitlement is in the state ENTITLEMENT_ACTIVE,
   * ENTITLEMENT_PENDING_CANCELLATION, ENTITLEMENT_PENDING_PLAN_CHANGE, or
   * ENTITLEMENT_PENDING_PLAN_CHANGE_APPROVAL, this field is populated with the
   * current offer. * If the entitlement is in the state ENTITLEMENT_CANCELLED,
   * then this field is populated with the latest offer that the order was
   * associated with.
   *
   * @param string $offer
   */
  public function setOffer($offer)
  {
    $this->offer = $offer;
  }
  /**
   * @return string
   */
  public function getOffer()
  {
    return $this->offer;
  }
  /**
   * Output only. The offer duration of the current offer, in ISO 8601 duration
   * format. This is empty if the entitlement wasn't made using an offer, or if
   * the offer has a specified end date instead of a duration. * If the
   * entitlement is in the state ENTITLEMENT_ACTIVATION_REQUESTED, and the
   * upcoming offer doesn't have a specified end date, then this field is
   * populated with the duration of the upcoming offer. Otherwise, this field is
   * empty. * If the entitlement is in the state ENTITLEMENT_ACTIVE,
   * ENTITLEMENT_PENDING_CANCELLATION, ENTITLEMENT_PENDING_PLAN_CHANGE, or
   * ENTITLEMENT_PENDING_PLAN_CHANGE_APPROVAL, and the current offer doesn't
   * have a specified end date, then this field contains the duration of the
   * current offer. Otherwise, this field is empty. * If the entitlement is in
   * the state ENTITLEMENT_CANCELLED, and the offer doesn't have a specified end
   * date, then this field is populated with the duration of the latest offer
   * that the order was associated with. Otherwise, this field is empty.
   *
   * @param string $offerDuration
   */
  public function setOfferDuration($offerDuration)
  {
    $this->offerDuration = $offerDuration;
  }
  /**
   * @return string
   */
  public function getOfferDuration()
  {
    return $this->offerDuration;
  }
  /**
   * Output only. End time for the current term of the Offer associated with
   * this entitlement. The value of this field can change naturally over time
   * due to auto-renewal, even if the offer isn't changed. * If the entitlement
   * is in the state ENTITLEMENT_ACTIVATION_REQUESTED, then: * If the
   * entitlement isn't approved yet approved, and the offer has a specified end
   * date, then this field is populated with the expected end time of the
   * upcoming offer, in the future. Otherwise, this field is empty. * If the
   * entitlement is approved, then this field is populated with the expected end
   * time of the upcoming offer, in the future. This means that this field and
   * the field offer_duration can both exist. * If the entitlement is in the
   * state ENTITLEMENT_ACTIVE or ENTITLEMENT_PENDING_CANCELLATION, then this
   * field is populated with the expected end time of the current offer, in the
   * future. This field's value is set regardless of whether the offer has a
   * specific end date or a duration. This means that this field and the field
   * offer_duration can both exist. * If the entitlement is in the state
   * ENTITLEMENT_PENDING_PLAN_CHANGE_APPROVAL or
   * ENTITLEMENT_PENDING_PLAN_CHANGE: * If the entitlement's pricing model is
   * usage based and the associated offer is a private offer whose term has
   * ended, then this field reflects the ACTUAL end time of the entitlement's
   * associated offer (in the past), even though the entitlement associated with
   * this private offer does not terminate at the end of that private offer's
   * term. * Otherwise, this is the expected end date of the current offer, in
   * the future. * If the entitlement is in the state ENTITLEMENT_CANCELLED,
   * then this field is populated with the end time, in the past, of the latest
   * offer that the order was associated with. If the entitlement was cancelled
   * before any offer started, then this field is empty.
   *
   * @param string $offerEndTime
   */
  public function setOfferEndTime($offerEndTime)
  {
    $this->offerEndTime = $offerEndTime;
  }
  /**
   * @return string
   */
  public function getOfferEndTime()
  {
    return $this->offerEndTime;
  }
  /**
   * Output only. The order ID of this entitlement, without any `orders/`
   * resource name prefix.
   *
   * @param string $orderId
   */
  public function setOrderId($orderId)
  {
    $this->orderId = $orderId;
  }
  /**
   * @return string
   */
  public function getOrderId()
  {
    return $this->orderId;
  }
  /**
   * Output only. The identifier of the plan that was procured. Required if the
   * product has plans.
   *
   * @param string $plan
   */
  public function setPlan($plan)
  {
    $this->plan = $plan;
  }
  /**
   * @return string
   */
  public function getPlan()
  {
    return $this->plan;
  }
  /**
   * Output only. The identifier of the entity that was purchased. This may
   * actually represent a product, quote, or offer. We strongly recommend that
   * you use the following more explicit fields: productExternalName,
   * quoteExternalName, or offer.
   *
   * @deprecated
   * @param string $product
   */
  public function setProduct($product)
  {
    $this->product = $product;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getProduct()
  {
    return $this->product;
  }
  /**
   * Output only. The identifier of the product that was procured.
   *
   * @param string $productExternalName
   */
  public function setProductExternalName($productExternalName)
  {
    $this->productExternalName = $productExternalName;
  }
  /**
   * @return string
   */
  public function getProductExternalName()
  {
    return $this->productExternalName;
  }
  /**
   * Output only. The identifier of the service provider that this entitlement
   * was created against. Each service provider is assigned a unique provider
   * value when they onboard with Cloud Commerce platform.
   *
   * @param string $provider
   */
  public function setProvider($provider)
  {
    $this->provider = $provider;
  }
  /**
   * @return string
   */
  public function getProvider()
  {
    return $this->provider;
  }
  /**
   * Output only. The identifier of the quote that was used to procure. Empty if
   * the order is not purchased using a quote.
   *
   * @param string $quoteExternalName
   */
  public function setQuoteExternalName($quoteExternalName)
  {
    $this->quoteExternalName = $quoteExternalName;
  }
  /**
   * @return string
   */
  public function getQuoteExternalName()
  {
    return $this->quoteExternalName;
  }
  /**
   * Output only. The state of the entitlement.
   *
   * Accepted values: ENTITLEMENT_STATE_UNSPECIFIED,
   * ENTITLEMENT_ACTIVATION_REQUESTED, ENTITLEMENT_ACTIVE,
   * ENTITLEMENT_PENDING_CANCELLATION, ENTITLEMENT_CANCELLED,
   * ENTITLEMENT_PENDING_PLAN_CHANGE, ENTITLEMENT_PENDING_PLAN_CHANGE_APPROVAL,
   * ENTITLEMENT_SUSPENDED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. End time for the subscription corresponding to this
   * entitlement.
   *
   * @param string $subscriptionEndTime
   */
  public function setSubscriptionEndTime($subscriptionEndTime)
  {
    $this->subscriptionEndTime = $subscriptionEndTime;
  }
  /**
   * @return string
   */
  public function getSubscriptionEndTime()
  {
    return $this->subscriptionEndTime;
  }
  /**
   * Output only. The last update timestamp.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Output only. The consumerId to use when reporting usage through the Service
   * Control API. See the consumerId field at [Reporting
   * Metrics](https://cloud.google.com/service-control/reporting-metrics) for
   * more details. This field is present only if the product has usage-based
   * billing configured.
   *
   * @param string $usageReportingId
   */
  public function setUsageReportingId($usageReportingId)
  {
    $this->usageReportingId = $usageReportingId;
  }
  /**
   * @return string
   */
  public function getUsageReportingId()
  {
    return $this->usageReportingId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Entitlement::class, 'Google_Service_CloudCommercePartnerProcurementService_Entitlement');
