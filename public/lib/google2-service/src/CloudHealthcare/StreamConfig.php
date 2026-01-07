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

class StreamConfig extends \Google\Collection
{
  protected $collection_key = 'resourceTypes';
  protected $bigqueryDestinationType = GoogleCloudHealthcareV1FhirBigQueryDestination::class;
  protected $bigqueryDestinationDataType = '';
  protected $deidentifiedStoreDestinationType = DeidentifiedStoreDestination::class;
  protected $deidentifiedStoreDestinationDataType = '';
  /**
   * Optional. Supply a FHIR resource type (such as "Patient" or "Observation").
   * See https://www.hl7.org/fhir/valueset-resource-types.html for a list of all
   * FHIR resource types. The server treats an empty list as an intent to stream
   * all the supported resource types in this FHIR store.
   *
   * @var string[]
   */
  public $resourceTypes;

  /**
   * Optional. The destination BigQuery structure that contains both the dataset
   * location and corresponding schema config. The output is organized in one
   * table per resource type. The server reuses the existing tables (if any)
   * that are named after the resource types. For example, "Patient",
   * "Observation". When there is no existing table for a given resource type,
   * the server attempts to create one. When a table schema doesn't align with
   * the schema config, either because of existing incompatible schema or out of
   * band incompatible modification, the server does not stream in new data.
   * BigQuery imposes a 1 MB limit on streaming insert row size, therefore any
   * resource mutation that generates more than 1 MB of BigQuery data is not
   * streamed. One resolution in this case is to delete the incompatible table
   * and let the server recreate one, though the newly created table only
   * contains data after the table recreation. Results are written to BigQuery
   * tables according to the parameters in BigQueryDestination.WriteDisposition.
   * Different versions of the same resource are distinguishable by the
   * meta.versionId and meta.lastUpdated columns. The operation
   * (CREATE/UPDATE/DELETE) that results in the new version is recorded in the
   * meta.tag. The tables contain all historical resource versions since
   * streaming was enabled. For query convenience, the server also creates one
   * view per table of the same name containing only the current resource
   * version. The streamed data in the BigQuery dataset is not guaranteed to be
   * completely unique. The combination of the id and meta.versionId columns
   * should ideally identify a single unique row. But in rare cases, duplicates
   * may exist. At query time, users may use the SQL select statement to keep
   * only one of the duplicate rows given an id and meta.versionId pair.
   * Alternatively, the server created view mentioned above also filters out
   * duplicates. If a resource mutation cannot be streamed to BigQuery, errors
   * are logged to Cloud Logging. For more information, see [Viewing error logs
   * in Cloud Logging](https://cloud.google.com/healthcare/docs/how-
   * tos/logging)).
   *
   * @param GoogleCloudHealthcareV1FhirBigQueryDestination $bigqueryDestination
   */
  public function setBigqueryDestination(GoogleCloudHealthcareV1FhirBigQueryDestination $bigqueryDestination)
  {
    $this->bigqueryDestination = $bigqueryDestination;
  }
  /**
   * @return GoogleCloudHealthcareV1FhirBigQueryDestination
   */
  public function getBigqueryDestination()
  {
    return $this->bigqueryDestination;
  }
  /**
   * The destination FHIR store for de-identified resources. After this field is
   * added, all subsequent creates/updates/patches to the source store will be
   * de-identified using the provided configuration and applied to the
   * destination store. Resources deleted from the source store will be deleted
   * from the destination store. Importing resources to the source store will
   * not trigger the streaming. If the source store already contains resources
   * when this option is enabled, those resources will not be copied to the
   * destination store unless they are subsequently updated. This may result in
   * invalid references in the destination store. Before adding this config, you
   * must grant the healthcare.fhirResources.update permission on the
   * destination store to your project's **Cloud Healthcare Service Agent**
   * [service account](https://cloud.google.com/healthcare/docs/how-
   * tos/permissions-healthcare-api-gcp-
   * products#the_cloud_healthcare_service_agent). The destination store must
   * set enable_update_create to true. The destination store must have
   * disable_referential_integrity set to true. If a resource cannot be de-
   * identified, errors will be logged to Cloud Logging (see [Viewing error logs
   * in Cloud Logging](https://cloud.google.com/healthcare/docs/how-
   * tos/logging)). Not supported for R5 stores.
   *
   * @param DeidentifiedStoreDestination $deidentifiedStoreDestination
   */
  public function setDeidentifiedStoreDestination(DeidentifiedStoreDestination $deidentifiedStoreDestination)
  {
    $this->deidentifiedStoreDestination = $deidentifiedStoreDestination;
  }
  /**
   * @return DeidentifiedStoreDestination
   */
  public function getDeidentifiedStoreDestination()
  {
    return $this->deidentifiedStoreDestination;
  }
  /**
   * Optional. Supply a FHIR resource type (such as "Patient" or "Observation").
   * See https://www.hl7.org/fhir/valueset-resource-types.html for a list of all
   * FHIR resource types. The server treats an empty list as an intent to stream
   * all the supported resource types in this FHIR store.
   *
   * @param string[] $resourceTypes
   */
  public function setResourceTypes($resourceTypes)
  {
    $this->resourceTypes = $resourceTypes;
  }
  /**
   * @return string[]
   */
  public function getResourceTypes()
  {
    return $this->resourceTypes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StreamConfig::class, 'Google_Service_CloudHealthcare_StreamConfig');
