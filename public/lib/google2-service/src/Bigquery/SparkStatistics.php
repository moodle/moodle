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

namespace Google\Service\Bigquery;

class SparkStatistics extends \Google\Model
{
  /**
   * Output only. Endpoints returned from Dataproc. Key list: -
   * history_server_endpoint: A link to Spark job UI.
   *
   * @var string[]
   */
  public $endpoints;
  /**
   * Output only. The Google Cloud Storage bucket that is used as the default
   * file system by the Spark application. This field is only filled when the
   * Spark procedure uses the invoker security mode. The `gcsStagingBucket`
   * bucket is inferred from the `@@spark_proc_properties.staging_bucket` system
   * variable (if it is provided). Otherwise, BigQuery creates a default staging
   * bucket for the job and returns the bucket name in this field. Example: *
   * `gs://[bucket_name]`
   *
   * @var string
   */
  public $gcsStagingBucket;
  /**
   * Output only. The Cloud KMS encryption key that is used to protect the
   * resources created by the Spark job. If the Spark procedure uses the invoker
   * security mode, the Cloud KMS encryption key is either inferred from the
   * provided system variable, `@@spark_proc_properties.kms_key_name`, or the
   * default key of the BigQuery job's project (if the CMEK organization policy
   * is enforced). Otherwise, the Cloud KMS key is either inferred from the
   * Spark connection associated with the procedure (if it is provided), or from
   * the default key of the Spark connection's project if the CMEK organization
   * policy is enforced. Example: * `projects/[kms_project_id]/locations/[region
   * ]/keyRings/[key_region]/cryptoKeys/[key]`
   *
   * @var string
   */
  public $kmsKeyName;
  protected $loggingInfoType = SparkLoggingInfo::class;
  protected $loggingInfoDataType = '';
  /**
   * Output only. Spark job ID if a Spark job is created successfully.
   *
   * @var string
   */
  public $sparkJobId;
  /**
   * Output only. Location where the Spark job is executed. A location is
   * selected by BigQueury for jobs configured to run in a multi-region.
   *
   * @var string
   */
  public $sparkJobLocation;

  /**
   * Output only. Endpoints returned from Dataproc. Key list: -
   * history_server_endpoint: A link to Spark job UI.
   *
   * @param string[] $endpoints
   */
  public function setEndpoints($endpoints)
  {
    $this->endpoints = $endpoints;
  }
  /**
   * @return string[]
   */
  public function getEndpoints()
  {
    return $this->endpoints;
  }
  /**
   * Output only. The Google Cloud Storage bucket that is used as the default
   * file system by the Spark application. This field is only filled when the
   * Spark procedure uses the invoker security mode. The `gcsStagingBucket`
   * bucket is inferred from the `@@spark_proc_properties.staging_bucket` system
   * variable (if it is provided). Otherwise, BigQuery creates a default staging
   * bucket for the job and returns the bucket name in this field. Example: *
   * `gs://[bucket_name]`
   *
   * @param string $gcsStagingBucket
   */
  public function setGcsStagingBucket($gcsStagingBucket)
  {
    $this->gcsStagingBucket = $gcsStagingBucket;
  }
  /**
   * @return string
   */
  public function getGcsStagingBucket()
  {
    return $this->gcsStagingBucket;
  }
  /**
   * Output only. The Cloud KMS encryption key that is used to protect the
   * resources created by the Spark job. If the Spark procedure uses the invoker
   * security mode, the Cloud KMS encryption key is either inferred from the
   * provided system variable, `@@spark_proc_properties.kms_key_name`, or the
   * default key of the BigQuery job's project (if the CMEK organization policy
   * is enforced). Otherwise, the Cloud KMS key is either inferred from the
   * Spark connection associated with the procedure (if it is provided), or from
   * the default key of the Spark connection's project if the CMEK organization
   * policy is enforced. Example: * `projects/[kms_project_id]/locations/[region
   * ]/keyRings/[key_region]/cryptoKeys/[key]`
   *
   * @param string $kmsKeyName
   */
  public function setKmsKeyName($kmsKeyName)
  {
    $this->kmsKeyName = $kmsKeyName;
  }
  /**
   * @return string
   */
  public function getKmsKeyName()
  {
    return $this->kmsKeyName;
  }
  /**
   * Output only. Logging info is used to generate a link to Cloud Logging.
   *
   * @param SparkLoggingInfo $loggingInfo
   */
  public function setLoggingInfo(SparkLoggingInfo $loggingInfo)
  {
    $this->loggingInfo = $loggingInfo;
  }
  /**
   * @return SparkLoggingInfo
   */
  public function getLoggingInfo()
  {
    return $this->loggingInfo;
  }
  /**
   * Output only. Spark job ID if a Spark job is created successfully.
   *
   * @param string $sparkJobId
   */
  public function setSparkJobId($sparkJobId)
  {
    $this->sparkJobId = $sparkJobId;
  }
  /**
   * @return string
   */
  public function getSparkJobId()
  {
    return $this->sparkJobId;
  }
  /**
   * Output only. Location where the Spark job is executed. A location is
   * selected by BigQueury for jobs configured to run in a multi-region.
   *
   * @param string $sparkJobLocation
   */
  public function setSparkJobLocation($sparkJobLocation)
  {
    $this->sparkJobLocation = $sparkJobLocation;
  }
  /**
   * @return string
   */
  public function getSparkJobLocation()
  {
    return $this->sparkJobLocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SparkStatistics::class, 'Google_Service_Bigquery_SparkStatistics');
