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

namespace Google\Service\BlockchainNodeEngine;

class EthereumEndpoints extends \Google\Model
{
  /**
   * Output only. The assigned URL for the node's Beacon API endpoint.
   *
   * @var string
   */
  public $beaconApiEndpoint;
  /**
   * Output only. The assigned URL for the node's Beacon Prometheus metrics
   * endpoint. See [Prometheus Metrics](https://lighthouse-
   * book.sigmaprime.io/advanced_metrics.html) for more details.
   *
   * @var string
   */
  public $beaconPrometheusMetricsApiEndpoint;
  /**
   * Output only. The assigned URL for the node's execution client's Prometheus
   * metrics endpoint.
   *
   * @var string
   */
  public $executionClientPrometheusMetricsApiEndpoint;

  /**
   * Output only. The assigned URL for the node's Beacon API endpoint.
   *
   * @param string $beaconApiEndpoint
   */
  public function setBeaconApiEndpoint($beaconApiEndpoint)
  {
    $this->beaconApiEndpoint = $beaconApiEndpoint;
  }
  /**
   * @return string
   */
  public function getBeaconApiEndpoint()
  {
    return $this->beaconApiEndpoint;
  }
  /**
   * Output only. The assigned URL for the node's Beacon Prometheus metrics
   * endpoint. See [Prometheus Metrics](https://lighthouse-
   * book.sigmaprime.io/advanced_metrics.html) for more details.
   *
   * @param string $beaconPrometheusMetricsApiEndpoint
   */
  public function setBeaconPrometheusMetricsApiEndpoint($beaconPrometheusMetricsApiEndpoint)
  {
    $this->beaconPrometheusMetricsApiEndpoint = $beaconPrometheusMetricsApiEndpoint;
  }
  /**
   * @return string
   */
  public function getBeaconPrometheusMetricsApiEndpoint()
  {
    return $this->beaconPrometheusMetricsApiEndpoint;
  }
  /**
   * Output only. The assigned URL for the node's execution client's Prometheus
   * metrics endpoint.
   *
   * @param string $executionClientPrometheusMetricsApiEndpoint
   */
  public function setExecutionClientPrometheusMetricsApiEndpoint($executionClientPrometheusMetricsApiEndpoint)
  {
    $this->executionClientPrometheusMetricsApiEndpoint = $executionClientPrometheusMetricsApiEndpoint;
  }
  /**
   * @return string
   */
  public function getExecutionClientPrometheusMetricsApiEndpoint()
  {
    return $this->executionClientPrometheusMetricsApiEndpoint;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EthereumEndpoints::class, 'Google_Service_BlockchainNodeEngine_EthereumEndpoints');
