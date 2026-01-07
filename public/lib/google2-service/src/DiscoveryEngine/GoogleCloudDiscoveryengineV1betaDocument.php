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

class GoogleCloudDiscoveryengineV1betaDocument extends \Google\Model
{
  protected $contentType = GoogleCloudDiscoveryengineV1betaDocumentContent::class;
  protected $contentDataType = '';
  /**
   * @var array[]
   */
  public $derivedStructData;
  /**
   * @var string
   */
  public $id;
  /**
   * @var string
   */
  public $indexTime;
  /**
   * @var string
   */
  public $jsonData;
  /**
   * @var string
   */
  public $name;
  /**
   * @var string
   */
  public $parentDocumentId;
  /**
   * @var string
   */
  public $schemaId;
  /**
   * @var array[]
   */
  public $structData;

  /**
   * @param GoogleCloudDiscoveryengineV1betaDocumentContent
   */
  public function setContent(GoogleCloudDiscoveryengineV1betaDocumentContent $content)
  {
    $this->content = $content;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaDocumentContent
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * @param array[]
   */
  public function setDerivedStructData($derivedStructData)
  {
    $this->derivedStructData = $derivedStructData;
  }
  /**
   * @return array[]
   */
  public function getDerivedStructData()
  {
    return $this->derivedStructData;
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
   * @param string
   */
  public function setIndexTime($indexTime)
  {
    $this->indexTime = $indexTime;
  }
  /**
   * @return string
   */
  public function getIndexTime()
  {
    return $this->indexTime;
  }
  /**
   * @param string
   */
  public function setJsonData($jsonData)
  {
    $this->jsonData = $jsonData;
  }
  /**
   * @return string
   */
  public function getJsonData()
  {
    return $this->jsonData;
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
   * @param string
   */
  public function setParentDocumentId($parentDocumentId)
  {
    $this->parentDocumentId = $parentDocumentId;
  }
  /**
   * @return string
   */
  public function getParentDocumentId()
  {
    return $this->parentDocumentId;
  }
  /**
   * @param string
   */
  public function setSchemaId($schemaId)
  {
    $this->schemaId = $schemaId;
  }
  /**
   * @return string
   */
  public function getSchemaId()
  {
    return $this->schemaId;
  }
  /**
   * @param array[]
   */
  public function setStructData($structData)
  {
    $this->structData = $structData;
  }
  /**
   * @return array[]
   */
  public function getStructData()
  {
    return $this->structData;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaDocument::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaDocument');
