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

class StatementTemplate extends \Google\Model
{
  /**
   * The relationship being asserted between the source and target. If omitted,
   * you must specify a BulkCheckRequest.default_relation value to use here.
   *
   * @var string
   */
  public $relation;
  protected $sourceType = Asset::class;
  protected $sourceDataType = '';
  protected $targetType = Asset::class;
  protected $targetDataType = '';

  /**
   * The relationship being asserted between the source and target. If omitted,
   * you must specify a BulkCheckRequest.default_relation value to use here.
   *
   * @param string $relation
   */
  public function setRelation($relation)
  {
    $this->relation = $relation;
  }
  /**
   * @return string
   */
  public function getRelation()
  {
    return $this->relation;
  }
  /**
   * The source asset that is asserting the statement. If omitted, you must
   * specify a BulkCheckRequest.default_source value to use here.
   *
   * @param Asset $source
   */
  public function setSource(Asset $source)
  {
    $this->source = $source;
  }
  /**
   * @return Asset
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * The target that the source is declaring the relationship with. If omitted,
   * you must specify a BulkCheckRequest.default_target to use here.
   *
   * @param Asset $target
   */
  public function setTarget(Asset $target)
  {
    $this->target = $target;
  }
  /**
   * @return Asset
   */
  public function getTarget()
  {
    return $this->target;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StatementTemplate::class, 'Google_Service_Digitalassetlinks_StatementTemplate');
