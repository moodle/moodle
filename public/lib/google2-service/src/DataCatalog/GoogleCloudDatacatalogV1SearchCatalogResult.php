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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1SearchCatalogResult extends \Google\Model
{
  /**
   * Default unknown system.
   */
  public const INTEGRATED_SYSTEM_INTEGRATED_SYSTEM_UNSPECIFIED = 'INTEGRATED_SYSTEM_UNSPECIFIED';
  /**
   * BigQuery.
   */
  public const INTEGRATED_SYSTEM_BIGQUERY = 'BIGQUERY';
  /**
   * Cloud Pub/Sub.
   */
  public const INTEGRATED_SYSTEM_CLOUD_PUBSUB = 'CLOUD_PUBSUB';
  /**
   * Dataproc Metastore.
   */
  public const INTEGRATED_SYSTEM_DATAPROC_METASTORE = 'DATAPROC_METASTORE';
  /**
   * Dataplex Universal Catalog.
   */
  public const INTEGRATED_SYSTEM_DATAPLEX = 'DATAPLEX';
  /**
   * Cloud Spanner
   */
  public const INTEGRATED_SYSTEM_CLOUD_SPANNER = 'CLOUD_SPANNER';
  /**
   * Cloud Bigtable
   */
  public const INTEGRATED_SYSTEM_CLOUD_BIGTABLE = 'CLOUD_BIGTABLE';
  /**
   * Cloud Sql
   */
  public const INTEGRATED_SYSTEM_CLOUD_SQL = 'CLOUD_SQL';
  /**
   * Looker
   */
  public const INTEGRATED_SYSTEM_LOOKER = 'LOOKER';
  /**
   * Vertex AI
   */
  public const INTEGRATED_SYSTEM_VERTEX_AI = 'VERTEX_AI';
  /**
   * Default unknown type.
   */
  public const SEARCH_RESULT_TYPE_SEARCH_RESULT_TYPE_UNSPECIFIED = 'SEARCH_RESULT_TYPE_UNSPECIFIED';
  /**
   * An Entry.
   */
  public const SEARCH_RESULT_TYPE_ENTRY = 'ENTRY';
  /**
   * A TagTemplate.
   */
  public const SEARCH_RESULT_TYPE_TAG_TEMPLATE = 'TAG_TEMPLATE';
  /**
   * An EntryGroup.
   */
  public const SEARCH_RESULT_TYPE_ENTRY_GROUP = 'ENTRY_GROUP';
  /**
   * Entry description that can consist of several sentences or paragraphs that
   * describe entry contents.
   *
   * @var string
   */
  public $description;
  /**
   * The display name of the result.
   *
   * @var string
   */
  public $displayName;
  /**
   * Fully qualified name (FQN) of the resource. FQNs take two forms: * For non-
   * regionalized resources:
   * `{SYSTEM}:{PROJECT}.{PATH_TO_RESOURCE_SEPARATED_WITH_DOTS}` * For
   * regionalized resources:
   * `{SYSTEM}:{PROJECT}.{LOCATION_ID}.{PATH_TO_RESOURCE_SEPARATED_WITH_DOTS}`
   * Example for a DPMS table: `dataproc_metastore:PROJECT_ID.LOCATION_ID.INSTAN
   * CE_ID.DATABASE_ID.TABLE_ID`
   *
   * @var string
   */
  public $fullyQualifiedName;
  /**
   * Output only. The source system that Data Catalog automatically integrates
   * with, such as BigQuery, Cloud Pub/Sub, or Dataproc Metastore.
   *
   * @var string
   */
  public $integratedSystem;
  /**
   * The full name of the Google Cloud resource the entry belongs to. For more
   * information, see [Full Resource Name]
   * (/apis/design/resource_names#full_resource_name). Example: `//bigquery.goog
   * leapis.com/projects/PROJECT_ID/datasets/DATASET_ID/tables/TABLE_ID`
   *
   * @var string
   */
  public $linkedResource;
  /**
   * The last modification timestamp of the entry in the source system.
   *
   * @var string
   */
  public $modifyTime;
  /**
   * The relative name of the resource in URL format. Examples: * `projects/{PRO
   * JECT_ID}/locations/{LOCATION_ID}/entryGroups/{ENTRY_GROUP_ID}/entries/{ENTR
   * Y_ID}` * `projects/{PROJECT_ID}/tagTemplates/{TAG_TEMPLATE_ID}`
   *
   * @var string
   */
  public $relativeResourceName;
  /**
   * Sub-type of the search result. A dot-delimited full type of the resource.
   * The same type you specify in the `type` search predicate. Examples:
   * `entry.table`, `entry.dataStream`, `tagTemplate`.
   *
   * @var string
   */
  public $searchResultSubtype;
  /**
   * Type of the search result. You can use this field to determine which get
   * method to call to fetch the full resource.
   *
   * @var string
   */
  public $searchResultType;
  /**
   * Custom source system that you can manually integrate Data Catalog with.
   *
   * @var string
   */
  public $userSpecifiedSystem;

  /**
   * Entry description that can consist of several sentences or paragraphs that
   * describe entry contents.
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
   * The display name of the result.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Fully qualified name (FQN) of the resource. FQNs take two forms: * For non-
   * regionalized resources:
   * `{SYSTEM}:{PROJECT}.{PATH_TO_RESOURCE_SEPARATED_WITH_DOTS}` * For
   * regionalized resources:
   * `{SYSTEM}:{PROJECT}.{LOCATION_ID}.{PATH_TO_RESOURCE_SEPARATED_WITH_DOTS}`
   * Example for a DPMS table: `dataproc_metastore:PROJECT_ID.LOCATION_ID.INSTAN
   * CE_ID.DATABASE_ID.TABLE_ID`
   *
   * @param string $fullyQualifiedName
   */
  public function setFullyQualifiedName($fullyQualifiedName)
  {
    $this->fullyQualifiedName = $fullyQualifiedName;
  }
  /**
   * @return string
   */
  public function getFullyQualifiedName()
  {
    return $this->fullyQualifiedName;
  }
  /**
   * Output only. The source system that Data Catalog automatically integrates
   * with, such as BigQuery, Cloud Pub/Sub, or Dataproc Metastore.
   *
   * Accepted values: INTEGRATED_SYSTEM_UNSPECIFIED, BIGQUERY, CLOUD_PUBSUB,
   * DATAPROC_METASTORE, DATAPLEX, CLOUD_SPANNER, CLOUD_BIGTABLE, CLOUD_SQL,
   * LOOKER, VERTEX_AI
   *
   * @param self::INTEGRATED_SYSTEM_* $integratedSystem
   */
  public function setIntegratedSystem($integratedSystem)
  {
    $this->integratedSystem = $integratedSystem;
  }
  /**
   * @return self::INTEGRATED_SYSTEM_*
   */
  public function getIntegratedSystem()
  {
    return $this->integratedSystem;
  }
  /**
   * The full name of the Google Cloud resource the entry belongs to. For more
   * information, see [Full Resource Name]
   * (/apis/design/resource_names#full_resource_name). Example: `//bigquery.goog
   * leapis.com/projects/PROJECT_ID/datasets/DATASET_ID/tables/TABLE_ID`
   *
   * @param string $linkedResource
   */
  public function setLinkedResource($linkedResource)
  {
    $this->linkedResource = $linkedResource;
  }
  /**
   * @return string
   */
  public function getLinkedResource()
  {
    return $this->linkedResource;
  }
  /**
   * The last modification timestamp of the entry in the source system.
   *
   * @param string $modifyTime
   */
  public function setModifyTime($modifyTime)
  {
    $this->modifyTime = $modifyTime;
  }
  /**
   * @return string
   */
  public function getModifyTime()
  {
    return $this->modifyTime;
  }
  /**
   * The relative name of the resource in URL format. Examples: * `projects/{PRO
   * JECT_ID}/locations/{LOCATION_ID}/entryGroups/{ENTRY_GROUP_ID}/entries/{ENTR
   * Y_ID}` * `projects/{PROJECT_ID}/tagTemplates/{TAG_TEMPLATE_ID}`
   *
   * @param string $relativeResourceName
   */
  public function setRelativeResourceName($relativeResourceName)
  {
    $this->relativeResourceName = $relativeResourceName;
  }
  /**
   * @return string
   */
  public function getRelativeResourceName()
  {
    return $this->relativeResourceName;
  }
  /**
   * Sub-type of the search result. A dot-delimited full type of the resource.
   * The same type you specify in the `type` search predicate. Examples:
   * `entry.table`, `entry.dataStream`, `tagTemplate`.
   *
   * @param string $searchResultSubtype
   */
  public function setSearchResultSubtype($searchResultSubtype)
  {
    $this->searchResultSubtype = $searchResultSubtype;
  }
  /**
   * @return string
   */
  public function getSearchResultSubtype()
  {
    return $this->searchResultSubtype;
  }
  /**
   * Type of the search result. You can use this field to determine which get
   * method to call to fetch the full resource.
   *
   * Accepted values: SEARCH_RESULT_TYPE_UNSPECIFIED, ENTRY, TAG_TEMPLATE,
   * ENTRY_GROUP
   *
   * @param self::SEARCH_RESULT_TYPE_* $searchResultType
   */
  public function setSearchResultType($searchResultType)
  {
    $this->searchResultType = $searchResultType;
  }
  /**
   * @return self::SEARCH_RESULT_TYPE_*
   */
  public function getSearchResultType()
  {
    return $this->searchResultType;
  }
  /**
   * Custom source system that you can manually integrate Data Catalog with.
   *
   * @param string $userSpecifiedSystem
   */
  public function setUserSpecifiedSystem($userSpecifiedSystem)
  {
    $this->userSpecifiedSystem = $userSpecifiedSystem;
  }
  /**
   * @return string
   */
  public function getUserSpecifiedSystem()
  {
    return $this->userSpecifiedSystem;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1SearchCatalogResult::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1SearchCatalogResult');
