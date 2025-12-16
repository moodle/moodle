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

class ExportResourcesRequest extends \Google\Model
{
  protected $internal_gapi_mappings = [
        "since" => "_since",
        "type" => "_type",
  ];
  /**
   * If provided, only resources updated after this time are exported. The time
   * uses the format YYYY-MM-DDThh:mm:ss.sss+zz:zz. For example,
   * `2015-02-07T13:28:17.239+02:00` or `2017-01-01T00:00:00Z`. The time must be
   * specified to the second and include a time zone.
   *
   * @var string
   */
  public $since;
  /**
   * String of comma-delimited FHIR resource types. If provided, only resources
   * of the specified resource type(s) are exported.
   *
   * @var string
   */
  public $type;
  protected $bigqueryDestinationType = GoogleCloudHealthcareV1FhirBigQueryDestination::class;
  protected $bigqueryDestinationDataType = '';
  protected $gcsDestinationType = GoogleCloudHealthcareV1FhirGcsDestination::class;
  protected $gcsDestinationDataType = '';

  /**
   * If provided, only resources updated after this time are exported. The time
   * uses the format YYYY-MM-DDThh:mm:ss.sss+zz:zz. For example,
   * `2015-02-07T13:28:17.239+02:00` or `2017-01-01T00:00:00Z`. The time must be
   * specified to the second and include a time zone.
   *
   * @param string $since
   */
  public function setSince($since)
  {
    $this->since = $since;
  }
  /**
   * @return string
   */
  public function getSince()
  {
    return $this->since;
  }
  /**
   * String of comma-delimited FHIR resource types. If provided, only resources
   * of the specified resource type(s) are exported.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * The BigQuery output destination. The Cloud Healthcare Service Agent
   * requires two IAM roles on the BigQuery location:
   * `roles/bigquery.dataEditor` and `roles/bigquery.jobUser`. The output is one
   * BigQuery table per resource type. Unlike when setting `BigQueryDestination`
   * for `StreamConfig`, `ExportResources` does not create BigQuery views.
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
   * The Cloud Storage output destination. The Healthcare Service Agent account
   * requires the `roles/storage.objectAdmin` role on the Cloud Storage
   * location. The exported outputs are organized by FHIR resource types. The
   * server creates one object per resource type. Each object contains newline
   * delimited JSON, and each line is a FHIR resource.
   *
   * @param GoogleCloudHealthcareV1FhirGcsDestination $gcsDestination
   */
  public function setGcsDestination(GoogleCloudHealthcareV1FhirGcsDestination $gcsDestination)
  {
    $this->gcsDestination = $gcsDestination;
  }
  /**
   * @return GoogleCloudHealthcareV1FhirGcsDestination
   */
  public function getGcsDestination()
  {
    return $this->gcsDestination;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExportResourcesRequest::class, 'Google_Service_CloudHealthcare_ExportResourcesRequest');
