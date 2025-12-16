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

namespace Google\Service\Container;

class MonitoringConfig extends \Google\Model
{
  protected $advancedDatapathObservabilityConfigType = AdvancedDatapathObservabilityConfig::class;
  protected $advancedDatapathObservabilityConfigDataType = '';
  protected $componentConfigType = MonitoringComponentConfig::class;
  protected $componentConfigDataType = '';
  protected $managedPrometheusConfigType = ManagedPrometheusConfig::class;
  protected $managedPrometheusConfigDataType = '';

  /**
   * Configuration of Advanced Datapath Observability features.
   *
   * @param AdvancedDatapathObservabilityConfig $advancedDatapathObservabilityConfig
   */
  public function setAdvancedDatapathObservabilityConfig(AdvancedDatapathObservabilityConfig $advancedDatapathObservabilityConfig)
  {
    $this->advancedDatapathObservabilityConfig = $advancedDatapathObservabilityConfig;
  }
  /**
   * @return AdvancedDatapathObservabilityConfig
   */
  public function getAdvancedDatapathObservabilityConfig()
  {
    return $this->advancedDatapathObservabilityConfig;
  }
  /**
   * Monitoring components configuration
   *
   * @param MonitoringComponentConfig $componentConfig
   */
  public function setComponentConfig(MonitoringComponentConfig $componentConfig)
  {
    $this->componentConfig = $componentConfig;
  }
  /**
   * @return MonitoringComponentConfig
   */
  public function getComponentConfig()
  {
    return $this->componentConfig;
  }
  /**
   * Enable Google Cloud Managed Service for Prometheus in the cluster.
   *
   * @param ManagedPrometheusConfig $managedPrometheusConfig
   */
  public function setManagedPrometheusConfig(ManagedPrometheusConfig $managedPrometheusConfig)
  {
    $this->managedPrometheusConfig = $managedPrometheusConfig;
  }
  /**
   * @return ManagedPrometheusConfig
   */
  public function getManagedPrometheusConfig()
  {
    return $this->managedPrometheusConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MonitoringConfig::class, 'Google_Service_Container_MonitoringConfig');
