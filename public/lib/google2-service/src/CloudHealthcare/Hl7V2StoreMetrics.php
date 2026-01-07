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

namespace Google\Service\CloudHealthcare;

class Hl7V2StoreMetrics extends \Google\Collection
{
  protected $collection_key = 'metrics';
  protected $metricsType = Hl7V2StoreMetric::class;
  protected $metricsDataType = 'array';
  /**
   * The resource name of the HL7v2 store to get metrics for, in the format
   * `projects/{project_id}/datasets/{dataset_id}/hl7V2Stores/{hl7v2_store_id}`.
   *
   * @var string
   */
  public $name;

  /**
   * List of HL7v2 store metrics by message type.
   *
   * @param Hl7V2StoreMetric[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return Hl7V2StoreMetric[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * The resource name of the HL7v2 store to get metrics for, in the format
   * `projects/{project_id}/datasets/{dataset_id}/hl7V2Stores/{hl7v2_store_id}`.
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
class_alias(Hl7V2StoreMetrics::class, 'Google_Service_CloudHealthcare_Hl7V2StoreMetrics');
