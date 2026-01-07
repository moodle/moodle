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

namespace Google\Service\AnalyticsHub;

class GooglePubsubV1Subscription extends \Google\Collection
{
  protected $collection_key = 'messageTransforms';
  /**
   * Optional. The approximate amount of time (on a best-effort basis) Pub/Sub
   * waits for the subscriber to acknowledge receipt before resending the
   * message. In the interval after the message is delivered and before it is
   * acknowledged, it is considered to be _outstanding_. During that time
   * period, the message will not be redelivered (on a best-effort basis). For
   * pull subscriptions, this value is used as the initial value for the ack
   * deadline. To override this value for a given message, call
   * `ModifyAckDeadline` with the corresponding `ack_id` if using non-streaming
   * pull or send the `ack_id` in a `StreamingModifyAckDeadlineRequest` if using
   * streaming pull. The minimum custom deadline you can specify is 10 seconds.
   * The maximum custom deadline you can specify is 600 seconds (10 minutes). If
   * this parameter is 0, a default value of 10 seconds is used. For push
   * delivery, this value is also used to set the request timeout for the call
   * to the push endpoint. If the subscriber never acknowledges the message, the
   * Pub/Sub system will eventually redeliver the message.
   *
   * @var int
   */
  public $ackDeadlineSeconds;
  protected $bigqueryConfigType = BigQueryConfig::class;
  protected $bigqueryConfigDataType = '';
  protected $cloudStorageConfigType = CloudStorageConfig::class;
  protected $cloudStorageConfigDataType = '';
  protected $deadLetterPolicyType = DeadLetterPolicy::class;
  protected $deadLetterPolicyDataType = '';
  /**
   * Optional. Indicates whether the subscription is detached from its topic.
   * Detached subscriptions don't receive messages from their topic and don't
   * retain any backlog. `Pull` and `StreamingPull` requests will return
   * FAILED_PRECONDITION. If the subscription is a push subscription, pushes to
   * the endpoint will not be made.
   *
   * @var bool
   */
  public $detached;
  /**
   * Optional. If true, Pub/Sub provides the following guarantees for the
   * delivery of a message with a given value of `message_id` on this
   * subscription: * The message sent to a subscriber is guaranteed not to be
   * resent before the message's acknowledgement deadline expires. * An
   * acknowledged message will not be resent to a subscriber. Note that
   * subscribers may still receive multiple copies of a message when
   * `enable_exactly_once_delivery` is true if the message was published
   * multiple times by a publisher client. These copies are considered distinct
   * by Pub/Sub and have distinct `message_id` values.
   *
   * @var bool
   */
  public $enableExactlyOnceDelivery;
  /**
   * Optional. If true, messages published with the same `ordering_key` in
   * `PubsubMessage` will be delivered to the subscribers in the order in which
   * they are received by the Pub/Sub system. Otherwise, they may be delivered
   * in any order.
   *
   * @var bool
   */
  public $enableMessageOrdering;
  protected $expirationPolicyType = ExpirationPolicy::class;
  protected $expirationPolicyDataType = '';
  /**
   * Optional. An expression written in the Pub/Sub [filter
   * language](https://cloud.google.com/pubsub/docs/filtering). If non-empty,
   * then only `PubsubMessage`s whose `attributes` field matches the filter are
   * delivered on this subscription. If empty, then no messages are filtered
   * out.
   *
   * @var string
   */
  public $filter;
  /**
   * Optional. See [Creating and managing
   * labels](https://cloud.google.com/pubsub/docs/labels).
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. How long to retain unacknowledged messages in the subscription's
   * backlog, from the moment a message is published. If `retain_acked_messages`
   * is true, then this also configures the retention of acknowledged messages,
   * and thus configures how far back in time a `Seek` can be done. Defaults to
   * 7 days. Cannot be more than 31 days or less than 10 minutes.
   *
   * @var string
   */
  public $messageRetentionDuration;
  protected $messageTransformsType = MessageTransform::class;
  protected $messageTransformsDataType = 'array';
  /**
   * Required. Name of the subscription. Format is
   * `projects/{project}/subscriptions/{sub}`.
   *
   * @var string
   */
  public $name;
  protected $pushConfigType = PushConfig::class;
  protected $pushConfigDataType = '';
  /**
   * Optional. Indicates whether to retain acknowledged messages. If true, then
   * messages are not expunged from the subscription's backlog, even if they are
   * acknowledged, until they fall out of the `message_retention_duration`
   * window. This must be true if you would like to [`Seek` to a timestamp]
   * (https://cloud.google.com/pubsub/docs/replay-overview#seek_to_a_time) in
   * the past to replay previously-acknowledged messages.
   *
   * @var bool
   */
  public $retainAckedMessages;
  protected $retryPolicyType = RetryPolicy::class;
  protected $retryPolicyDataType = '';
  /**
   * Optional. Input only. Immutable. Tag keys/values directly bound to this
   * resource. For example: "123/environment": "production", "123/costCenter":
   * "marketing"
   *
   * @var string[]
   */
  public $tags;

  /**
   * Optional. The approximate amount of time (on a best-effort basis) Pub/Sub
   * waits for the subscriber to acknowledge receipt before resending the
   * message. In the interval after the message is delivered and before it is
   * acknowledged, it is considered to be _outstanding_. During that time
   * period, the message will not be redelivered (on a best-effort basis). For
   * pull subscriptions, this value is used as the initial value for the ack
   * deadline. To override this value for a given message, call
   * `ModifyAckDeadline` with the corresponding `ack_id` if using non-streaming
   * pull or send the `ack_id` in a `StreamingModifyAckDeadlineRequest` if using
   * streaming pull. The minimum custom deadline you can specify is 10 seconds.
   * The maximum custom deadline you can specify is 600 seconds (10 minutes). If
   * this parameter is 0, a default value of 10 seconds is used. For push
   * delivery, this value is also used to set the request timeout for the call
   * to the push endpoint. If the subscriber never acknowledges the message, the
   * Pub/Sub system will eventually redeliver the message.
   *
   * @param int $ackDeadlineSeconds
   */
  public function setAckDeadlineSeconds($ackDeadlineSeconds)
  {
    $this->ackDeadlineSeconds = $ackDeadlineSeconds;
  }
  /**
   * @return int
   */
  public function getAckDeadlineSeconds()
  {
    return $this->ackDeadlineSeconds;
  }
  /**
   * Optional. If delivery to BigQuery is used with this subscription, this
   * field is used to configure it.
   *
   * @param BigQueryConfig $bigqueryConfig
   */
  public function setBigqueryConfig(BigQueryConfig $bigqueryConfig)
  {
    $this->bigqueryConfig = $bigqueryConfig;
  }
  /**
   * @return BigQueryConfig
   */
  public function getBigqueryConfig()
  {
    return $this->bigqueryConfig;
  }
  /**
   * Optional. If delivery to Google Cloud Storage is used with this
   * subscription, this field is used to configure it.
   *
   * @param CloudStorageConfig $cloudStorageConfig
   */
  public function setCloudStorageConfig(CloudStorageConfig $cloudStorageConfig)
  {
    $this->cloudStorageConfig = $cloudStorageConfig;
  }
  /**
   * @return CloudStorageConfig
   */
  public function getCloudStorageConfig()
  {
    return $this->cloudStorageConfig;
  }
  /**
   * Optional. A policy that specifies the conditions for dead lettering
   * messages in this subscription. If dead_letter_policy is not set, dead
   * lettering is disabled. The Pub/Sub service account associated with this
   * subscriptions's parent project (i.e., service-{project_number}@gcp-sa-
   * pubsub.iam.gserviceaccount.com) must have permission to Acknowledge()
   * messages on this subscription.
   *
   * @param DeadLetterPolicy $deadLetterPolicy
   */
  public function setDeadLetterPolicy(DeadLetterPolicy $deadLetterPolicy)
  {
    $this->deadLetterPolicy = $deadLetterPolicy;
  }
  /**
   * @return DeadLetterPolicy
   */
  public function getDeadLetterPolicy()
  {
    return $this->deadLetterPolicy;
  }
  /**
   * Optional. Indicates whether the subscription is detached from its topic.
   * Detached subscriptions don't receive messages from their topic and don't
   * retain any backlog. `Pull` and `StreamingPull` requests will return
   * FAILED_PRECONDITION. If the subscription is a push subscription, pushes to
   * the endpoint will not be made.
   *
   * @param bool $detached
   */
  public function setDetached($detached)
  {
    $this->detached = $detached;
  }
  /**
   * @return bool
   */
  public function getDetached()
  {
    return $this->detached;
  }
  /**
   * Optional. If true, Pub/Sub provides the following guarantees for the
   * delivery of a message with a given value of `message_id` on this
   * subscription: * The message sent to a subscriber is guaranteed not to be
   * resent before the message's acknowledgement deadline expires. * An
   * acknowledged message will not be resent to a subscriber. Note that
   * subscribers may still receive multiple copies of a message when
   * `enable_exactly_once_delivery` is true if the message was published
   * multiple times by a publisher client. These copies are considered distinct
   * by Pub/Sub and have distinct `message_id` values.
   *
   * @param bool $enableExactlyOnceDelivery
   */
  public function setEnableExactlyOnceDelivery($enableExactlyOnceDelivery)
  {
    $this->enableExactlyOnceDelivery = $enableExactlyOnceDelivery;
  }
  /**
   * @return bool
   */
  public function getEnableExactlyOnceDelivery()
  {
    return $this->enableExactlyOnceDelivery;
  }
  /**
   * Optional. If true, messages published with the same `ordering_key` in
   * `PubsubMessage` will be delivered to the subscribers in the order in which
   * they are received by the Pub/Sub system. Otherwise, they may be delivered
   * in any order.
   *
   * @param bool $enableMessageOrdering
   */
  public function setEnableMessageOrdering($enableMessageOrdering)
  {
    $this->enableMessageOrdering = $enableMessageOrdering;
  }
  /**
   * @return bool
   */
  public function getEnableMessageOrdering()
  {
    return $this->enableMessageOrdering;
  }
  /**
   * Optional. A policy that specifies the conditions for this subscription's
   * expiration. A subscription is considered active as long as any connected
   * subscriber is successfully consuming messages from the subscription or is
   * issuing operations on the subscription. If `expiration_policy` is not set,
   * a *default policy* with `ttl` of 31 days will be used. The minimum allowed
   * value for `expiration_policy.ttl` is 1 day. If `expiration_policy` is set,
   * but `expiration_policy.ttl` is not set, the subscription never expires.
   *
   * @param ExpirationPolicy $expirationPolicy
   */
  public function setExpirationPolicy(ExpirationPolicy $expirationPolicy)
  {
    $this->expirationPolicy = $expirationPolicy;
  }
  /**
   * @return ExpirationPolicy
   */
  public function getExpirationPolicy()
  {
    return $this->expirationPolicy;
  }
  /**
   * Optional. An expression written in the Pub/Sub [filter
   * language](https://cloud.google.com/pubsub/docs/filtering). If non-empty,
   * then only `PubsubMessage`s whose `attributes` field matches the filter are
   * delivered on this subscription. If empty, then no messages are filtered
   * out.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Optional. See [Creating and managing
   * labels](https://cloud.google.com/pubsub/docs/labels).
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Optional. How long to retain unacknowledged messages in the subscription's
   * backlog, from the moment a message is published. If `retain_acked_messages`
   * is true, then this also configures the retention of acknowledged messages,
   * and thus configures how far back in time a `Seek` can be done. Defaults to
   * 7 days. Cannot be more than 31 days or less than 10 minutes.
   *
   * @param string $messageRetentionDuration
   */
  public function setMessageRetentionDuration($messageRetentionDuration)
  {
    $this->messageRetentionDuration = $messageRetentionDuration;
  }
  /**
   * @return string
   */
  public function getMessageRetentionDuration()
  {
    return $this->messageRetentionDuration;
  }
  /**
   * Optional. Transforms to be applied to messages before they are delivered to
   * subscribers. Transforms are applied in the order specified.
   *
   * @param MessageTransform[] $messageTransforms
   */
  public function setMessageTransforms($messageTransforms)
  {
    $this->messageTransforms = $messageTransforms;
  }
  /**
   * @return MessageTransform[]
   */
  public function getMessageTransforms()
  {
    return $this->messageTransforms;
  }
  /**
   * Required. Name of the subscription. Format is
   * `projects/{project}/subscriptions/{sub}`.
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
   * Optional. If push delivery is used with this subscription, this field is
   * used to configure it.
   *
   * @param PushConfig $pushConfig
   */
  public function setPushConfig(PushConfig $pushConfig)
  {
    $this->pushConfig = $pushConfig;
  }
  /**
   * @return PushConfig
   */
  public function getPushConfig()
  {
    return $this->pushConfig;
  }
  /**
   * Optional. Indicates whether to retain acknowledged messages. If true, then
   * messages are not expunged from the subscription's backlog, even if they are
   * acknowledged, until they fall out of the `message_retention_duration`
   * window. This must be true if you would like to [`Seek` to a timestamp]
   * (https://cloud.google.com/pubsub/docs/replay-overview#seek_to_a_time) in
   * the past to replay previously-acknowledged messages.
   *
   * @param bool $retainAckedMessages
   */
  public function setRetainAckedMessages($retainAckedMessages)
  {
    $this->retainAckedMessages = $retainAckedMessages;
  }
  /**
   * @return bool
   */
  public function getRetainAckedMessages()
  {
    return $this->retainAckedMessages;
  }
  /**
   * Optional. A policy that specifies how Pub/Sub retries message delivery for
   * this subscription. If not set, the default retry policy is applied. This
   * generally implies that messages will be retried as soon as possible for
   * healthy subscribers. RetryPolicy will be triggered on NACKs or
   * acknowledgement deadline exceeded events for a given message.
   *
   * @param RetryPolicy $retryPolicy
   */
  public function setRetryPolicy(RetryPolicy $retryPolicy)
  {
    $this->retryPolicy = $retryPolicy;
  }
  /**
   * @return RetryPolicy
   */
  public function getRetryPolicy()
  {
    return $this->retryPolicy;
  }
  /**
   * Optional. Input only. Immutable. Tag keys/values directly bound to this
   * resource. For example: "123/environment": "production", "123/costCenter":
   * "marketing"
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePubsubV1Subscription::class, 'Google_Service_AnalyticsHub_GooglePubsubV1Subscription');
