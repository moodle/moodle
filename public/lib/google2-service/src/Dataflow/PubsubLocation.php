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

namespace Google\Service\Dataflow;

class PubsubLocation extends \Google\Model
{
  /**
   * Indicates whether the pipeline allows late-arriving data.
   *
   * @var bool
   */
  public $dropLateData;
  /**
   * If true, then this location represents dynamic topics.
   *
   * @var bool
   */
  public $dynamicDestinations;
  /**
   * If set, contains a pubsub label from which to extract record ids. If left
   * empty, record deduplication will be strictly best effort.
   *
   * @var string
   */
  public $idLabel;
  /**
   * A pubsub subscription, in the form of
   * "pubsub.googleapis.com/subscriptions//"
   *
   * @var string
   */
  public $subscription;
  /**
   * If set, contains a pubsub label from which to extract record timestamps. If
   * left empty, record timestamps will be generated upon arrival.
   *
   * @var string
   */
  public $timestampLabel;
  /**
   * A pubsub topic, in the form of "pubsub.googleapis.com/topics//"
   *
   * @var string
   */
  public $topic;
  /**
   * If set, specifies the pubsub subscription that will be used for tracking
   * custom time timestamps for watermark estimation.
   *
   * @var string
   */
  public $trackingSubscription;
  /**
   * If true, then the client has requested to get pubsub attributes.
   *
   * @var bool
   */
  public $withAttributes;

  /**
   * Indicates whether the pipeline allows late-arriving data.
   *
   * @param bool $dropLateData
   */
  public function setDropLateData($dropLateData)
  {
    $this->dropLateData = $dropLateData;
  }
  /**
   * @return bool
   */
  public function getDropLateData()
  {
    return $this->dropLateData;
  }
  /**
   * If true, then this location represents dynamic topics.
   *
   * @param bool $dynamicDestinations
   */
  public function setDynamicDestinations($dynamicDestinations)
  {
    $this->dynamicDestinations = $dynamicDestinations;
  }
  /**
   * @return bool
   */
  public function getDynamicDestinations()
  {
    return $this->dynamicDestinations;
  }
  /**
   * If set, contains a pubsub label from which to extract record ids. If left
   * empty, record deduplication will be strictly best effort.
   *
   * @param string $idLabel
   */
  public function setIdLabel($idLabel)
  {
    $this->idLabel = $idLabel;
  }
  /**
   * @return string
   */
  public function getIdLabel()
  {
    return $this->idLabel;
  }
  /**
   * A pubsub subscription, in the form of
   * "pubsub.googleapis.com/subscriptions//"
   *
   * @param string $subscription
   */
  public function setSubscription($subscription)
  {
    $this->subscription = $subscription;
  }
  /**
   * @return string
   */
  public function getSubscription()
  {
    return $this->subscription;
  }
  /**
   * If set, contains a pubsub label from which to extract record timestamps. If
   * left empty, record timestamps will be generated upon arrival.
   *
   * @param string $timestampLabel
   */
  public function setTimestampLabel($timestampLabel)
  {
    $this->timestampLabel = $timestampLabel;
  }
  /**
   * @return string
   */
  public function getTimestampLabel()
  {
    return $this->timestampLabel;
  }
  /**
   * A pubsub topic, in the form of "pubsub.googleapis.com/topics//"
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
  /**
   * If set, specifies the pubsub subscription that will be used for tracking
   * custom time timestamps for watermark estimation.
   *
   * @param string $trackingSubscription
   */
  public function setTrackingSubscription($trackingSubscription)
  {
    $this->trackingSubscription = $trackingSubscription;
  }
  /**
   * @return string
   */
  public function getTrackingSubscription()
  {
    return $this->trackingSubscription;
  }
  /**
   * If true, then the client has requested to get pubsub attributes.
   *
   * @param bool $withAttributes
   */
  public function setWithAttributes($withAttributes)
  {
    $this->withAttributes = $withAttributes;
  }
  /**
   * @return bool
   */
  public function getWithAttributes()
  {
    return $this->withAttributes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PubsubLocation::class, 'Google_Service_Dataflow_PubsubLocation');
