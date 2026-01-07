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

namespace Google\Service\WorkspaceEvents;

class Subscription extends \Google\Collection
{
  /**
   * Default value. This value is unused.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The subscription is active and can receive and deliver events to its
   * notification endpoint.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The subscription is unable to receive events due to an error. To identify
   * the error, see the `suspension_reason` field.
   */
  public const STATE_SUSPENDED = 'SUSPENDED';
  /**
   * The subscription is deleted.
   */
  public const STATE_DELETED = 'DELETED';
  /**
   * Default value. This value is unused.
   */
  public const SUSPENSION_REASON_ERROR_TYPE_UNSPECIFIED = 'ERROR_TYPE_UNSPECIFIED';
  /**
   * The authorizing user has revoked the grant of one or more OAuth scopes. To
   * learn more about authorization for Google Workspace, see [Configure the
   * OAuth consent
   * screen](https://developers.google.com/workspace/guides/configure-oauth-
   * consent#choose-scopes).
   */
  public const SUSPENSION_REASON_USER_SCOPE_REVOKED = 'USER_SCOPE_REVOKED';
  /**
   * The target resource for the subscription no longer exists.
   */
  public const SUSPENSION_REASON_RESOURCE_DELETED = 'RESOURCE_DELETED';
  /**
   * The user that authorized the creation of the subscription no longer has
   * access to the subscription's target resource.
   */
  public const SUSPENSION_REASON_USER_AUTHORIZATION_FAILURE = 'USER_AUTHORIZATION_FAILURE';
  /**
   * The Google Workspace application doesn't have access to deliver events to
   * your subscription's notification endpoint.
   */
  public const SUSPENSION_REASON_ENDPOINT_PERMISSION_DENIED = 'ENDPOINT_PERMISSION_DENIED';
  /**
   * The subscription's notification endpoint doesn't exist, or the endpoint
   * can't be found in the Google Cloud project where you created the
   * subscription.
   */
  public const SUSPENSION_REASON_ENDPOINT_NOT_FOUND = 'ENDPOINT_NOT_FOUND';
  /**
   * The subscription's notification endpoint failed to receive events due to
   * insufficient quota or reaching rate limiting.
   */
  public const SUSPENSION_REASON_ENDPOINT_RESOURCE_EXHAUSTED = 'ENDPOINT_RESOURCE_EXHAUSTED';
  /**
   * An unidentified error has occurred.
   */
  public const SUSPENSION_REASON_OTHER = 'OTHER';
  protected $collection_key = 'eventTypes';
  /**
   * Output only. The user who authorized the creation of the subscription. When
   * a user authorizes the subscription, this field and the `user_authority`
   * field have the same value and the format is: Format: `users/{user}` For
   * Google Workspace users, the `{user}` value is the
   * [`user.id`](https://developers.google.com/admin-
   * sdk/directory/reference/rest/v1/users#User.FIELDS.ids) field from the
   * Directory API. When a Chat app authorizes the subscription, only
   * `service_account_authority` field populates and this field is empty.
   *
   * @var string
   */
  public $authority;
  /**
   * Output only. The time when the subscription is created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. This checksum is computed by the server based on the value of
   * other fields, and might be sent on update requests to ensure the client has
   * an up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Required. Unordered list. Input for creating a subscription. Otherwise,
   * output only. One or more types of events to receive about the target
   * resource. Formatted according to the CloudEvents specification. The
   * supported event types depend on the target resource of your subscription.
   * For details, see [Supported Google Workspace
   * events](https://developers.google.com/workspace/events/guides#supported-
   * events). By default, you also receive events about the [lifecycle of your
   * subscription](https://developers.google.com/workspace/events/guides/events-
   * lifecycle). You don't need to specify lifecycle events for this field. If
   * you specify an event type that doesn't exist for the target resource, the
   * request returns an HTTP `400 Bad Request` status code.
   *
   * @var string[]
   */
  public $eventTypes;
  /**
   * Non-empty default. The timestamp in UTC when the subscription expires.
   * Always displayed on output, regardless of what was used on input.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Identifier. Resource name of the subscription. Format:
   * `subscriptions/{subscription}`
   *
   * @var string
   */
  public $name;
  protected $notificationEndpointType = NotificationEndpoint::class;
  protected $notificationEndpointDataType = '';
  protected $payloadOptionsType = PayloadOptions::class;
  protected $payloadOptionsDataType = '';
  /**
   * Output only. If `true`, the subscription is in the process of being
   * updated.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Output only. The state of the subscription. Determines whether the
   * subscription can receive events and deliver them to the notification
   * endpoint.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The error that suspended the subscription. To reactivate the
   * subscription, resolve the error and call the `ReactivateSubscription`
   * method.
   *
   * @var string
   */
  public $suspensionReason;
  /**
   * Required. Immutable. The Google Workspace resource that's monitored for
   * events, formatted as the [full resource
   * name](https://google.aip.dev/122#full-resource-names). To learn about
   * target resources and the events that they support, see [Supported Google
   * Workspace events](https://developers.google.com/workspace/events#supported-
   * events). A user can only authorize your app to create one subscription for
   * a given target resource. If your app tries to create another subscription
   * with the same user credentials, the request returns an `ALREADY_EXISTS`
   * error.
   *
   * @var string
   */
  public $targetResource;
  /**
   * Input only. The time-to-live (TTL) or duration for the subscription. If
   * unspecified or set to `0`, uses the maximum possible duration.
   *
   * @var string
   */
  public $ttl;
  /**
   * Output only. System-assigned unique identifier for the subscription.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The last time that the subscription is updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The user who authorized the creation of the subscription. When
   * a user authorizes the subscription, this field and the `user_authority`
   * field have the same value and the format is: Format: `users/{user}` For
   * Google Workspace users, the `{user}` value is the
   * [`user.id`](https://developers.google.com/admin-
   * sdk/directory/reference/rest/v1/users#User.FIELDS.ids) field from the
   * Directory API. When a Chat app authorizes the subscription, only
   * `service_account_authority` field populates and this field is empty.
   *
   * @param string $authority
   */
  public function setAuthority($authority)
  {
    $this->authority = $authority;
  }
  /**
   * @return string
   */
  public function getAuthority()
  {
    return $this->authority;
  }
  /**
   * Output only. The time when the subscription is created.
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
   * Optional. This checksum is computed by the server based on the value of
   * other fields, and might be sent on update requests to ensure the client has
   * an up-to-date value before proceeding.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Required. Unordered list. Input for creating a subscription. Otherwise,
   * output only. One or more types of events to receive about the target
   * resource. Formatted according to the CloudEvents specification. The
   * supported event types depend on the target resource of your subscription.
   * For details, see [Supported Google Workspace
   * events](https://developers.google.com/workspace/events/guides#supported-
   * events). By default, you also receive events about the [lifecycle of your
   * subscription](https://developers.google.com/workspace/events/guides/events-
   * lifecycle). You don't need to specify lifecycle events for this field. If
   * you specify an event type that doesn't exist for the target resource, the
   * request returns an HTTP `400 Bad Request` status code.
   *
   * @param string[] $eventTypes
   */
  public function setEventTypes($eventTypes)
  {
    $this->eventTypes = $eventTypes;
  }
  /**
   * @return string[]
   */
  public function getEventTypes()
  {
    return $this->eventTypes;
  }
  /**
   * Non-empty default. The timestamp in UTC when the subscription expires.
   * Always displayed on output, regardless of what was used on input.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Identifier. Resource name of the subscription. Format:
   * `subscriptions/{subscription}`
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
   * Required. Immutable. The endpoint where the subscription delivers events,
   * such as a Pub/Sub topic.
   *
   * @param NotificationEndpoint $notificationEndpoint
   */
  public function setNotificationEndpoint(NotificationEndpoint $notificationEndpoint)
  {
    $this->notificationEndpoint = $notificationEndpoint;
  }
  /**
   * @return NotificationEndpoint
   */
  public function getNotificationEndpoint()
  {
    return $this->notificationEndpoint;
  }
  /**
   * Optional. Options about what data to include in the event payload. Only
   * supported for Google Chat and Google Drive events.
   *
   * @param PayloadOptions $payloadOptions
   */
  public function setPayloadOptions(PayloadOptions $payloadOptions)
  {
    $this->payloadOptions = $payloadOptions;
  }
  /**
   * @return PayloadOptions
   */
  public function getPayloadOptions()
  {
    return $this->payloadOptions;
  }
  /**
   * Output only. If `true`, the subscription is in the process of being
   * updated.
   *
   * @param bool $reconciling
   */
  public function setReconciling($reconciling)
  {
    $this->reconciling = $reconciling;
  }
  /**
   * @return bool
   */
  public function getReconciling()
  {
    return $this->reconciling;
  }
  /**
   * Output only. The state of the subscription. Determines whether the
   * subscription can receive events and deliver them to the notification
   * endpoint.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, SUSPENDED, DELETED
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
   * Output only. The error that suspended the subscription. To reactivate the
   * subscription, resolve the error and call the `ReactivateSubscription`
   * method.
   *
   * Accepted values: ERROR_TYPE_UNSPECIFIED, USER_SCOPE_REVOKED,
   * RESOURCE_DELETED, USER_AUTHORIZATION_FAILURE, ENDPOINT_PERMISSION_DENIED,
   * ENDPOINT_NOT_FOUND, ENDPOINT_RESOURCE_EXHAUSTED, OTHER
   *
   * @param self::SUSPENSION_REASON_* $suspensionReason
   */
  public function setSuspensionReason($suspensionReason)
  {
    $this->suspensionReason = $suspensionReason;
  }
  /**
   * @return self::SUSPENSION_REASON_*
   */
  public function getSuspensionReason()
  {
    return $this->suspensionReason;
  }
  /**
   * Required. Immutable. The Google Workspace resource that's monitored for
   * events, formatted as the [full resource
   * name](https://google.aip.dev/122#full-resource-names). To learn about
   * target resources and the events that they support, see [Supported Google
   * Workspace events](https://developers.google.com/workspace/events#supported-
   * events). A user can only authorize your app to create one subscription for
   * a given target resource. If your app tries to create another subscription
   * with the same user credentials, the request returns an `ALREADY_EXISTS`
   * error.
   *
   * @param string $targetResource
   */
  public function setTargetResource($targetResource)
  {
    $this->targetResource = $targetResource;
  }
  /**
   * @return string
   */
  public function getTargetResource()
  {
    return $this->targetResource;
  }
  /**
   * Input only. The time-to-live (TTL) or duration for the subscription. If
   * unspecified or set to `0`, uses the maximum possible duration.
   *
   * @param string $ttl
   */
  public function setTtl($ttl)
  {
    $this->ttl = $ttl;
  }
  /**
   * @return string
   */
  public function getTtl()
  {
    return $this->ttl;
  }
  /**
   * Output only. System-assigned unique identifier for the subscription.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. The last time that the subscription is updated.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Subscription::class, 'Google_Service_WorkspaceEvents_Subscription');
