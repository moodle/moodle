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

namespace Google\Service\CloudSearch;

class SearchApplication extends \Google\Collection
{
  protected $collection_key = 'sourceConfig';
  protected $dataSourceRestrictionsType = DataSourceRestriction::class;
  protected $dataSourceRestrictionsDataType = 'array';
  protected $defaultFacetOptionsType = FacetOptions::class;
  protected $defaultFacetOptionsDataType = 'array';
  protected $defaultSortOptionsType = SortOptions::class;
  protected $defaultSortOptionsDataType = '';
  /**
   * Display name of the Search Application. The maximum length is 300
   * characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * Indicates whether audit logging is on/off for requests made for the search
   * application in query APIs.
   *
   * @var bool
   */
  public $enableAuditLog;
  /**
   * The name of the Search Application. Format:
   * searchapplications/{application_id}.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. IDs of the Long Running Operations (LROs) currently running
   * for this schema. Output only field.
   *
   * @var string[]
   */
  public $operationIds;
  protected $queryInterpretationConfigType = QueryInterpretationConfig::class;
  protected $queryInterpretationConfigDataType = '';
  /**
   * With each result we should return the URI for its thumbnail (when
   * applicable)
   *
   * @var bool
   */
  public $returnResultThumbnailUrls;
  protected $scoringConfigType = ScoringConfig::class;
  protected $scoringConfigDataType = '';
  protected $sourceConfigType = SourceConfig::class;
  protected $sourceConfigDataType = 'array';

  /**
   * Retrictions applied to the configurations. The maximum number of elements
   * is 10.
   *
   * @param DataSourceRestriction[] $dataSourceRestrictions
   */
  public function setDataSourceRestrictions($dataSourceRestrictions)
  {
    $this->dataSourceRestrictions = $dataSourceRestrictions;
  }
  /**
   * @return DataSourceRestriction[]
   */
  public function getDataSourceRestrictions()
  {
    return $this->dataSourceRestrictions;
  }
  /**
   * The default fields for returning facet results. The sources specified here
   * also have been included in data_source_restrictions above.
   *
   * @param FacetOptions[] $defaultFacetOptions
   */
  public function setDefaultFacetOptions($defaultFacetOptions)
  {
    $this->defaultFacetOptions = $defaultFacetOptions;
  }
  /**
   * @return FacetOptions[]
   */
  public function getDefaultFacetOptions()
  {
    return $this->defaultFacetOptions;
  }
  /**
   * The default options for sorting the search results
   *
   * @param SortOptions $defaultSortOptions
   */
  public function setDefaultSortOptions(SortOptions $defaultSortOptions)
  {
    $this->defaultSortOptions = $defaultSortOptions;
  }
  /**
   * @return SortOptions
   */
  public function getDefaultSortOptions()
  {
    return $this->defaultSortOptions;
  }
  /**
   * Display name of the Search Application. The maximum length is 300
   * characters.
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
   * Indicates whether audit logging is on/off for requests made for the search
   * application in query APIs.
   *
   * @param bool $enableAuditLog
   */
  public function setEnableAuditLog($enableAuditLog)
  {
    $this->enableAuditLog = $enableAuditLog;
  }
  /**
   * @return bool
   */
  public function getEnableAuditLog()
  {
    return $this->enableAuditLog;
  }
  /**
   * The name of the Search Application. Format:
   * searchapplications/{application_id}.
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
   * Output only. IDs of the Long Running Operations (LROs) currently running
   * for this schema. Output only field.
   *
   * @param string[] $operationIds
   */
  public function setOperationIds($operationIds)
  {
    $this->operationIds = $operationIds;
  }
  /**
   * @return string[]
   */
  public function getOperationIds()
  {
    return $this->operationIds;
  }
  /**
   * The default options for query interpretation
   *
   * @param QueryInterpretationConfig $queryInterpretationConfig
   */
  public function setQueryInterpretationConfig(QueryInterpretationConfig $queryInterpretationConfig)
  {
    $this->queryInterpretationConfig = $queryInterpretationConfig;
  }
  /**
   * @return QueryInterpretationConfig
   */
  public function getQueryInterpretationConfig()
  {
    return $this->queryInterpretationConfig;
  }
  /**
   * With each result we should return the URI for its thumbnail (when
   * applicable)
   *
   * @param bool $returnResultThumbnailUrls
   */
  public function setReturnResultThumbnailUrls($returnResultThumbnailUrls)
  {
    $this->returnResultThumbnailUrls = $returnResultThumbnailUrls;
  }
  /**
   * @return bool
   */
  public function getReturnResultThumbnailUrls()
  {
    return $this->returnResultThumbnailUrls;
  }
  /**
   * Configuration for ranking results.
   *
   * @param ScoringConfig $scoringConfig
   */
  public function setScoringConfig(ScoringConfig $scoringConfig)
  {
    $this->scoringConfig = $scoringConfig;
  }
  /**
   * @return ScoringConfig
   */
  public function getScoringConfig()
  {
    return $this->scoringConfig;
  }
  /**
   * Configuration for a sources specified in data_source_restrictions.
   *
   * @param SourceConfig[] $sourceConfig
   */
  public function setSourceConfig($sourceConfig)
  {
    $this->sourceConfig = $sourceConfig;
  }
  /**
   * @return SourceConfig[]
   */
  public function getSourceConfig()
  {
    return $this->sourceConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SearchApplication::class, 'Google_Service_CloudSearch_SearchApplication');
