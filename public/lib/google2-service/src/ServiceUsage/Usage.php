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

namespace Google\Service\ServiceUsage;

class Usage extends \Google\Collection
{
  protected $collection_key = 'rules';
  /**
   * The full resource name of a channel used for sending notifications to the
   * service producer. Google Service Management currently only supports [Google
   * Cloud Pub/Sub](https://cloud.google.com/pubsub) as a notification channel.
   * To use Google Cloud Pub/Sub as the channel, this must be the name of a
   * Cloud Pub/Sub topic that uses the Cloud Pub/Sub topic name format
   * documented in https://cloud.google.com/pubsub/docs/overview.
   *
   * @var string
   */
  public $producerNotificationChannel;
  /**
   * Requirements that must be satisfied before a consumer project can use the
   * service. Each requirement is of the form /; for example
   * 'serviceusage.googleapis.com/billing-enabled'. For Google APIs, a Terms of
   * Service requirement must be included here. Google Cloud APIs must include
   * "serviceusage.googleapis.com/tos/cloud". Other Google APIs should include
   * "serviceusage.googleapis.com/tos/universal". Additional ToS can be included
   * based on the business needs.
   *
   * @var string[]
   */
  public $requirements;
  protected $rulesType = UsageRule::class;
  protected $rulesDataType = 'array';

  /**
   * The full resource name of a channel used for sending notifications to the
   * service producer. Google Service Management currently only supports [Google
   * Cloud Pub/Sub](https://cloud.google.com/pubsub) as a notification channel.
   * To use Google Cloud Pub/Sub as the channel, this must be the name of a
   * Cloud Pub/Sub topic that uses the Cloud Pub/Sub topic name format
   * documented in https://cloud.google.com/pubsub/docs/overview.
   *
   * @param string $producerNotificationChannel
   */
  public function setProducerNotificationChannel($producerNotificationChannel)
  {
    $this->producerNotificationChannel = $producerNotificationChannel;
  }
  /**
   * @return string
   */
  public function getProducerNotificationChannel()
  {
    return $this->producerNotificationChannel;
  }
  /**
   * Requirements that must be satisfied before a consumer project can use the
   * service. Each requirement is of the form /; for example
   * 'serviceusage.googleapis.com/billing-enabled'. For Google APIs, a Terms of
   * Service requirement must be included here. Google Cloud APIs must include
   * "serviceusage.googleapis.com/tos/cloud". Other Google APIs should include
   * "serviceusage.googleapis.com/tos/universal". Additional ToS can be included
   * based on the business needs.
   *
   * @param string[] $requirements
   */
  public function setRequirements($requirements)
  {
    $this->requirements = $requirements;
  }
  /**
   * @return string[]
   */
  public function getRequirements()
  {
    return $this->requirements;
  }
  /**
   * A list of usage rules that apply to individual API methods. **NOTE:** All
   * service configuration rules follow "last one wins" order.
   *
   * @param UsageRule[] $rules
   */
  public function setRules($rules)
  {
    $this->rules = $rules;
  }
  /**
   * @return UsageRule[]
   */
  public function getRules()
  {
    return $this->rules;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Usage::class, 'Google_Service_ServiceUsage_Usage');
