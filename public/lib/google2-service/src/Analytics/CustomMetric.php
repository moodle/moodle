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

class CustomMetric extends \Google\Model
{
  protected $internal_gapi_mappings = [
        "maxValue" => "max_value",
        "minValue" => "min_value",
  ];
  /**
   * Account ID.
   *
   * @var string
   */
  public $accountId;
  /**
   * Boolean indicating whether the custom metric is active.
   *
   * @var bool
   */
  public $active;
  /**
   * Time the custom metric was created.
   *
   * @var string
   */
  public $created;
  /**
   * Custom metric ID.
   *
   * @var string
   */
  public $id;
  /**
   * Index of the custom metric.
   *
   * @var int
   */
  public $index;
  /**
   * Kind value for a custom metric. Set to "analytics#customMetric". It is a
   * read-only field.
   *
   * @var string
   */
  public $kind;
  /**
   * Max value of custom metric.
   *
   * @var string
   */
  public $maxValue;
  /**
   * Min value of custom metric.
   *
   * @var string
   */
  public $minValue;
  /**
   * Name of the custom metric.
   *
   * @var string
   */
  public $name;
  protected $parentLinkType = CustomMetricParentLink::class;
  protected $parentLinkDataType = '';
  /**
   * Scope of the custom metric: HIT or PRODUCT.
   *
   * @var string
   */
  public $scope;
  /**
   * Link for the custom metric
   *
   * @var string
   */
  public $selfLink;
  /**
   * Data type of custom metric.
   *
   * @var string
   */
  public $type;
  /**
   * Time the custom metric was last modified.
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
   * Boolean indicating whether the custom metric is active.
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
   * Time the custom metric was created.
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
   * Custom metric ID.
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
   * Index of the custom metric.
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
   * Kind value for a custom metric. Set to "analytics#customMetric". It is a
   * read-only field.
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
   * Max value of custom metric.
   *
   * @param string $maxValue
   */
  public function setMaxValue($maxValue)
  {
    $this->maxValue = $maxValue;
  }
  /**
   * @return string
   */
  public function getMaxValue()
  {
    return $this->maxValue;
  }
  /**
   * Min value of custom metric.
   *
   * @param string $minValue
   */
  public function setMinValue($minValue)
  {
    $this->minValue = $minValue;
  }
  /**
   * @return string
   */
  public function getMinValue()
  {
    return $this->minValue;
  }
  /**
   * Name of the custom metric.
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
   * Parent link for the custom metric. Points to the property to which the
   * custom metric belongs.
   *
   * @param CustomMetricParentLink $parentLink
   */
  public function setParentLink(CustomMetricParentLink $parentLink)
  {
    $this->parentLink = $parentLink;
  }
  /**
   * @return CustomMetricParentLink
   */
  public function getParentLink()
  {
    return $this->parentLink;
  }
  /**
   * Scope of the custom metric: HIT or PRODUCT.
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
   * Link for the custom metric
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
   * Data type of custom metric.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Time the custom metric was last modified.
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
class_alias(CustomMetric::class, 'Google_Service_Analytics_CustomMetric');
