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

namespace Google\Service\CloudFunctions;

class EventTrigger extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const RETRY_POLICY_RETRY_POLICY_UNSPECIFIED = 'RETRY_POLICY_UNSPECIFIED';
  /**
   * Do not retry.
   */
  public const RETRY_POLICY_RETRY_POLICY_DO_NOT_RETRY = 'RETRY_POLICY_DO_NOT_RETRY';
  /**
   * Retry on any failure, retry up to 7 days with an exponential backoff
   * (capped at 10 seconds).
   */
  public const RETRY_POLICY_RETRY_POLICY_RETRY = 'RETRY_POLICY_RETRY';
  protected $collection_key = 'eventFilters';
  /**
   * Optional. The name of the channel associated with the trigger in
   * `projects/{project}/locations/{location}/channels/{channel}` format. You
   * must provide a channel to receive events from Eventarc SaaS partners.
   *
   * @var string
   */
  public $channel;
  protected $eventFiltersType = EventFilter::class;
  protected $eventFiltersDataType = 'array';
  /**
   * Required. The type of event to observe. For example:
   * `google.cloud.audit.log.v1.written` or
   * `google.cloud.pubsub.topic.v1.messagePublished`.
   *
   * @var string
   */
  public $eventType;
  /**
   * Optional. The name of a Pub/Sub topic in the same project that will be used
   * as the transport topic for the event delivery. Format:
   * `projects/{project}/topics/{topic}`. This is only valid for events of type
   * `google.cloud.pubsub.topic.v1.messagePublished`. The topic provided here
   * will not be deleted at function deletion.
   *
   * @var string
   */
  public $pubsubTopic;
  /**
   * Optional. If unset, then defaults to ignoring failures (i.e. not retrying
   * them).
   *
   * @var string
   */
  public $retryPolicy;
  /**
   * Optional. The hostname of the service that 1st Gen function should be
   * observed. If no string is provided, the default service implementing the
   * API will be used. For example, `storage.googleapis.com` is the default for
   * all event types in the `google.storage` namespace. The field is only
   * applicable to 1st Gen functions.
   *
   * @var string
   */
  public $service;
  /**
   * Optional. The email of the trigger's service account. The service account
   * must have permission to invoke Cloud Run services, the permission is
   * `run.routes.invoke`. If empty, defaults to the Compute Engine default
   * service account: `{project_number}-compute@developer.gserviceaccount.com`.
   *
   * @var string
   */
  public $serviceAccountEmail;
  /**
   * Output only. The resource name of the Eventarc trigger. The format of this
   * field is `projects/{project}/locations/{region}/triggers/{trigger}`.
   *
   * @var string
   */
  public $trigger;
  /**
   * The region that the trigger will be in. The trigger will only receive
   * events originating in this region. It can be the same region as the
   * function, a different region or multi-region, or the global region. If not
   * provided, defaults to the same region as the function.
   *
   * @var string
   */
  public $triggerRegion;

  /**
   * Optional. The name of the channel associated with the trigger in
   * `projects/{project}/locations/{location}/channels/{channel}` format. You
   * must provide a channel to receive events from Eventarc SaaS partners.
   *
   * @param string $channel
   */
  public function setChannel($channel)
  {
    $this->channel = $channel;
  }
  /**
   * @return string
   */
  public function getChannel()
  {
    return $this->channel;
  }
  /**
   * Criteria used to filter events.
   *
   * @param EventFilter[] $eventFilters
   */
  public function setEventFilters($eventFilters)
  {
    $this->eventFilters = $eventFilters;
  }
  /**
   * @return EventFilter[]
   */
  public function getEventFilters()
  {
    return $this->eventFilters;
  }
  /**
   * Required. The type of event to observe. For example:
   * `google.cloud.audit.log.v1.written` or
   * `google.cloud.pubsub.topic.v1.messagePublished`.
   *
   * @param string $eventType
   */
  public function setEventType($eventType)
  {
    $this->eventType = $eventType;
  }
  /**
   * @return string
   */
  public function getEventType()
  {
    return $this->eventType;
  }
  /**
   * Optional. The name of a Pub/Sub topic in the same project that will be used
   * as the transport topic for the event delivery. Format:
   * `projects/{project}/topics/{topic}`. This is only valid for events of type
   * `google.cloud.pubsub.topic.v1.messagePublished`. The topic provided here
   * will not be deleted at function deletion.
   *
   * @param string $pubsubTopic
   */
  public function setPubsubTopic($pubsubTopic)
  {
    $this->pubsubTopic = $pubsubTopic;
  }
  /**
   * @return string
   */
  public function getPubsubTopic()
  {
    return $this->pubsubTopic;
  }
  /**
   * Optional. If unset, then defaults to ignoring failures (i.e. not retrying
   * them).
   *
   * Accepted values: RETRY_POLICY_UNSPECIFIED, RETRY_POLICY_DO_NOT_RETRY,
   * RETRY_POLICY_RETRY
   *
   * @param self::RETRY_POLICY_* $retryPolicy
   */
  public function setRetryPolicy($retryPolicy)
  {
    $this->retryPolicy = $retryPolicy;
  }
  /**
   * @return self::RETRY_POLICY_*
   */
  public function getRetryPolicy()
  {
    return $this->retryPolicy;
  }
  /**
   * Optional. The hostname of the service that 1st Gen function should be
   * observed. If no string is provided, the default service implementing the
   * API will be used. For example, `storage.googleapis.com` is the default for
   * all event types in the `google.storage` namespace. The field is only
   * applicable to 1st Gen functions.
   *
   * @param string $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @return string
   */
  public function getService()
  {
    return $this->service;
  }
  /**
   * Optional. The email of the trigger's service account. The service account
   * must have permission to invoke Cloud Run services, the permission is
   * `run.routes.invoke`. If empty, defaults to the Compute Engine default
   * service account: `{project_number}-compute@developer.gserviceaccount.com`.
   *
   * @param string $serviceAccountEmail
   */
  public function setServiceAccountEmail($serviceAccountEmail)
  {
    $this->serviceAccountEmail = $serviceAccountEmail;
  }
  /**
   * @return string
   */
  public function getServiceAccountEmail()
  {
    return $this->serviceAccountEmail;
  }
  /**
   * Output only. The resource name of the Eventarc trigger. The format of this
   * field is `projects/{project}/locations/{region}/triggers/{trigger}`.
   *
   * @param string $trigger
   */
  public function setTrigger($trigger)
  {
    $this->trigger = $trigger;
  }
  /**
   * @return string
   */
  public function getTrigger()
  {
    return $this->trigger;
  }
  /**
   * The region that the trigger will be in. The trigger will only receive
   * events originating in this region. It can be the same region as the
   * function, a different region or multi-region, or the global region. If not
   * provided, defaults to the same region as the function.
   *
   * @param string $triggerRegion
   */
  public function setTriggerRegion($triggerRegion)
  {
    $this->triggerRegion = $triggerRegion;
  }
  /**
   * @return string
   */
  public function getTriggerRegion()
  {
    return $this->triggerRegion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EventTrigger::class, 'Google_Service_CloudFunctions_EventTrigger');
