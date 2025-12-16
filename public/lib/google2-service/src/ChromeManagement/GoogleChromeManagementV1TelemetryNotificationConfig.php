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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementV1TelemetryNotificationConfig extends \Google\Model
{
  /**
   * Output only. Google Workspace customer that owns the resource.
   *
   * @var string
   */
  public $customer;
  protected $filterType = GoogleChromeManagementV1TelemetryNotificationFilter::class;
  protected $filterDataType = '';
  /**
   * The pubsub topic to which notifications are published to.
   *
   * @var string
   */
  public $googleCloudPubsubTopic;
  /**
   * Output only. Resource name of the notification configuration.
   *
   * @var string
   */
  public $name;

  /**
   * Output only. Google Workspace customer that owns the resource.
   *
   * @param string $customer
   */
  public function setCustomer($customer)
  {
    $this->customer = $customer;
  }
  /**
   * @return string
   */
  public function getCustomer()
  {
    return $this->customer;
  }
  /**
   * Only send notifications for telemetry data matching this filter.
   *
   * @param GoogleChromeManagementV1TelemetryNotificationFilter $filter
   */
  public function setFilter(GoogleChromeManagementV1TelemetryNotificationFilter $filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return GoogleChromeManagementV1TelemetryNotificationFilter
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * The pubsub topic to which notifications are published to.
   *
   * @param string $googleCloudPubsubTopic
   */
  public function setGoogleCloudPubsubTopic($googleCloudPubsubTopic)
  {
    $this->googleCloudPubsubTopic = $googleCloudPubsubTopic;
  }
  /**
   * @return string
   */
  public function getGoogleCloudPubsubTopic()
  {
    return $this->googleCloudPubsubTopic;
  }
  /**
   * Output only. Resource name of the notification configuration.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1TelemetryNotificationConfig::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1TelemetryNotificationConfig');
