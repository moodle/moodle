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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1alpha1RuntimeAnnotationUserInput extends \Google\Model
{
  /**
   * Unknown query source.
   */
  public const QUERY_SOURCE_QUERY_SOURCE_UNSPECIFIED = 'QUERY_SOURCE_UNSPECIFIED';
  /**
   * The query is from agents.
   */
  public const QUERY_SOURCE_AGENT_QUERY = 'AGENT_QUERY';
  /**
   * The query is a query from previous suggestions, e.g. from a preceding
   * SuggestKnowledgeAssist response.
   */
  public const QUERY_SOURCE_SUGGESTED_QUERY = 'SUGGESTED_QUERY';
  /**
   * The resource name of associated generator. Format:
   * `projects//locations//generators/`
   *
   * @var string
   */
  public $generatorName;
  /**
   * Query text. Article Search uses this to store the input query used to
   * generate the search results.
   *
   * @var string
   */
  public $query;
  /**
   * Query source for the answer.
   *
   * @var string
   */
  public $querySource;

  /**
   * The resource name of associated generator. Format:
   * `projects//locations//generators/`
   *
   * @param string $generatorName
   */
  public function setGeneratorName($generatorName)
  {
    $this->generatorName = $generatorName;
  }
  /**
   * @return string
   */
  public function getGeneratorName()
  {
    return $this->generatorName;
  }
  /**
   * Query text. Article Search uses this to store the input query used to
   * generate the search results.
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
   * Query source for the answer.
   *
   * Accepted values: QUERY_SOURCE_UNSPECIFIED, AGENT_QUERY, SUGGESTED_QUERY
   *
   * @param self::QUERY_SOURCE_* $querySource
   */
  public function setQuerySource($querySource)
  {
    $this->querySource = $querySource;
  }
  /**
   * @return self::QUERY_SOURCE_*
   */
  public function getQuerySource()
  {
    return $this->querySource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1alpha1RuntimeAnnotationUserInput::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1alpha1RuntimeAnnotationUserInput');
