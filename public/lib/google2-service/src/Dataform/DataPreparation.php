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

namespace Google\Service\Dataform;

class DataPreparation extends \Google\Collection
{
  protected $collection_key = 'tags';
  protected $contentsSqlType = SqlDefinition::class;
  protected $contentsSqlDataType = '';
  /**
   * The data preparation definition, stored as a YAML string.
   *
   * @var string
   */
  public $contentsYaml;
  protected $dependencyTargetsType = Target::class;
  protected $dependencyTargetsDataType = 'array';
  /**
   * Whether this action is disabled (i.e. should not be run).
   *
   * @var bool
   */
  public $disabled;
  /**
   * Arbitrary, user-defined tags on this action.
   *
   * @var string[]
   */
  public $tags;

  /**
   * SQL definition for a Data Preparation. Contains a SQL query and additional
   * context information.
   *
   * @param SqlDefinition $contentsSql
   */
  public function setContentsSql(SqlDefinition $contentsSql)
  {
    $this->contentsSql = $contentsSql;
  }
  /**
   * @return SqlDefinition
   */
  public function getContentsSql()
  {
    return $this->contentsSql;
  }
  /**
   * The data preparation definition, stored as a YAML string.
   *
   * @param string $contentsYaml
   */
  public function setContentsYaml($contentsYaml)
  {
    $this->contentsYaml = $contentsYaml;
  }
  /**
   * @return string
   */
  public function getContentsYaml()
  {
    return $this->contentsYaml;
  }
  /**
   * A list of actions that this action depends on.
   *
   * @param Target[] $dependencyTargets
   */
  public function setDependencyTargets($dependencyTargets)
  {
    $this->dependencyTargets = $dependencyTargets;
  }
  /**
   * @return Target[]
   */
  public function getDependencyTargets()
  {
    return $this->dependencyTargets;
  }
  /**
   * Whether this action is disabled (i.e. should not be run).
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * Arbitrary, user-defined tags on this action.
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataPreparation::class, 'Google_Service_Dataform_DataPreparation');
