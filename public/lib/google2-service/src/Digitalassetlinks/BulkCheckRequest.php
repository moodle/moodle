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

namespace Google\Service\Digitalassetlinks;

class BulkCheckRequest extends \Google\Collection
{
  protected $collection_key = 'statements';
  /**
   * If specified, will be used in any given template statement that doesn’t
   * specify a relation.
   *
   * @var string
   */
  public $defaultRelation;
  protected $defaultSourceType = Asset::class;
  protected $defaultSourceDataType = '';
  protected $defaultTargetType = Asset::class;
  protected $defaultTargetDataType = '';
  /**
   * Same configuration as in CheckRequest; all statement checks will use the
   * same configuration.
   *
   * @var bool
   */
  public $returnRelationExtensions;
  protected $statementsType = StatementTemplate::class;
  protected $statementsDataType = 'array';

  /**
   * If specified, will be used in any given template statement that doesn’t
   * specify a relation.
   *
   * @param string $defaultRelation
   */
  public function setDefaultRelation($defaultRelation)
  {
    $this->defaultRelation = $defaultRelation;
  }
  /**
   * @return string
   */
  public function getDefaultRelation()
  {
    return $this->defaultRelation;
  }
  /**
   * If specified, will be used in any given template statement that doesn’t
   * specify a source.
   *
   * @param Asset $defaultSource
   */
  public function setDefaultSource(Asset $defaultSource)
  {
    $this->defaultSource = $defaultSource;
  }
  /**
   * @return Asset
   */
  public function getDefaultSource()
  {
    return $this->defaultSource;
  }
  /**
   * If specified, will be used in any given template statement that doesn’t
   * specify a target.
   *
   * @param Asset $defaultTarget
   */
  public function setDefaultTarget(Asset $defaultTarget)
  {
    $this->defaultTarget = $defaultTarget;
  }
  /**
   * @return Asset
   */
  public function getDefaultTarget()
  {
    return $this->defaultTarget;
  }
  /**
   * Same configuration as in CheckRequest; all statement checks will use the
   * same configuration.
   *
   * @param bool $returnRelationExtensions
   */
  public function setReturnRelationExtensions($returnRelationExtensions)
  {
    $this->returnRelationExtensions = $returnRelationExtensions;
  }
  /**
   * @return bool
   */
  public function getReturnRelationExtensions()
  {
    return $this->returnRelationExtensions;
  }
  /**
   * List of statements to check. For each statement, you can omit a field if
   * the corresponding default_* field below was supplied. Minimum 1 statement;
   * maximum 1,000 statements. Any additional statements will be ignored.
   *
   * @param StatementTemplate[] $statements
   */
  public function setStatements($statements)
  {
    $this->statements = $statements;
  }
  /**
   * @return StatementTemplate[]
   */
  public function getStatements()
  {
    return $this->statements;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BulkCheckRequest::class, 'Google_Service_Digitalassetlinks_BulkCheckRequest');
