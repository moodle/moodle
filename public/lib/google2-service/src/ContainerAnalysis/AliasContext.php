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

namespace Google\Service\ContainerAnalysis;

class AliasContext extends \Google\Model
{
  /**
   * Unknown.
   */
  public const KIND_KIND_UNSPECIFIED = 'KIND_UNSPECIFIED';
  /**
   * Git tag.
   */
  public const KIND_FIXED = 'FIXED';
  /**
   * Git branch.
   */
  public const KIND_MOVABLE = 'MOVABLE';
  /**
   * Used to specify non-standard aliases. For example, if a Git repo has a ref
   * named "refs/foo/bar".
   */
  public const KIND_OTHER = 'OTHER';
  /**
   * The alias kind.
   *
   * @var string
   */
  public $kind;
  /**
   * The alias name.
   *
   * @var string
   */
  public $name;

  /**
   * The alias kind.
   *
   * Accepted values: KIND_UNSPECIFIED, FIXED, MOVABLE, OTHER
   *
   * @param self::KIND_* $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return self::KIND_*
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The alias name.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AliasContext::class, 'Google_Service_ContainerAnalysis_AliasContext');
