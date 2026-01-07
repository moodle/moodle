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

class GoogleCloudHealthcareV1DicomStreamConfig extends \Google\Model
{
  protected $bigqueryDestinationType = GoogleCloudHealthcareV1DicomBigQueryDestination::class;
  protected $bigqueryDestinationDataType = '';

  /**
   * Results are appended to this table. The server creates a new table in the
   * given BigQuery dataset if the specified table does not exist. To enable the
   * Cloud Healthcare API to write to your BigQuery table, you must give the
   * Cloud Healthcare API service account the bigquery.dataEditor role. The
   * service account is: `service-{PROJECT_NUMBER}@gcp-sa-
   * healthcare.iam.gserviceaccount.com`. The PROJECT_NUMBER identifies the
   * project that the DICOM store resides in. To get the project number, go to
   * the Cloud Console Dashboard. It is recommended to not have a custom schema
   * in the destination table which could conflict with the schema created by
   * the Cloud Healthcare API. Instance deletions are not applied to the
   * destination table. The destination's table schema will be automatically
   * updated in case a new instance's data is incompatible with the current
   * schema. The schema should not be updated manually as this can cause
   * incompatibilies that cannot be resolved automatically. One resolution in
   * this case is to delete the incompatible table and let the server recreate
   * one, though the newly created table only contains data after the table
   * recreation. BigQuery imposes a 1 MB limit on streaming insert row size,
   * therefore any instance that generates more than 1 MB of BigQuery data will
   * not be streamed. If an instance cannot be streamed to BigQuery, errors will
   * be logged to Cloud Logging (see [Viewing error logs in Cloud
   * Logging](https://cloud.google.com/healthcare/docs/how-tos/logging)).
   *
   * @param GoogleCloudHealthcareV1DicomBigQueryDestination $bigqueryDestination
   */
  public function setBigqueryDestination(GoogleCloudHealthcareV1DicomBigQueryDestination $bigqueryDestination)
  {
    $this->bigqueryDestination = $bigqueryDestination;
  }
  /**
   * @return GoogleCloudHealthcareV1DicomBigQueryDestination
   */
  public function getBigqueryDestination()
  {
    return $this->bigqueryDestination;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudHealthcareV1DicomStreamConfig::class, 'Google_Service_CloudHealthcare_GoogleCloudHealthcareV1DicomStreamConfig');
