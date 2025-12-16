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

class FhirStoreMetrics extends \Google\Collection
{
  protected $collection_key = 'metrics';
  protected $metricsType = FhirStoreMetric::class;
  protected $metricsDataType = 'array';
  /**
   * The resource name of the FHIR store to get metrics for, in the format
   * `projects/{project_id}/datasets/{dataset_id}/fhirStores/{fhir_store_id}`.
   *
   * @var string
   */
  public $name;

  /**
   * List of FhirStoreMetric by resource type.
   *
   * @param FhirStoreMetric[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return FhirStoreMetric[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * The resource name of the FHIR store to get metrics for, in the format
   * `projects/{project_id}/datasets/{dataset_id}/fhirStores/{fhir_store_id}`.
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
class_alias(FhirStoreMetrics::class, 'Google_Service_CloudHealthcare_FhirStoreMetrics');
