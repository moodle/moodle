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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1Content extends \Google\Model
{
  /**
   * Output only. Content creation time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. Content data in string format.
   *
   * @var string
   */
  public $dataText;
  /**
   * Optional. Description of the content.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. User defined labels for the content.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The relative resource name of the content, of the form: projec
   * ts/{project_id}/locations/{location_id}/lakes/{lake_id}/content/{content_id
   * }
   *
   * @var string
   */
  public $name;
  protected $notebookType = GoogleCloudDataplexV1ContentNotebook::class;
  protected $notebookDataType = '';
  /**
   * Required. The path for the Content file, represented as directory
   * structure. Unique within a lake. Limited to alphanumerics, hyphens,
   * underscores, dots and slashes.
   *
   * @var string
   */
  public $path;
  protected $sqlScriptType = GoogleCloudDataplexV1ContentSqlScript::class;
  protected $sqlScriptDataType = '';
  /**
   * Output only. System generated globally unique ID for the content. This ID
   * will be different if the content is deleted and re-created with the same
   * name.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time when the content was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Content creation time.
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
   * Required. Content data in string format.
   *
   * @param string $dataText
   */
  public function setDataText($dataText)
  {
    $this->dataText = $dataText;
  }
  /**
   * @return string
   */
  public function getDataText()
  {
    return $this->dataText;
  }
  /**
   * Optional. Description of the content.
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
   * Optional. User defined labels for the content.
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
   * Output only. The relative resource name of the content, of the form: projec
   * ts/{project_id}/locations/{location_id}/lakes/{lake_id}/content/{content_id
   * }
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
   * Notebook related configurations.
   *
   * @param GoogleCloudDataplexV1ContentNotebook $notebook
   */
  public function setNotebook(GoogleCloudDataplexV1ContentNotebook $notebook)
  {
    $this->notebook = $notebook;
  }
  /**
   * @return GoogleCloudDataplexV1ContentNotebook
   */
  public function getNotebook()
  {
    return $this->notebook;
  }
  /**
   * Required. The path for the Content file, represented as directory
   * structure. Unique within a lake. Limited to alphanumerics, hyphens,
   * underscores, dots and slashes.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Sql Script related configurations.
   *
   * @param GoogleCloudDataplexV1ContentSqlScript $sqlScript
   */
  public function setSqlScript(GoogleCloudDataplexV1ContentSqlScript $sqlScript)
  {
    $this->sqlScript = $sqlScript;
  }
  /**
   * @return GoogleCloudDataplexV1ContentSqlScript
   */
  public function getSqlScript()
  {
    return $this->sqlScript;
  }
  /**
   * Output only. System generated globally unique ID for the content. This ID
   * will be different if the content is deleted and re-created with the same
   * name.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. The time when the content was last updated.
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
class_alias(GoogleCloudDataplexV1Content::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1Content');
