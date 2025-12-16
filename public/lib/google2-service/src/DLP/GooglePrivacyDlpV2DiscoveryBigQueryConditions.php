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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2DiscoveryBigQueryConditions extends \Google\Model
{
  /**
   * Unused.
   */
  public const TYPE_COLLECTION_BIG_QUERY_COLLECTION_UNSPECIFIED = 'BIG_QUERY_COLLECTION_UNSPECIFIED';
  /**
   * Automatically generate profiles for all tables, even if the table type is
   * not yet fully supported for analysis. Profiles for unsupported tables will
   * be generated with errors to indicate their partial support. When full
   * support is added, the tables will automatically be profiled during the next
   * scheduled run.
   */
  public const TYPE_COLLECTION_BIG_QUERY_COLLECTION_ALL_TYPES = 'BIG_QUERY_COLLECTION_ALL_TYPES';
  /**
   * Only those types fully supported will be profiled. Will expand
   * automatically as Cloud DLP adds support for new table types. Unsupported
   * table types will not have partial profiles generated.
   */
  public const TYPE_COLLECTION_BIG_QUERY_COLLECTION_ONLY_SUPPORTED_TYPES = 'BIG_QUERY_COLLECTION_ONLY_SUPPORTED_TYPES';
  /**
   * BigQuery table must have been created after this date. Used to avoid
   * backfilling.
   *
   * @var string
   */
  public $createdAfter;
  protected $orConditionsType = GooglePrivacyDlpV2OrConditions::class;
  protected $orConditionsDataType = '';
  /**
   * Restrict discovery to categories of table types.
   *
   * @var string
   */
  public $typeCollection;
  protected $typesType = GooglePrivacyDlpV2BigQueryTableTypes::class;
  protected $typesDataType = '';

  /**
   * BigQuery table must have been created after this date. Used to avoid
   * backfilling.
   *
   * @param string $createdAfter
   */
  public function setCreatedAfter($createdAfter)
  {
    $this->createdAfter = $createdAfter;
  }
  /**
   * @return string
   */
  public function getCreatedAfter()
  {
    return $this->createdAfter;
  }
  /**
   * At least one of the conditions must be true for a table to be scanned.
   *
   * @param GooglePrivacyDlpV2OrConditions $orConditions
   */
  public function setOrConditions(GooglePrivacyDlpV2OrConditions $orConditions)
  {
    $this->orConditions = $orConditions;
  }
  /**
   * @return GooglePrivacyDlpV2OrConditions
   */
  public function getOrConditions()
  {
    return $this->orConditions;
  }
  /**
   * Restrict discovery to categories of table types.
   *
   * Accepted values: BIG_QUERY_COLLECTION_UNSPECIFIED,
   * BIG_QUERY_COLLECTION_ALL_TYPES, BIG_QUERY_COLLECTION_ONLY_SUPPORTED_TYPES
   *
   * @param self::TYPE_COLLECTION_* $typeCollection
   */
  public function setTypeCollection($typeCollection)
  {
    $this->typeCollection = $typeCollection;
  }
  /**
   * @return self::TYPE_COLLECTION_*
   */
  public function getTypeCollection()
  {
    return $this->typeCollection;
  }
  /**
   * Restrict discovery to specific table types.
   *
   * @param GooglePrivacyDlpV2BigQueryTableTypes $types
   */
  public function setTypes(GooglePrivacyDlpV2BigQueryTableTypes $types)
  {
    $this->types = $types;
  }
  /**
   * @return GooglePrivacyDlpV2BigQueryTableTypes
   */
  public function getTypes()
  {
    return $this->types;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DiscoveryBigQueryConditions::class, 'Google_Service_DLP_GooglePrivacyDlpV2DiscoveryBigQueryConditions');
