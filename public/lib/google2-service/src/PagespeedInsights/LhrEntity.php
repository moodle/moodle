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

namespace Google\Service\PagespeedInsights;

class LhrEntity extends \Google\Collection
{
  protected $collection_key = 'origins';
  /**
   * Optional. An optional category name for the entity.
   *
   * @var string
   */
  public $category;
  /**
   * Optional. An optional homepage URL of the entity.
   *
   * @var string
   */
  public $homepage;
  /**
   * Optional. An optional flag indicating if the entity is the first party.
   *
   * @var bool
   */
  public $isFirstParty;
  /**
   * Optional. An optional flag indicating if the entity is not recognized.
   *
   * @var bool
   */
  public $isUnrecognized;
  /**
   * Required. Name of the entity.
   *
   * @var string
   */
  public $name;
  /**
   * Required. A list of URL origin strings that belong to this entity.
   *
   * @var string[]
   */
  public $origins;

  /**
   * Optional. An optional category name for the entity.
   *
   * @param string $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return string
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * Optional. An optional homepage URL of the entity.
   *
   * @param string $homepage
   */
  public function setHomepage($homepage)
  {
    $this->homepage = $homepage;
  }
  /**
   * @return string
   */
  public function getHomepage()
  {
    return $this->homepage;
  }
  /**
   * Optional. An optional flag indicating if the entity is the first party.
   *
   * @param bool $isFirstParty
   */
  public function setIsFirstParty($isFirstParty)
  {
    $this->isFirstParty = $isFirstParty;
  }
  /**
   * @return bool
   */
  public function getIsFirstParty()
  {
    return $this->isFirstParty;
  }
  /**
   * Optional. An optional flag indicating if the entity is not recognized.
   *
   * @param bool $isUnrecognized
   */
  public function setIsUnrecognized($isUnrecognized)
  {
    $this->isUnrecognized = $isUnrecognized;
  }
  /**
   * @return bool
   */
  public function getIsUnrecognized()
  {
    return $this->isUnrecognized;
  }
  /**
   * Required. Name of the entity.
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
   * Required. A list of URL origin strings that belong to this entity.
   *
   * @param string[] $origins
   */
  public function setOrigins($origins)
  {
    $this->origins = $origins;
  }
  /**
   * @return string[]
   */
  public function getOrigins()
  {
    return $this->origins;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LhrEntity::class, 'Google_Service_PagespeedInsights_LhrEntity');
