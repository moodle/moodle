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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1betaServingConfig extends \Google\Collection
{
  protected $collection_key = 'synonymsControlIds';
  /**
   * @var string[]
   */
  public $boostControlIds;
  /**
   * @var string
   */
  public $createTime;
  /**
   * @var string
   */
  public $displayName;
  /**
   * @var string[]
   */
  public $dissociateControlIds;
  /**
   * @var string
   */
  public $diversityLevel;
  protected $embeddingConfigType = GoogleCloudDiscoveryengineV1betaEmbeddingConfig::class;
  protected $embeddingConfigDataType = '';
  /**
   * @var string[]
   */
  public $filterControlIds;
  protected $genericConfigType = GoogleCloudDiscoveryengineV1betaServingConfigGenericConfig::class;
  protected $genericConfigDataType = '';
  /**
   * @var string[]
   */
  public $ignoreControlIds;
  protected $mediaConfigType = GoogleCloudDiscoveryengineV1betaServingConfigMediaConfig::class;
  protected $mediaConfigDataType = '';
  /**
   * @var string
   */
  public $modelId;
  /**
   * @var string
   */
  public $name;
  /**
   * @var string[]
   */
  public $onewaySynonymsControlIds;
  /**
   * @var string
   */
  public $rankingExpression;
  /**
   * @var string[]
   */
  public $redirectControlIds;
  /**
   * @var string[]
   */
  public $replacementControlIds;
  /**
   * @var string
   */
  public $solutionType;
  /**
   * @var string[]
   */
  public $synonymsControlIds;
  /**
   * @var string
   */
  public $updateTime;

  /**
   * @param string[]
   */
  public function setBoostControlIds($boostControlIds)
  {
    $this->boostControlIds = $boostControlIds;
  }
  /**
   * @return string[]
   */
  public function getBoostControlIds()
  {
    return $this->boostControlIds;
  }
  /**
   * @param string
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
   * @param string
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
   * @param string[]
   */
  public function setDissociateControlIds($dissociateControlIds)
  {
    $this->dissociateControlIds = $dissociateControlIds;
  }
  /**
   * @return string[]
   */
  public function getDissociateControlIds()
  {
    return $this->dissociateControlIds;
  }
  /**
   * @param string
   */
  public function setDiversityLevel($diversityLevel)
  {
    $this->diversityLevel = $diversityLevel;
  }
  /**
   * @return string
   */
  public function getDiversityLevel()
  {
    return $this->diversityLevel;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaEmbeddingConfig
   */
  public function setEmbeddingConfig(GoogleCloudDiscoveryengineV1betaEmbeddingConfig $embeddingConfig)
  {
    $this->embeddingConfig = $embeddingConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaEmbeddingConfig
   */
  public function getEmbeddingConfig()
  {
    return $this->embeddingConfig;
  }
  /**
   * @param string[]
   */
  public function setFilterControlIds($filterControlIds)
  {
    $this->filterControlIds = $filterControlIds;
  }
  /**
   * @return string[]
   */
  public function getFilterControlIds()
  {
    return $this->filterControlIds;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaServingConfigGenericConfig
   */
  public function setGenericConfig(GoogleCloudDiscoveryengineV1betaServingConfigGenericConfig $genericConfig)
  {
    $this->genericConfig = $genericConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaServingConfigGenericConfig
   */
  public function getGenericConfig()
  {
    return $this->genericConfig;
  }
  /**
   * @param string[]
   */
  public function setIgnoreControlIds($ignoreControlIds)
  {
    $this->ignoreControlIds = $ignoreControlIds;
  }
  /**
   * @return string[]
   */
  public function getIgnoreControlIds()
  {
    return $this->ignoreControlIds;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaServingConfigMediaConfig
   */
  public function setMediaConfig(GoogleCloudDiscoveryengineV1betaServingConfigMediaConfig $mediaConfig)
  {
    $this->mediaConfig = $mediaConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaServingConfigMediaConfig
   */
  public function getMediaConfig()
  {
    return $this->mediaConfig;
  }
  /**
   * @param string
   */
  public function setModelId($modelId)
  {
    $this->modelId = $modelId;
  }
  /**
   * @return string
   */
  public function getModelId()
  {
    return $this->modelId;
  }
  /**
   * @param string
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
   * @param string[]
   */
  public function setOnewaySynonymsControlIds($onewaySynonymsControlIds)
  {
    $this->onewaySynonymsControlIds = $onewaySynonymsControlIds;
  }
  /**
   * @return string[]
   */
  public function getOnewaySynonymsControlIds()
  {
    return $this->onewaySynonymsControlIds;
  }
  /**
   * @param string
   */
  public function setRankingExpression($rankingExpression)
  {
    $this->rankingExpression = $rankingExpression;
  }
  /**
   * @return string
   */
  public function getRankingExpression()
  {
    return $this->rankingExpression;
  }
  /**
   * @param string[]
   */
  public function setRedirectControlIds($redirectControlIds)
  {
    $this->redirectControlIds = $redirectControlIds;
  }
  /**
   * @return string[]
   */
  public function getRedirectControlIds()
  {
    return $this->redirectControlIds;
  }
  /**
   * @param string[]
   */
  public function setReplacementControlIds($replacementControlIds)
  {
    $this->replacementControlIds = $replacementControlIds;
  }
  /**
   * @return string[]
   */
  public function getReplacementControlIds()
  {
    return $this->replacementControlIds;
  }
  /**
   * @param string
   */
  public function setSolutionType($solutionType)
  {
    $this->solutionType = $solutionType;
  }
  /**
   * @return string
   */
  public function getSolutionType()
  {
    return $this->solutionType;
  }
  /**
   * @param string[]
   */
  public function setSynonymsControlIds($synonymsControlIds)
  {
    $this->synonymsControlIds = $synonymsControlIds;
  }
  /**
   * @return string[]
   */
  public function getSynonymsControlIds()
  {
    return $this->synonymsControlIds;
  }
  /**
   * @param string
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
class_alias(GoogleCloudDiscoveryengineV1betaServingConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaServingConfig');
