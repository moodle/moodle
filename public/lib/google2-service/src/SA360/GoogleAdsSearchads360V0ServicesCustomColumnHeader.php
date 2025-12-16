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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ServicesCustomColumnHeader extends \Google\Model
{
  /**
   * The custom column ID.
   *
   * @var string
   */
  public $id;
  /**
   * The user defined name of the custom column.
   *
   * @var string
   */
  public $name;
  /**
   * True when the custom column references metrics.
   *
   * @var bool
   */
  public $referencesMetrics;

  /**
   * The custom column ID.
   *
   * @param string $id
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
   * The user defined name of the custom column.
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
   * True when the custom column references metrics.
   *
   * @param bool $referencesMetrics
   */
  public function setReferencesMetrics($referencesMetrics)
  {
    $this->referencesMetrics = $referencesMetrics;
  }
  /**
   * @return bool
   */
  public function getReferencesMetrics()
  {
    return $this->referencesMetrics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ServicesCustomColumnHeader::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ServicesCustomColumnHeader');
