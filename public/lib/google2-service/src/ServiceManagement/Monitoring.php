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

namespace Google\Service\ServiceManagement;

class Monitoring extends \Google\Collection
{
  protected $collection_key = 'producerDestinations';
  protected $consumerDestinationsType = MonitoringDestination::class;
  protected $consumerDestinationsDataType = 'array';
  protected $producerDestinationsType = MonitoringDestination::class;
  protected $producerDestinationsDataType = 'array';

  /**
   * Monitoring configurations for sending metrics to the consumer project.
   * There can be multiple consumer destinations. A monitored resource type may
   * appear in multiple monitoring destinations if different aggregations are
   * needed for different sets of metrics associated with that monitored
   * resource type. A monitored resource and metric pair may only be used once
   * in the Monitoring configuration.
   *
   * @param MonitoringDestination[] $consumerDestinations
   */
  public function setConsumerDestinations($consumerDestinations)
  {
    $this->consumerDestinations = $consumerDestinations;
  }
  /**
   * @return MonitoringDestination[]
   */
  public function getConsumerDestinations()
  {
    return $this->consumerDestinations;
  }
  /**
   * Monitoring configurations for sending metrics to the producer project.
   * There can be multiple producer destinations. A monitored resource type may
   * appear in multiple monitoring destinations if different aggregations are
   * needed for different sets of metrics associated with that monitored
   * resource type. A monitored resource and metric pair may only be used once
   * in the Monitoring configuration.
   *
   * @param MonitoringDestination[] $producerDestinations
   */
  public function setProducerDestinations($producerDestinations)
  {
    $this->producerDestinations = $producerDestinations;
  }
  /**
   * @return MonitoringDestination[]
   */
  public function getProducerDestinations()
  {
    return $this->producerDestinations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Monitoring::class, 'Google_Service_ServiceManagement_Monitoring');
