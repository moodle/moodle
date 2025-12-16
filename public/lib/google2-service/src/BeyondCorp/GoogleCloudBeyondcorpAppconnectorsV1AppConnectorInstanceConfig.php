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

namespace Google\Service\BeyondCorp;

class GoogleCloudBeyondcorpAppconnectorsV1AppConnectorInstanceConfig extends \Google\Model
{
  protected $imageConfigType = GoogleCloudBeyondcorpAppconnectorsV1ImageConfig::class;
  protected $imageConfigDataType = '';
  /**
   * The SLM instance agent configuration.
   *
   * @var array[]
   */
  public $instanceConfig;
  protected $notificationConfigType = GoogleCloudBeyondcorpAppconnectorsV1NotificationConfig::class;
  protected $notificationConfigDataType = '';
  /**
   * Required. A monotonically increasing number generated and maintained by the
   * API provider. Every time a config changes in the backend, the
   * sequenceNumber should be bumped up to reflect the change.
   *
   * @var string
   */
  public $sequenceNumber;

  /**
   * ImageConfig defines the GCR images to run for the remote agent's control
   * plane.
   *
   * @param GoogleCloudBeyondcorpAppconnectorsV1ImageConfig $imageConfig
   */
  public function setImageConfig(GoogleCloudBeyondcorpAppconnectorsV1ImageConfig $imageConfig)
  {
    $this->imageConfig = $imageConfig;
  }
  /**
   * @return GoogleCloudBeyondcorpAppconnectorsV1ImageConfig
   */
  public function getImageConfig()
  {
    return $this->imageConfig;
  }
  /**
   * The SLM instance agent configuration.
   *
   * @param array[] $instanceConfig
   */
  public function setInstanceConfig($instanceConfig)
  {
    $this->instanceConfig = $instanceConfig;
  }
  /**
   * @return array[]
   */
  public function getInstanceConfig()
  {
    return $this->instanceConfig;
  }
  /**
   * NotificationConfig defines the notification mechanism that the remote
   * instance should subscribe to in order to receive notification.
   *
   * @param GoogleCloudBeyondcorpAppconnectorsV1NotificationConfig $notificationConfig
   */
  public function setNotificationConfig(GoogleCloudBeyondcorpAppconnectorsV1NotificationConfig $notificationConfig)
  {
    $this->notificationConfig = $notificationConfig;
  }
  /**
   * @return GoogleCloudBeyondcorpAppconnectorsV1NotificationConfig
   */
  public function getNotificationConfig()
  {
    return $this->notificationConfig;
  }
  /**
   * Required. A monotonically increasing number generated and maintained by the
   * API provider. Every time a config changes in the backend, the
   * sequenceNumber should be bumped up to reflect the change.
   *
   * @param string $sequenceNumber
   */
  public function setSequenceNumber($sequenceNumber)
  {
    $this->sequenceNumber = $sequenceNumber;
  }
  /**
   * @return string
   */
  public function getSequenceNumber()
  {
    return $this->sequenceNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudBeyondcorpAppconnectorsV1AppConnectorInstanceConfig::class, 'Google_Service_BeyondCorp_GoogleCloudBeyondcorpAppconnectorsV1AppConnectorInstanceConfig');
