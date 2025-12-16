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

class Connection extends \Google\Model
{
  protected $awsType = AwsProperties::class;
  protected $awsDataType = '';
  protected $azureType = AzureProperties::class;
  protected $azureDataType = '';
  protected $cloudResourceType = CloudResourceProperties::class;
  protected $cloudResourceDataType = '';
  protected $cloudSpannerType = CloudSpannerProperties::class;
  protected $cloudSpannerDataType = '';
  protected $cloudSqlType = CloudSqlProperties::class;
  protected $cloudSqlDataType = '';
  protected $configurationType = ConnectorConfiguration::class;
  protected $configurationDataType = '';
  /**
   * Output only. The creation timestamp of the connection.
   *
   * @var string
   */
  public $creationTime;
  /**
   * User provided description.
   *
   * @var string
   */
  public $description;
  /**
   * User provided display name for the connection.
   *
   * @var string
   */
  public $friendlyName;
  /**
   * Output only. True, if credential is configured for this connection.
   *
   * @var bool
   */
  public $hasCredential;
  /**
   * Optional. The Cloud KMS key that is used for credentials encryption. If
   * omitted, internal Google owned encryption keys are used. Example: `projects
   * /[kms_project_id]/locations/[region]/keyRings/[key_region]/cryptoKeys/[key]
   * `
   *
   * @var string
   */
  public $kmsKeyName;
  /**
   * Output only. The last update timestamp of the connection.
   *
   * @var string
   */
  public $lastModifiedTime;
  /**
   * Output only. The resource name of the connection in the form of:
   * `projects/{project_id}/locations/{location_id}/connections/{connection_id}`
   *
   * @var string
   */
  public $name;
  protected $salesforceDataCloudType = SalesforceDataCloudProperties::class;
  protected $salesforceDataCloudDataType = '';
  protected $sparkType = SparkProperties::class;
  protected $sparkDataType = '';

  /**
   * Amazon Web Services (AWS) properties.
   *
   * @param AwsProperties $aws
   */
  public function setAws(AwsProperties $aws)
  {
    $this->aws = $aws;
  }
  /**
   * @return AwsProperties
   */
  public function getAws()
  {
    return $this->aws;
  }
  /**
   * Azure properties.
   *
   * @param AzureProperties $azure
   */
  public function setAzure(AzureProperties $azure)
  {
    $this->azure = $azure;
  }
  /**
   * @return AzureProperties
   */
  public function getAzure()
  {
    return $this->azure;
  }
  /**
   * Cloud Resource properties.
   *
   * @param CloudResourceProperties $cloudResource
   */
  public function setCloudResource(CloudResourceProperties $cloudResource)
  {
    $this->cloudResource = $cloudResource;
  }
  /**
   * @return CloudResourceProperties
   */
  public function getCloudResource()
  {
    return $this->cloudResource;
  }
  /**
   * Cloud Spanner properties.
   *
   * @param CloudSpannerProperties $cloudSpanner
   */
  public function setCloudSpanner(CloudSpannerProperties $cloudSpanner)
  {
    $this->cloudSpanner = $cloudSpanner;
  }
  /**
   * @return CloudSpannerProperties
   */
  public function getCloudSpanner()
  {
    return $this->cloudSpanner;
  }
  /**
   * Cloud SQL properties.
   *
   * @param CloudSqlProperties $cloudSql
   */
  public function setCloudSql(CloudSqlProperties $cloudSql)
  {
    $this->cloudSql = $cloudSql;
  }
  /**
   * @return CloudSqlProperties
   */
  public function getCloudSql()
  {
    return $this->cloudSql;
  }
  /**
   * Optional. Connector configuration.
   *
   * @param ConnectorConfiguration $configuration
   */
  public function setConfiguration(ConnectorConfiguration $configuration)
  {
    $this->configuration = $configuration;
  }
  /**
   * @return ConnectorConfiguration
   */
  public function getConfiguration()
  {
    return $this->configuration;
  }
  /**
   * Output only. The creation timestamp of the connection.
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * User provided description.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * User provided display name for the connection.
   *
   * @param string $friendlyName
   */
  public function setFriendlyName($friendlyName)
  {
    $this->friendlyName = $friendlyName;
  }
  /**
   * @return string
   */
  public function getFriendlyName()
  {
    return $this->friendlyName;
  }
  /**
   * Output only. True, if credential is configured for this connection.
   *
   * @param bool $hasCredential
   */
  public function setHasCredential($hasCredential)
  {
    $this->hasCredential = $hasCredential;
  }
  /**
   * @return bool
   */
  public function getHasCredential()
  {
    return $this->hasCredential;
  }
  /**
   * Optional. The Cloud KMS key that is used for credentials encryption. If
   * omitted, internal Google owned encryption keys are used. Example: `projects
   * /[kms_project_id]/locations/[region]/keyRings/[key_region]/cryptoKeys/[key]
   * `
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
   * Output only. The last update timestamp of the connection.
   *
   * @param string $lastModifiedTime
   */
  public function setLastModifiedTime($lastModifiedTime)
  {
    $this->lastModifiedTime = $lastModifiedTime;
  }
  /**
   * @return string
   */
  public function getLastModifiedTime()
  {
    return $this->lastModifiedTime;
  }
  /**
   * Output only. The resource name of the connection in the form of:
   * `projects/{project_id}/locations/{location_id}/connections/{connection_id}`
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
   * Optional. Salesforce DataCloud properties. This field is intended for use
   * only by Salesforce partner projects. This field contains properties for
   * your Salesforce DataCloud connection.
   *
   * @param SalesforceDataCloudProperties $salesforceDataCloud
   */
  public function setSalesforceDataCloud(SalesforceDataCloudProperties $salesforceDataCloud)
  {
    $this->salesforceDataCloud = $salesforceDataCloud;
  }
  /**
   * @return SalesforceDataCloudProperties
   */
  public function getSalesforceDataCloud()
  {
    return $this->salesforceDataCloud;
  }
  /**
   * Spark properties.
   *
   * @param SparkProperties $spark
   */
  public function setSpark(SparkProperties $spark)
  {
    $this->spark = $spark;
  }
  /**
   * @return SparkProperties
   */
  public function getSpark()
  {
    return $this->spark;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Connection::class, 'Google_Service_BigQueryConnectionService_Connection');
