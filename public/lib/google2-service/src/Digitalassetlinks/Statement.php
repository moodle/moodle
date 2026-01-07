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

class Statement extends \Google\Model
{
  /**
   * The relation identifies the use of the statement as intended by the source
   * asset's owner (that is, the person or entity who issued the statement).
   * Every complete statement has a relation. We identify relations with strings
   * of the format `/`, where `` must be one of a set of pre-defined purpose
   * categories, and `` is a free-form lowercase alphanumeric string that
   * describes the specific use case of the statement. Refer to [our API
   * documentation](/digital-asset-links/v1/relation-strings) for the current
   * list of supported relations. Example:
   * `delegate_permission/common.handle_all_urls` REQUIRED
   *
   * @var string
   */
  public $relation;
  /**
   * Statements may specify relation level extensions/payloads to express more
   * details when declaring permissions to grant from the source asset to the
   * target asset. These relation extensions should be specified in the
   * `relation_extensions` object, keyed by the relation type they're associated
   * with. { relation: ["delegate_permission/common.handle_all_urls"], target:
   * {...}, relation_extensions: { "delegate_permission/common.handle_all_urls":
   * { ...handle_all_urls specific payload specified here... } } } When
   * requested, and specified in the statement file, the API will return
   * relation_extensions associated with the statement's relation type. i.e. the
   * API will only return relation_extensions specified for
   * "delegate_permission/common.handle_all_urls" if this statement object's
   * relation type is "delegate_permission/common.handle_all_urls".
   *
   * @var array[]
   */
  public $relationExtensions;
  protected $sourceType = Asset::class;
  protected $sourceDataType = '';
  protected $targetType = Asset::class;
  protected $targetDataType = '';

  /**
   * The relation identifies the use of the statement as intended by the source
   * asset's owner (that is, the person or entity who issued the statement).
   * Every complete statement has a relation. We identify relations with strings
   * of the format `/`, where `` must be one of a set of pre-defined purpose
   * categories, and `` is a free-form lowercase alphanumeric string that
   * describes the specific use case of the statement. Refer to [our API
   * documentation](/digital-asset-links/v1/relation-strings) for the current
   * list of supported relations. Example:
   * `delegate_permission/common.handle_all_urls` REQUIRED
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
   * Statements may specify relation level extensions/payloads to express more
   * details when declaring permissions to grant from the source asset to the
   * target asset. These relation extensions should be specified in the
   * `relation_extensions` object, keyed by the relation type they're associated
   * with. { relation: ["delegate_permission/common.handle_all_urls"], target:
   * {...}, relation_extensions: { "delegate_permission/common.handle_all_urls":
   * { ...handle_all_urls specific payload specified here... } } } When
   * requested, and specified in the statement file, the API will return
   * relation_extensions associated with the statement's relation type. i.e. the
   * API will only return relation_extensions specified for
   * "delegate_permission/common.handle_all_urls" if this statement object's
   * relation type is "delegate_permission/common.handle_all_urls".
   *
   * @param array[] $relationExtensions
   */
  public function setRelationExtensions($relationExtensions)
  {
    $this->relationExtensions = $relationExtensions;
  }
  /**
   * @return array[]
   */
  public function getRelationExtensions()
  {
    return $this->relationExtensions;
  }
  /**
   * Every statement has a source asset. REQUIRED
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
   * Every statement has a target asset. REQUIRED
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
class_alias(Statement::class, 'Google_Service_Digitalassetlinks_Statement');
