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

namespace Google\Service\PubsubLite;

class Subscription extends \Google\Model
{
  protected $deliveryConfigType = DeliveryConfig::class;
  protected $deliveryConfigDataType = '';
  protected $exportConfigType = ExportConfig::class;
  protected $exportConfigDataType = '';
  /**
   * The name of the subscription. Structured like: projects/{project_number}/lo
   * cations/{location}/subscriptions/{subscription_id}
   *
   * @var string
   */
  public $name;
  /**
   * The name of the topic this subscription is attached to. Structured like:
   * projects/{project_number}/locations/{location}/topics/{topic_id}
   *
   * @var string
   */
  public $topic;

  /**
   * The settings for this subscription's message delivery.
   *
   * @param DeliveryConfig $deliveryConfig
   */
  public function setDeliveryConfig(DeliveryConfig $deliveryConfig)
  {
    $this->deliveryConfig = $deliveryConfig;
  }
  /**
   * @return DeliveryConfig
   */
  public function getDeliveryConfig()
  {
    return $this->deliveryConfig;
  }
  /**
   * If present, messages are automatically written from the Pub/Sub Lite topic
   * associated with this subscription to a destination.
   *
   * @param ExportConfig $exportConfig
   */
  public function setExportConfig(ExportConfig $exportConfig)
  {
    $this->exportConfig = $exportConfig;
  }
  /**
   * @return ExportConfig
   */
  public function getExportConfig()
  {
    return $this->exportConfig;
  }
  /**
   * The name of the subscription. Structured like: projects/{project_number}/lo
   * cations/{location}/subscriptions/{subscription_id}
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
   * The name of the topic this subscription is attached to. Structured like:
   * projects/{project_number}/locations/{location}/topics/{topic_id}
   *
   * @param string $topic
   */
  public function setTopic($topic)
  {
    $this->topic = $topic;
  }
  /**
   * @return string
   */
  public function getTopic()
  {
    return $this->topic;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Subscription::class, 'Google_Service_PubsubLite_Subscription');
