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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1Action extends \Google\Collection
{
  /**
   * Unspecified category.
   */
  public const CATEGORY_CATEGORY_UNSPECIFIED = 'CATEGORY_UNSPECIFIED';
  /**
   * Resource management related issues.
   */
  public const CATEGORY_RESOURCE_MANAGEMENT = 'RESOURCE_MANAGEMENT';
  /**
   * Security policy related issues.
   */
  public const CATEGORY_SECURITY_POLICY = 'SECURITY_POLICY';
  /**
   * Data and discovery related issues.
   */
  public const CATEGORY_DATA_DISCOVERY = 'DATA_DISCOVERY';
  protected $collection_key = 'dataLocations';
  /**
   * Output only. The relative resource name of the asset, of the form: projects
   * /{project_number}/locations/{location_id}/lakes/{lake_id}/zones/{zone_id}/a
   * ssets/{asset_id}.
   *
   * @var string
   */
  public $asset;
  /**
   * The category of issue associated with the action.
   *
   * @var string
   */
  public $category;
  /**
   * The list of data locations associated with this action. Cloud Storage
   * locations are represented as URI paths(E.g.
   * gs://bucket/table1/year=2020/month=Jan/). BigQuery locations refer to
   * resource names(E.g. bigquery.googleapis.com/projects/project-
   * id/datasets/dataset-id).
   *
   * @var string[]
   */
  public $dataLocations;
  /**
   * The time that the issue was detected.
   *
   * @var string
   */
  public $detectTime;
  protected $failedSecurityPolicyApplyType = GoogleCloudDataplexV1ActionFailedSecurityPolicyApply::class;
  protected $failedSecurityPolicyApplyDataType = '';
  protected $incompatibleDataSchemaType = GoogleCloudDataplexV1ActionIncompatibleDataSchema::class;
  protected $incompatibleDataSchemaDataType = '';
  protected $invalidDataFormatType = GoogleCloudDataplexV1ActionInvalidDataFormat::class;
  protected $invalidDataFormatDataType = '';
  protected $invalidDataOrganizationType = GoogleCloudDataplexV1ActionInvalidDataOrganization::class;
  protected $invalidDataOrganizationDataType = '';
  protected $invalidDataPartitionType = GoogleCloudDataplexV1ActionInvalidDataPartition::class;
  protected $invalidDataPartitionDataType = '';
  /**
   * Detailed description of the issue requiring action.
   *
   * @var string
   */
  public $issue;
  /**
   * Output only. The relative resource name of the lake, of the form:
   * projects/{project_number}/locations/{location_id}/lakes/{lake_id}.
   *
   * @var string
   */
  public $lake;
  protected $missingDataType = GoogleCloudDataplexV1ActionMissingData::class;
  protected $missingDataDataType = '';
  protected $missingResourceType = GoogleCloudDataplexV1ActionMissingResource::class;
  protected $missingResourceDataType = '';
  /**
   * Output only. The relative resource name of the action, of the form:
   * projects/{project}/locations/{location}/lakes/{lake}/actions/{action} proje
   * cts/{project}/locations/{location}/lakes/{lake}/zones/{zone}/actions/{actio
   * n} projects/{project}/locations/{location}/lakes/{lake}/zones/{zone}/assets
   * /{asset}/actions/{action}.
   *
   * @var string
   */
  public $name;
  protected $unauthorizedResourceType = GoogleCloudDataplexV1ActionUnauthorizedResource::class;
  protected $unauthorizedResourceDataType = '';
  /**
   * Output only. The relative resource name of the zone, of the form: projects/
   * {project_number}/locations/{location_id}/lakes/{lake_id}/zones/{zone_id}.
   *
   * @var string
   */
  public $zone;

  /**
   * Output only. The relative resource name of the asset, of the form: projects
   * /{project_number}/locations/{location_id}/lakes/{lake_id}/zones/{zone_id}/a
   * ssets/{asset_id}.
   *
   * @param string $asset
   */
  public function setAsset($asset)
  {
    $this->asset = $asset;
  }
  /**
   * @return string
   */
  public function getAsset()
  {
    return $this->asset;
  }
  /**
   * The category of issue associated with the action.
   *
   * Accepted values: CATEGORY_UNSPECIFIED, RESOURCE_MANAGEMENT,
   * SECURITY_POLICY, DATA_DISCOVERY
   *
   * @param self::CATEGORY_* $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return self::CATEGORY_*
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * The list of data locations associated with this action. Cloud Storage
   * locations are represented as URI paths(E.g.
   * gs://bucket/table1/year=2020/month=Jan/). BigQuery locations refer to
   * resource names(E.g. bigquery.googleapis.com/projects/project-
   * id/datasets/dataset-id).
   *
   * @param string[] $dataLocations
   */
  public function setDataLocations($dataLocations)
  {
    $this->dataLocations = $dataLocations;
  }
  /**
   * @return string[]
   */
  public function getDataLocations()
  {
    return $this->dataLocations;
  }
  /**
   * The time that the issue was detected.
   *
   * @param string $detectTime
   */
  public function setDetectTime($detectTime)
  {
    $this->detectTime = $detectTime;
  }
  /**
   * @return string
   */
  public function getDetectTime()
  {
    return $this->detectTime;
  }
  /**
   * Details for issues related to applying security policy.
   *
   * @param GoogleCloudDataplexV1ActionFailedSecurityPolicyApply $failedSecurityPolicyApply
   */
  public function setFailedSecurityPolicyApply(GoogleCloudDataplexV1ActionFailedSecurityPolicyApply $failedSecurityPolicyApply)
  {
    $this->failedSecurityPolicyApply = $failedSecurityPolicyApply;
  }
  /**
   * @return GoogleCloudDataplexV1ActionFailedSecurityPolicyApply
   */
  public function getFailedSecurityPolicyApply()
  {
    return $this->failedSecurityPolicyApply;
  }
  /**
   * Details for issues related to incompatible schemas detected within data.
   *
   * @param GoogleCloudDataplexV1ActionIncompatibleDataSchema $incompatibleDataSchema
   */
  public function setIncompatibleDataSchema(GoogleCloudDataplexV1ActionIncompatibleDataSchema $incompatibleDataSchema)
  {
    $this->incompatibleDataSchema = $incompatibleDataSchema;
  }
  /**
   * @return GoogleCloudDataplexV1ActionIncompatibleDataSchema
   */
  public function getIncompatibleDataSchema()
  {
    return $this->incompatibleDataSchema;
  }
  /**
   * Details for issues related to invalid or unsupported data formats.
   *
   * @param GoogleCloudDataplexV1ActionInvalidDataFormat $invalidDataFormat
   */
  public function setInvalidDataFormat(GoogleCloudDataplexV1ActionInvalidDataFormat $invalidDataFormat)
  {
    $this->invalidDataFormat = $invalidDataFormat;
  }
  /**
   * @return GoogleCloudDataplexV1ActionInvalidDataFormat
   */
  public function getInvalidDataFormat()
  {
    return $this->invalidDataFormat;
  }
  /**
   * Details for issues related to invalid data arrangement.
   *
   * @param GoogleCloudDataplexV1ActionInvalidDataOrganization $invalidDataOrganization
   */
  public function setInvalidDataOrganization(GoogleCloudDataplexV1ActionInvalidDataOrganization $invalidDataOrganization)
  {
    $this->invalidDataOrganization = $invalidDataOrganization;
  }
  /**
   * @return GoogleCloudDataplexV1ActionInvalidDataOrganization
   */
  public function getInvalidDataOrganization()
  {
    return $this->invalidDataOrganization;
  }
  /**
   * Details for issues related to invalid or unsupported data partition
   * structure.
   *
   * @param GoogleCloudDataplexV1ActionInvalidDataPartition $invalidDataPartition
   */
  public function setInvalidDataPartition(GoogleCloudDataplexV1ActionInvalidDataPartition $invalidDataPartition)
  {
    $this->invalidDataPartition = $invalidDataPartition;
  }
  /**
   * @return GoogleCloudDataplexV1ActionInvalidDataPartition
   */
  public function getInvalidDataPartition()
  {
    return $this->invalidDataPartition;
  }
  /**
   * Detailed description of the issue requiring action.
   *
   * @param string $issue
   */
  public function setIssue($issue)
  {
    $this->issue = $issue;
  }
  /**
   * @return string
   */
  public function getIssue()
  {
    return $this->issue;
  }
  /**
   * Output only. The relative resource name of the lake, of the form:
   * projects/{project_number}/locations/{location_id}/lakes/{lake_id}.
   *
   * @param string $lake
   */
  public function setLake($lake)
  {
    $this->lake = $lake;
  }
  /**
   * @return string
   */
  public function getLake()
  {
    return $this->lake;
  }
  /**
   * Details for issues related to absence of data within managed resources.
   *
   * @param GoogleCloudDataplexV1ActionMissingData $missingData
   */
  public function setMissingData(GoogleCloudDataplexV1ActionMissingData $missingData)
  {
    $this->missingData = $missingData;
  }
  /**
   * @return GoogleCloudDataplexV1ActionMissingData
   */
  public function getMissingData()
  {
    return $this->missingData;
  }
  /**
   * Details for issues related to absence of a managed resource.
   *
   * @param GoogleCloudDataplexV1ActionMissingResource $missingResource
   */
  public function setMissingResource(GoogleCloudDataplexV1ActionMissingResource $missingResource)
  {
    $this->missingResource = $missingResource;
  }
  /**
   * @return GoogleCloudDataplexV1ActionMissingResource
   */
  public function getMissingResource()
  {
    return $this->missingResource;
  }
  /**
   * Output only. The relative resource name of the action, of the form:
   * projects/{project}/locations/{location}/lakes/{lake}/actions/{action} proje
   * cts/{project}/locations/{location}/lakes/{lake}/zones/{zone}/actions/{actio
   * n} projects/{project}/locations/{location}/lakes/{lake}/zones/{zone}/assets
   * /{asset}/actions/{action}.
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
   * Details for issues related to lack of permissions to access data resources.
   *
   * @param GoogleCloudDataplexV1ActionUnauthorizedResource $unauthorizedResource
   */
  public function setUnauthorizedResource(GoogleCloudDataplexV1ActionUnauthorizedResource $unauthorizedResource)
  {
    $this->unauthorizedResource = $unauthorizedResource;
  }
  /**
   * @return GoogleCloudDataplexV1ActionUnauthorizedResource
   */
  public function getUnauthorizedResource()
  {
    return $this->unauthorizedResource;
  }
  /**
   * Output only. The relative resource name of the zone, of the form: projects/
   * {project_number}/locations/{location_id}/lakes/{lake_id}/zones/{zone_id}.
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1Action::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1Action');
