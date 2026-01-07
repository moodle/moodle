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

class ViewDefinition extends \Google\Collection
{
  protected $collection_key = 'userDefinedFunctionResources';
  protected $foreignDefinitionsType = ForeignViewDefinition::class;
  protected $foreignDefinitionsDataType = 'array';
  protected $privacyPolicyType = PrivacyPolicy::class;
  protected $privacyPolicyDataType = '';
  /**
   * Required. A query that BigQuery executes when the view is referenced.
   *
   * @var string
   */
  public $query;
  /**
   * True if the column names are explicitly specified. For example by using the
   * 'CREATE VIEW v(c1, c2) AS ...' syntax. Can only be set for GoogleSQL views.
   *
   * @var bool
   */
  public $useExplicitColumnNames;
  /**
   * Specifies whether to use BigQuery's legacy SQL for this view. The default
   * value is true. If set to false, the view will use BigQuery's GoogleSQL:
   * https://cloud.google.com/bigquery/sql-reference/ Queries and views that
   * reference this view must use the same flag value. A wrapper is used here
   * because the default value is True.
   *
   * @var bool
   */
  public $useLegacySql;
  protected $userDefinedFunctionResourcesType = UserDefinedFunctionResource::class;
  protected $userDefinedFunctionResourcesDataType = 'array';

  /**
   * Optional. Foreign view representations.
   *
   * @param ForeignViewDefinition[] $foreignDefinitions
   */
  public function setForeignDefinitions($foreignDefinitions)
  {
    $this->foreignDefinitions = $foreignDefinitions;
  }
  /**
   * @return ForeignViewDefinition[]
   */
  public function getForeignDefinitions()
  {
    return $this->foreignDefinitions;
  }
  /**
   * Optional. Specifies the privacy policy for the view.
   *
   * @param PrivacyPolicy $privacyPolicy
   */
  public function setPrivacyPolicy(PrivacyPolicy $privacyPolicy)
  {
    $this->privacyPolicy = $privacyPolicy;
  }
  /**
   * @return PrivacyPolicy
   */
  public function getPrivacyPolicy()
  {
    return $this->privacyPolicy;
  }
  /**
   * Required. A query that BigQuery executes when the view is referenced.
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * True if the column names are explicitly specified. For example by using the
   * 'CREATE VIEW v(c1, c2) AS ...' syntax. Can only be set for GoogleSQL views.
   *
   * @param bool $useExplicitColumnNames
   */
  public function setUseExplicitColumnNames($useExplicitColumnNames)
  {
    $this->useExplicitColumnNames = $useExplicitColumnNames;
  }
  /**
   * @return bool
   */
  public function getUseExplicitColumnNames()
  {
    return $this->useExplicitColumnNames;
  }
  /**
   * Specifies whether to use BigQuery's legacy SQL for this view. The default
   * value is true. If set to false, the view will use BigQuery's GoogleSQL:
   * https://cloud.google.com/bigquery/sql-reference/ Queries and views that
   * reference this view must use the same flag value. A wrapper is used here
   * because the default value is True.
   *
   * @param bool $useLegacySql
   */
  public function setUseLegacySql($useLegacySql)
  {
    $this->useLegacySql = $useLegacySql;
  }
  /**
   * @return bool
   */
  public function getUseLegacySql()
  {
    return $this->useLegacySql;
  }
  /**
   * Describes user-defined function resources used in the query.
   *
   * @param UserDefinedFunctionResource[] $userDefinedFunctionResources
   */
  public function setUserDefinedFunctionResources($userDefinedFunctionResources)
  {
    $this->userDefinedFunctionResources = $userDefinedFunctionResources;
  }
  /**
   * @return UserDefinedFunctionResource[]
   */
  public function getUserDefinedFunctionResources()
  {
    return $this->userDefinedFunctionResources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ViewDefinition::class, 'Google_Service_Bigquery_ViewDefinition');
