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

namespace Google\Service\Analytics;

class CustomDimension extends \Google\Model
{
  /**
   * Account ID.
   *
   * @var string
   */
  public $accountId;
  /**
   * Boolean indicating whether the custom dimension is active.
   *
   * @var bool
   */
  public $active;
  /**
   * Time the custom dimension was created.
   *
   * @var string
   */
  public $created;
  /**
   * Custom dimension ID.
   *
   * @var string
   */
  public $id;
  /**
   * Index of the custom dimension.
   *
   * @var int
   */
  public $index;
  /**
   * Kind value for a custom dimension. Set to "analytics#customDimension". It
   * is a read-only field.
   *
   * @var string
   */
  public $kind;
  /**
   * Name of the custom dimension.
   *
   * @var string
   */
  public $name;
  protected $parentLinkType = CustomDimensionParentLink::class;
  protected $parentLinkDataType = '';
  /**
   * Scope of the custom dimension: HIT, SESSION, USER or PRODUCT.
   *
   * @var string
   */
  public $scope;
  /**
   * Link for the custom dimension
   *
   * @var string
   */
  public $selfLink;
  /**
   * Time the custom dimension was last modified.
   *
   * @var string
   */
  public $updated;
  /**
   * Property ID.
   *
   * @var string
   */
  public $webPropertyId;

  /**
   * Account ID.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * Boolean indicating whether the custom dimension is active.
   *
   * @param bool $active
   */
  public function setActive($active)
  {
    $this->active = $active;
  }
  /**
   * @return bool
   */
  public function getActive()
  {
    return $this->active;
  }
  /**
   * Time the custom dimension was created.
   *
   * @param string $created
   */
  public function setCreated($created)
  {
    $this->created = $created;
  }
  /**
   * @return string
   */
  public function getCreated()
  {
    return $this->created;
  }
  /**
   * Custom dimension ID.
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
   * Index of the custom dimension.
   *
   * @param int $index
   */
  public function setIndex($index)
  {
    $this->index = $index;
  }
  /**
   * @return int
   */
  public function getIndex()
  {
    return $this->index;
  }
  /**
   * Kind value for a custom dimension. Set to "analytics#customDimension". It
   * is a read-only field.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Name of the custom dimension.
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
   * Parent link for the custom dimension. Points to the property to which the
   * custom dimension belongs.
   *
   * @param CustomDimensionParentLink $parentLink
   */
  public function setParentLink(CustomDimensionParentLink $parentLink)
  {
    $this->parentLink = $parentLink;
  }
  /**
   * @return CustomDimensionParentLink
   */
  public function getParentLink()
  {
    return $this->parentLink;
  }
  /**
   * Scope of the custom dimension: HIT, SESSION, USER or PRODUCT.
   *
   * @param string $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return string
   */
  public function getScope()
  {
    return $this->scope;
  }
  /**
   * Link for the custom dimension
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Time the custom dimension was last modified.
   *
   * @param string $updated
   */
  public function setUpdated($updated)
  {
    $this->updated = $updated;
  }
  /**
   * @return string
   */
  public function getUpdated()
  {
    return $this->updated;
  }
  /**
   * Property ID.
   *
   * @param string $webPropertyId
   */
  public function setWebPropertyId($webPropertyId)
  {
    $this->webPropertyId = $webPropertyId;
  }
  /**
   * @return string
   */
  public function getWebPropertyId()
  {
    return $this->webPropertyId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomDimension::class, 'Google_Service_Analytics_CustomDimension');
