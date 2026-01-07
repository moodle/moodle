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

namespace Google\Service\Eventarc;

class Trigger extends \Google\Collection
{
  protected $collection_key = 'eventFilters';
  /**
   * Optional. The name of the channel associated with the trigger in
   * `projects/{project}/locations/{location}/channels/{channel}` format. You
   * must provide a channel to receive events from Eventarc SaaS partners.
   *
   * @var string
   */
  public $channel;
  protected $conditionsType = StateCondition::class;
  protected $conditionsDataType = 'map';
  /**
   * Output only. The creation time.
   *
   * @var string
   */
  public $createTime;
  protected $destinationType = Destination::class;
  protected $destinationDataType = '';
  /**
   * Output only. This checksum is computed by the server based on the value of
   * other fields, and might be sent only on create requests to ensure that the
   * client has an up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. EventDataContentType specifies the type of payload in MIME format
   * that is expected from the CloudEvent data field. This is set to
   * `application/json` if the value is not defined.
   *
   * @var string
   */
  public $eventDataContentType;
  protected $eventFiltersType = EventFilter::class;
  protected $eventFiltersDataType = 'array';
  /**
   * Optional. User labels attached to the triggers that can be used to group
   * resources.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. The resource name of the trigger. Must be unique within the
   * location of the project and must be in
   * `projects/{project}/locations/{location}/triggers/{trigger}` format.
   *
   * @var string
   */
  public $name;
  protected $retryPolicyType = RetryPolicy::class;
  protected $retryPolicyDataType = '';
  /**
   * Output only. Whether or not this Trigger satisfies the requirements of
   * physical zone separation
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Optional. The IAM service account email associated with the trigger. The
   * service account represents the identity of the trigger. The
   * `iam.serviceAccounts.actAs` permission must be granted on the service
   * account to allow a principal to impersonate the service account. For more
   * information, see the [Roles and permissions](/eventarc/docs/all-roles-
   * permissions) page specific to the trigger destination.
   *
   * @var string
   */
  public $serviceAccount;
  protected $transportType = Transport::class;
  protected $transportDataType = '';
  /**
   * Output only. Server-assigned unique identifier for the trigger. The value
   * is a UUID4 string and guaranteed to remain unchanged until the resource is
   * deleted.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The last-modified time.
   *
   * @var string
   */
  public $updateTime;

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
   * Output only. The reason(s) why a trigger is in FAILED state.
   *
   * @param StateCondition[] $conditions
   */
  public function setConditions($conditions)
  {
    $this->conditions = $conditions;
  }
  /**
   * @return StateCondition[]
   */
  public function getConditions()
  {
    return $this->conditions;
  }
  /**
   * Output only. The creation time.
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
   * Required. Destination specifies where the events should be sent to.
   *
   * @param Destination $destination
   */
  public function setDestination(Destination $destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return Destination
   */
  public function getDestination()
  {
    return $this->destination;
  }
  /**
   * Output only. This checksum is computed by the server based on the value of
   * other fields, and might be sent only on create requests to ensure that the
   * client has an up-to-date value before proceeding.
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
   * Optional. EventDataContentType specifies the type of payload in MIME format
   * that is expected from the CloudEvent data field. This is set to
   * `application/json` if the value is not defined.
   *
   * @param string $eventDataContentType
   */
  public function setEventDataContentType($eventDataContentType)
  {
    $this->eventDataContentType = $eventDataContentType;
  }
  /**
   * @return string
   */
  public function getEventDataContentType()
  {
    return $this->eventDataContentType;
  }
  /**
   * Required. Unordered list. The list of filters that applies to event
   * attributes. Only events that match all the provided filters are sent to the
   * destination.
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
   * Optional. User labels attached to the triggers that can be used to group
   * resources.
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
   * Required. The resource name of the trigger. Must be unique within the
   * location of the project and must be in
   * `projects/{project}/locations/{location}/triggers/{trigger}` format.
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
   * Optional. The retry policy to use in the Trigger. If unset, event delivery
   * will be retried for up to 24 hours by default:
   * https://cloud.google.com/eventarc/docs/retry-events
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
   * Output only. Whether or not this Trigger satisfies the requirements of
   * physical zone separation
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Optional. The IAM service account email associated with the trigger. The
   * service account represents the identity of the trigger. The
   * `iam.serviceAccounts.actAs` permission must be granted on the service
   * account to allow a principal to impersonate the service account. For more
   * information, see the [Roles and permissions](/eventarc/docs/all-roles-
   * permissions) page specific to the trigger destination.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Optional. To deliver messages, Eventarc might use other Google Cloud
   * products as a transport intermediary. This field contains a reference to
   * that transport intermediary. This information can be used for debugging
   * purposes.
   *
   * @param Transport $transport
   */
  public function setTransport(Transport $transport)
  {
    $this->transport = $transport;
  }
  /**
   * @return Transport
   */
  public function getTransport()
  {
    return $this->transport;
  }
  /**
   * Output only. Server-assigned unique identifier for the trigger. The value
   * is a UUID4 string and guaranteed to remain unchanged until the resource is
   * deleted.
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
   * Output only. The last-modified time.
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
class_alias(Trigger::class, 'Google_Service_Eventarc_Trigger');
