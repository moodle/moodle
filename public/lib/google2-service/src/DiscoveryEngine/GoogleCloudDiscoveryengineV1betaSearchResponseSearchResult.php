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

class GoogleCloudDiscoveryengineV1betaSearchResponseSearchResult extends \Google\Model
{
  protected $documentType = GoogleCloudDiscoveryengineV1betaDocument::class;
  protected $documentDataType = '';
  /**
   * @var string
   */
  public $id;
  protected $modelScoresType = GoogleCloudDiscoveryengineV1betaDoubleList::class;
  protected $modelScoresDataType = 'map';

  /**
   * @param GoogleCloudDiscoveryengineV1betaDocument
   */
  public function setDocument(GoogleCloudDiscoveryengineV1betaDocument $document)
  {
    $this->document = $document;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaDocument
   */
  public function getDocument()
  {
    return $this->document;
  }
  /**
   * @param string
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaDoubleList[]
   */
  public function setModelScores($modelScores)
  {
    $this->modelScores = $modelScores;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaDoubleList[]
   */
  public function getModelScores()
  {
    return $this->modelScores;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaSearchResponseSearchResult::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaSearchResponseSearchResult');
