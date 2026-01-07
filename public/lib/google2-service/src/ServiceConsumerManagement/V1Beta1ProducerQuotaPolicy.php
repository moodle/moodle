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

namespace Google\Service\ServiceConsumerManagement;

class V1Beta1ProducerQuotaPolicy extends \Google\Model
{
  /**
   * The cloud resource container at which the quota policy is created. The
   * format is {container_type}/{container_number}
   *
   * @var string
   */
  public $container;
  /**
   * If this map is nonempty, then this policy applies only to specific values
   * for dimensions defined in the limit unit. For example, a policy on a limit
   * with the unit 1/{project}/{region} could contain an entry with the key
   * "region" and the value "us-east-1"; the policy is only applied to quota
   * consumed in that region. This map has the following restrictions: * Keys
   * that are not defined in the limit's unit are not valid keys. Any string
   * appearing in {brackets} in the unit (besides {project} or {user}) is a
   * defined key. * "project" is not a valid key; the project is already
   * specified in the parent resource name. * "user" is not a valid key; the API
   * does not support quota polcies that apply only to a specific user. * If
   * "region" appears as a key, its value must be a valid Cloud region. * If
   * "zone" appears as a key, its value must be a valid Cloud zone. * If any
   * valid key other than "region" or "zone" appears in the map, then all valid
   * keys other than "region" or "zone" must also appear in the map.
   *
   * @var string[]
   */
  public $dimensions;
  /**
   * The name of the metric to which this policy applies. An example name would
   * be: `compute.googleapis.com/cpus`
   *
   * @var string
   */
  public $metric;
  /**
   * The resource name of the producer policy. An example name would be: `servic
   * es/compute.googleapis.com/organizations/123/consumerQuotaMetrics/compute.go
   * ogleapis.com%2Fcpus/limits/%2Fproject%2Fregion/producerQuotaPolicies/4a3f2c
   * 1d`
   *
   * @var string
   */
  public $name;
  /**
   * The quota policy value. Can be any nonnegative integer, or -1 (unlimited
   * quota).
   *
   * @var string
   */
  public $policyValue;
  /**
   * The limit unit of the limit to which this policy applies. An example unit
   * would be: `1/{project}/{region}` Note that `{project}` and `{region}` are
   * not placeholders in this example; the literal characters `{` and `}` occur
   * in the string.
   *
   * @var string
   */
  public $unit;

  /**
   * The cloud resource container at which the quota policy is created. The
   * format is {container_type}/{container_number}
   *
   * @param string $container
   */
  public function setContainer($container)
  {
    $this->container = $container;
  }
  /**
   * @return string
   */
  public function getContainer()
  {
    return $this->container;
  }
  /**
   * If this map is nonempty, then this policy applies only to specific values
   * for dimensions defined in the limit unit. For example, a policy on a limit
   * with the unit 1/{project}/{region} could contain an entry with the key
   * "region" and the value "us-east-1"; the policy is only applied to quota
   * consumed in that region. This map has the following restrictions: * Keys
   * that are not defined in the limit's unit are not valid keys. Any string
   * appearing in {brackets} in the unit (besides {project} or {user}) is a
   * defined key. * "project" is not a valid key; the project is already
   * specified in the parent resource name. * "user" is not a valid key; the API
   * does not support quota polcies that apply only to a specific user. * If
   * "region" appears as a key, its value must be a valid Cloud region. * If
   * "zone" appears as a key, its value must be a valid Cloud zone. * If any
   * valid key other than "region" or "zone" appears in the map, then all valid
   * keys other than "region" or "zone" must also appear in the map.
   *
   * @param string[] $dimensions
   */
  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return string[]
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  /**
   * The name of the metric to which this policy applies. An example name would
   * be: `compute.googleapis.com/cpus`
   *
   * @param string $metric
   */
  public function setMetric($metric)
  {
    $this->metric = $metric;
  }
  /**
   * @return string
   */
  public function getMetric()
  {
    return $this->metric;
  }
  /**
   * The resource name of the producer policy. An example name would be: `servic
   * es/compute.googleapis.com/organizations/123/consumerQuotaMetrics/compute.go
   * ogleapis.com%2Fcpus/limits/%2Fproject%2Fregion/producerQuotaPolicies/4a3f2c
   * 1d`
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
   * The quota policy value. Can be any nonnegative integer, or -1 (unlimited
   * quota).
   *
   * @param string $policyValue
   */
  public function setPolicyValue($policyValue)
  {
    $this->policyValue = $policyValue;
  }
  /**
   * @return string
   */
  public function getPolicyValue()
  {
    return $this->policyValue;
  }
  /**
   * The limit unit of the limit to which this policy applies. An example unit
   * would be: `1/{project}/{region}` Note that `{project}` and `{region}` are
   * not placeholders in this example; the literal characters `{` and `}` occur
   * in the string.
   *
   * @param string $unit
   */
  public function setUnit($unit)
  {
    $this->unit = $unit;
  }
  /**
   * @return string
   */
  public function getUnit()
  {
    return $this->unit;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(V1Beta1ProducerQuotaPolicy::class, 'Google_Service_ServiceConsumerManagement_V1Beta1ProducerQuotaPolicy');
