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

class GoogleCloudAiplatformV1Feature extends \Google\Collection
{
  /**
   * The value type is unspecified.
   */
  public const VALUE_TYPE_VALUE_TYPE_UNSPECIFIED = 'VALUE_TYPE_UNSPECIFIED';
  /**
   * Used for Feature that is a boolean.
   */
  public const VALUE_TYPE_BOOL = 'BOOL';
  /**
   * Used for Feature that is a list of boolean.
   */
  public const VALUE_TYPE_BOOL_ARRAY = 'BOOL_ARRAY';
  /**
   * Used for Feature that is double.
   */
  public const VALUE_TYPE_DOUBLE = 'DOUBLE';
  /**
   * Used for Feature that is a list of double.
   */
  public const VALUE_TYPE_DOUBLE_ARRAY = 'DOUBLE_ARRAY';
  /**
   * Used for Feature that is INT64.
   */
  public const VALUE_TYPE_INT64 = 'INT64';
  /**
   * Used for Feature that is a list of INT64.
   */
  public const VALUE_TYPE_INT64_ARRAY = 'INT64_ARRAY';
  /**
   * Used for Feature that is string.
   */
  public const VALUE_TYPE_STRING = 'STRING';
  /**
   * Used for Feature that is a list of String.
   */
  public const VALUE_TYPE_STRING_ARRAY = 'STRING_ARRAY';
  /**
   * Used for Feature that is bytes.
   */
  public const VALUE_TYPE_BYTES = 'BYTES';
  /**
   * Used for Feature that is struct.
   */
  public const VALUE_TYPE_STRUCT = 'STRUCT';
  protected $collection_key = 'monitoringStatsAnomalies';
  /**
   * Output only. Only applicable for Vertex AI Feature Store (Legacy).
   * Timestamp when this EntityType was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Description of the Feature.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Only applicable for Vertex AI Feature Store (Legacy). If not set,
   * use the monitoring_config defined for the EntityType this Feature belongs
   * to. Only Features with type (Feature.ValueType) BOOL, STRING, DOUBLE or
   * INT64 can enable monitoring. If set to true, all types of data monitoring
   * are disabled despite the config on EntityType.
   *
   * @var bool
   */
  public $disableMonitoring;
  /**
   * Used to perform a consistent read-modify-write updates. If not set, a blind
   * "overwrite" update happens.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. The labels with user-defined metadata to organize your Features.
   * Label keys and values can be no longer than 64 characters (Unicode
   * codepoints), can only contain lowercase letters, numeric characters,
   * underscores and dashes. International characters are allowed. See
   * https://goo.gl/xmQnxf for more information on and examples of labels. No
   * more than 64 user labels can be associated with one Feature (System labels
   * are excluded)." System reserved label keys are prefixed with
   * "aiplatform.googleapis.com/" and are immutable.
   *
   * @var string[]
   */
  public $labels;
  protected $monitoringStatsAnomaliesType = GoogleCloudAiplatformV1FeatureMonitoringStatsAnomaly::class;
  protected $monitoringStatsAnomaliesDataType = 'array';
  /**
   * Immutable. Name of the Feature. Format: `projects/{project}/locations/{loca
   * tion}/featurestores/{featurestore}/entityTypes/{entity_type}/features/{feat
   * ure}` `projects/{project}/locations/{location}/featureGroups/{feature_group
   * }/features/{feature}` The last part feature is assigned by the client. The
   * feature can be up to 64 characters long and can consist only of ASCII Latin
   * letters A-Z and a-z, underscore(_), and ASCII digits 0-9 starting with a
   * letter. The value will be unique given an entity type.
   *
   * @var string
   */
  public $name;
  /**
   * Entity responsible for maintaining this feature. Can be comma separated
   * list of email addresses or URIs.
   *
   * @var string
   */
  public $pointOfContact;
  /**
   * Output only. Only applicable for Vertex AI Feature Store (Legacy).
   * Timestamp when this EntityType was most recently updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Immutable. Only applicable for Vertex AI Feature Store (Legacy). Type of
   * Feature value.
   *
   * @var string
   */
  public $valueType;
  /**
   * Only applicable for Vertex AI Feature Store. The name of the BigQuery
   * Table/View column hosting data for this version. If no value is provided,
   * will use feature_id.
   *
   * @var string
   */
  public $versionColumnName;

  /**
   * Output only. Only applicable for Vertex AI Feature Store (Legacy).
   * Timestamp when this EntityType was created.
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
   * Description of the Feature.
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
   * Optional. Only applicable for Vertex AI Feature Store (Legacy). If not set,
   * use the monitoring_config defined for the EntityType this Feature belongs
   * to. Only Features with type (Feature.ValueType) BOOL, STRING, DOUBLE or
   * INT64 can enable monitoring. If set to true, all types of data monitoring
   * are disabled despite the config on EntityType.
   *
   * @param bool $disableMonitoring
   */
  public function setDisableMonitoring($disableMonitoring)
  {
    $this->disableMonitoring = $disableMonitoring;
  }
  /**
   * @return bool
   */
  public function getDisableMonitoring()
  {
    return $this->disableMonitoring;
  }
  /**
   * Used to perform a consistent read-modify-write updates. If not set, a blind
   * "overwrite" update happens.
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
   * Optional. The labels with user-defined metadata to organize your Features.
   * Label keys and values can be no longer than 64 characters (Unicode
   * codepoints), can only contain lowercase letters, numeric characters,
   * underscores and dashes. International characters are allowed. See
   * https://goo.gl/xmQnxf for more information on and examples of labels. No
   * more than 64 user labels can be associated with one Feature (System labels
   * are excluded)." System reserved label keys are prefixed with
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
   * Output only. Only applicable for Vertex AI Feature Store (Legacy). The list
   * of historical stats and anomalies with specified objectives.
   *
   * @param GoogleCloudAiplatformV1FeatureMonitoringStatsAnomaly[] $monitoringStatsAnomalies
   */
  public function setMonitoringStatsAnomalies($monitoringStatsAnomalies)
  {
    $this->monitoringStatsAnomalies = $monitoringStatsAnomalies;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureMonitoringStatsAnomaly[]
   */
  public function getMonitoringStatsAnomalies()
  {
    return $this->monitoringStatsAnomalies;
  }
  /**
   * Immutable. Name of the Feature. Format: `projects/{project}/locations/{loca
   * tion}/featurestores/{featurestore}/entityTypes/{entity_type}/features/{feat
   * ure}` `projects/{project}/locations/{location}/featureGroups/{feature_group
   * }/features/{feature}` The last part feature is assigned by the client. The
   * feature can be up to 64 characters long and can consist only of ASCII Latin
   * letters A-Z and a-z, underscore(_), and ASCII digits 0-9 starting with a
   * letter. The value will be unique given an entity type.
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
   * Entity responsible for maintaining this feature. Can be comma separated
   * list of email addresses or URIs.
   *
   * @param string $pointOfContact
   */
  public function setPointOfContact($pointOfContact)
  {
    $this->pointOfContact = $pointOfContact;
  }
  /**
   * @return string
   */
  public function getPointOfContact()
  {
    return $this->pointOfContact;
  }
  /**
   * Output only. Only applicable for Vertex AI Feature Store (Legacy).
   * Timestamp when this EntityType was most recently updated.
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
  /**
   * Immutable. Only applicable for Vertex AI Feature Store (Legacy). Type of
   * Feature value.
   *
   * Accepted values: VALUE_TYPE_UNSPECIFIED, BOOL, BOOL_ARRAY, DOUBLE,
   * DOUBLE_ARRAY, INT64, INT64_ARRAY, STRING, STRING_ARRAY, BYTES, STRUCT
   *
   * @param self::VALUE_TYPE_* $valueType
   */
  public function setValueType($valueType)
  {
    $this->valueType = $valueType;
  }
  /**
   * @return self::VALUE_TYPE_*
   */
  public function getValueType()
  {
    return $this->valueType;
  }
  /**
   * Only applicable for Vertex AI Feature Store. The name of the BigQuery
   * Table/View column hosting data for this version. If no value is provided,
   * will use feature_id.
   *
   * @param string $versionColumnName
   */
  public function setVersionColumnName($versionColumnName)
  {
    $this->versionColumnName = $versionColumnName;
  }
  /**
   * @return string
   */
  public function getVersionColumnName()
  {
    return $this->versionColumnName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Feature::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Feature');
