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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1FeatureGroup extends \Google\Model
{
  /**
   * By default, the project-level Vertex AI Service Agent is enabled.
   */
  public const SERVICE_AGENT_TYPE_SERVICE_AGENT_TYPE_UNSPECIFIED = 'SERVICE_AGENT_TYPE_UNSPECIFIED';
  /**
   * Specifies the project-level Vertex AI Service Agent
   * (https://cloud.google.com/vertex-ai/docs/general/access-control#service-
   * agents).
   */
  public const SERVICE_AGENT_TYPE_SERVICE_AGENT_TYPE_PROJECT = 'SERVICE_AGENT_TYPE_PROJECT';
  /**
   * Enable a FeatureGroup service account to be created by Vertex AI and output
   * in the field `service_account_email`. This service account will be used to
   * read from the source BigQuery table during jobs under a FeatureGroup.
   */
  public const SERVICE_AGENT_TYPE_SERVICE_AGENT_TYPE_FEATURE_GROUP = 'SERVICE_AGENT_TYPE_FEATURE_GROUP';
  protected $bigQueryType = GoogleCloudAiplatformV1FeatureGroupBigQuery::class;
  protected $bigQueryDataType = '';
  /**
   * Output only. Timestamp when this FeatureGroup was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Description of the FeatureGroup.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Used to perform consistent read-modify-write updates. If not set,
   * a blind "overwrite" update happens.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. The labels with user-defined metadata to organize your
   * FeatureGroup. Label keys and values can be no longer than 64 characters
   * (Unicode codepoints), can only contain lowercase letters, numeric
   * characters, underscores and dashes. International characters are allowed.
   * See https://goo.gl/xmQnxf for more information on and examples of labels.
   * No more than 64 user labels can be associated with one FeatureGroup(System
   * labels are excluded)." System reserved label keys are prefixed with
   * "aiplatform.googleapis.com/" and are immutable.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. Name of the FeatureGroup. Format:
   * `projects/{project}/locations/{location}/featureGroups/{featureGroup}`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. A Service Account unique to this FeatureGroup. The role
   * bigquery.dataViewer should be granted to this service account to allow
   * Vertex AI Feature Store to access source data while running jobs under this
   * FeatureGroup.
   *
   * @var string
   */
  public $serviceAccountEmail;
  /**
   * Optional. Service agent type used during jobs under a FeatureGroup. By
   * default, the Vertex AI Service Agent is used. When using an IAM Policy to
   * isolate this FeatureGroup within a project, a separate service account
   * should be provisioned by setting this field to
   * `SERVICE_AGENT_TYPE_FEATURE_GROUP`. This will generate a separate service
   * account to access the BigQuery source table.
   *
   * @var string
   */
  public $serviceAgentType;
  /**
   * Output only. Timestamp when this FeatureGroup was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Indicates that features for this group come from BigQuery Table/View. By
   * default treats the source as a sparse time series source. The BigQuery
   * source table or view must have at least one entity ID column and a column
   * named `feature_timestamp`.
   *
   * @param GoogleCloudAiplatformV1FeatureGroupBigQuery $bigQuery
   */
  public function setBigQuery(GoogleCloudAiplatformV1FeatureGroupBigQuery $bigQuery)
  {
    $this->bigQuery = $bigQuery;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureGroupBigQuery
   */
  public function getBigQuery()
  {
    return $this->bigQuery;
  }
  /**
   * Output only. Timestamp when this FeatureGroup was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. Description of the FeatureGroup.
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
   * Optional. Used to perform consistent read-modify-write updates. If not set,
   * a blind "overwrite" update happens.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. The labels with user-defined metadata to organize your
   * FeatureGroup. Label keys and values can be no longer than 64 characters
   * (Unicode codepoints), can only contain lowercase letters, numeric
   * characters, underscores and dashes. International characters are allowed.
   * See https://goo.gl/xmQnxf for more information on and examples of labels.
   * No more than 64 user labels can be associated with one FeatureGroup(System
   * labels are excluded)." System reserved label keys are prefixed with
   * "aiplatform.googleapis.com/" and are immutable.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Identifier. Name of the FeatureGroup. Format:
   * `projects/{project}/locations/{location}/featureGroups/{featureGroup}`
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
   * Output only. A Service Account unique to this FeatureGroup. The role
   * bigquery.dataViewer should be granted to this service account to allow
   * Vertex AI Feature Store to access source data while running jobs under this
   * FeatureGroup.
   *
   * @param string $serviceAccountEmail
   */
  public function setServiceAccountEmail($serviceAccountEmail)
  {
    $this->serviceAccountEmail = $serviceAccountEmail;
  }
  /**
   * @return string
   */
  public function getServiceAccountEmail()
  {
    return $this->serviceAccountEmail;
  }
  /**
   * Optional. Service agent type used during jobs under a FeatureGroup. By
   * default, the Vertex AI Service Agent is used. When using an IAM Policy to
   * isolate this FeatureGroup within a project, a separate service account
   * should be provisioned by setting this field to
   * `SERVICE_AGENT_TYPE_FEATURE_GROUP`. This will generate a separate service
   * account to access the BigQuery source table.
   *
   * Accepted values: SERVICE_AGENT_TYPE_UNSPECIFIED,
   * SERVICE_AGENT_TYPE_PROJECT, SERVICE_AGENT_TYPE_FEATURE_GROUP
   *
   * @param self::SERVICE_AGENT_TYPE_* $serviceAgentType
   */
  public function setServiceAgentType($serviceAgentType)
  {
    $this->serviceAgentType = $serviceAgentType;
  }
  /**
   * @return self::SERVICE_AGENT_TYPE_*
   */
  public function getServiceAgentType()
  {
    return $this->serviceAgentType;
  }
  /**
   * Output only. Timestamp when this FeatureGroup was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FeatureGroup::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FeatureGroup');
