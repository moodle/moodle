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

namespace Google\Service\BigQueryConnectionService;

class SparkProperties extends \Google\Model
{
  protected $metastoreServiceConfigType = MetastoreServiceConfig::class;
  protected $metastoreServiceConfigDataType = '';
  /**
   * Output only. The account ID of the service created for the purpose of this
   * connection. The service account does not have any permissions associated
   * with it when it is created. After creation, customers delegate permissions
   * to the service account. When the connection is used in the context of a
   * stored procedure for Apache Spark in BigQuery, the service account is used
   * to connect to the desired resources in Google Cloud. The account ID is in
   * the form of: bqcx--@gcp-sa-bigquery-consp.iam.gserviceaccount.com
   *
   * @var string
   */
  public $serviceAccountId;
  protected $sparkHistoryServerConfigType = SparkHistoryServerConfig::class;
  protected $sparkHistoryServerConfigDataType = '';

  /**
   * Optional. Dataproc Metastore Service configuration for the connection.
   *
   * @param MetastoreServiceConfig $metastoreServiceConfig
   */
  public function setMetastoreServiceConfig(MetastoreServiceConfig $metastoreServiceConfig)
  {
    $this->metastoreServiceConfig = $metastoreServiceConfig;
  }
  /**
   * @return MetastoreServiceConfig
   */
  public function getMetastoreServiceConfig()
  {
    return $this->metastoreServiceConfig;
  }
  /**
   * Output only. The account ID of the service created for the purpose of this
   * connection. The service account does not have any permissions associated
   * with it when it is created. After creation, customers delegate permissions
   * to the service account. When the connection is used in the context of a
   * stored procedure for Apache Spark in BigQuery, the service account is used
   * to connect to the desired resources in Google Cloud. The account ID is in
   * the form of: bqcx--@gcp-sa-bigquery-consp.iam.gserviceaccount.com
   *
   * @param string $serviceAccountId
   */
  public function setServiceAccountId($serviceAccountId)
  {
    $this->serviceAccountId = $serviceAccountId;
  }
  /**
   * @return string
   */
  public function getServiceAccountId()
  {
    return $this->serviceAccountId;
  }
  /**
   * Optional. Spark History Server configuration for the connection.
   *
   * @param SparkHistoryServerConfig $sparkHistoryServerConfig
   */
  public function setSparkHistoryServerConfig(SparkHistoryServerConfig $sparkHistoryServerConfig)
  {
    $this->sparkHistoryServerConfig = $sparkHistoryServerConfig;
  }
  /**
   * @return SparkHistoryServerConfig
   */
  public function getSparkHistoryServerConfig()
  {
    return $this->sparkHistoryServerConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SparkProperties::class, 'Google_Service_BigQueryConnectionService_SparkProperties');
