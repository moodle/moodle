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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1alpha1SubscriberEvent extends \Google\Model
{
  protected $channelPartnerEventType = GoogleCloudChannelV1alpha1ChannelPartnerEvent::class;
  protected $channelPartnerEventDataType = '';
  protected $customerEventType = GoogleCloudChannelV1alpha1CustomerEvent::class;
  protected $customerEventDataType = '';
  protected $entitlementEventType = GoogleCloudChannelV1alpha1EntitlementEvent::class;
  protected $entitlementEventDataType = '';
  protected $opportunityEventType = GoogleCloudChannelV1alpha1OpportunityEvent::class;
  protected $opportunityEventDataType = '';

  /**
   * Channel Partner event sent as part of Pub/Sub event to partners.
   *
   * @param GoogleCloudChannelV1alpha1ChannelPartnerEvent $channelPartnerEvent
   */
  public function setChannelPartnerEvent(GoogleCloudChannelV1alpha1ChannelPartnerEvent $channelPartnerEvent)
  {
    $this->channelPartnerEvent = $channelPartnerEvent;
  }
  /**
   * @return GoogleCloudChannelV1alpha1ChannelPartnerEvent
   */
  public function getChannelPartnerEvent()
  {
    return $this->channelPartnerEvent;
  }
  /**
   * Customer event sent as part of Pub/Sub event to partners.
   *
   * @param GoogleCloudChannelV1alpha1CustomerEvent $customerEvent
   */
  public function setCustomerEvent(GoogleCloudChannelV1alpha1CustomerEvent $customerEvent)
  {
    $this->customerEvent = $customerEvent;
  }
  /**
   * @return GoogleCloudChannelV1alpha1CustomerEvent
   */
  public function getCustomerEvent()
  {
    return $this->customerEvent;
  }
  /**
   * Entitlement event sent as part of Pub/Sub event to partners.
   *
   * @param GoogleCloudChannelV1alpha1EntitlementEvent $entitlementEvent
   */
  public function setEntitlementEvent(GoogleCloudChannelV1alpha1EntitlementEvent $entitlementEvent)
  {
    $this->entitlementEvent = $entitlementEvent;
  }
  /**
   * @return GoogleCloudChannelV1alpha1EntitlementEvent
   */
  public function getEntitlementEvent()
  {
    return $this->entitlementEvent;
  }
  /**
   * Opportunity event sent as part of Pub/Sub event to partners/integrators.
   *
   * @param GoogleCloudChannelV1alpha1OpportunityEvent $opportunityEvent
   */
  public function setOpportunityEvent(GoogleCloudChannelV1alpha1OpportunityEvent $opportunityEvent)
  {
    $this->opportunityEvent = $opportunityEvent;
  }
  /**
   * @return GoogleCloudChannelV1alpha1OpportunityEvent
   */
  public function getOpportunityEvent()
  {
    return $this->opportunityEvent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1alpha1SubscriberEvent::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1alpha1SubscriberEvent');
