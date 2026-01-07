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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1DocumentInfo extends \Google\Collection
{
  protected $collection_key = 'promotionIds';
  /**
   * Optional. The conversion value associated with this Document. Must be set
   * if UserEvent.event_type is "conversion". For example, a value of 1000
   * signifies that 1000 seconds were spent viewing a Document for the `watch`
   * conversion type.
   *
   * @var float
   */
  public $conversionValue;
  /**
   * The Document resource ID.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. Whether the referenced Document can be found in the data
   * store.
   *
   * @var bool
   */
  public $joined;
  /**
   * The Document resource full name, of the form: `projects/{project}/locations
   * /{location}/collections/{collection_id}/dataStores/{data_store_id}/branches
   * /{branch_id}/documents/{document_id}`
   *
   * @var string
   */
  public $name;
  /**
   * The promotion IDs associated with this Document. Currently, this field is
   * restricted to at most one ID.
   *
   * @var string[]
   */
  public $promotionIds;
  /**
   * Quantity of the Document associated with the user event. Defaults to 1. For
   * example, this field is 2 if two quantities of the same Document are
   * involved in a `add-to-cart` event. Required for events of the following
   * event types: * `add-to-cart` * `purchase`
   *
   * @var int
   */
  public $quantity;
  /**
   * The Document URI - only allowed for website data stores.
   *
   * @var string
   */
  public $uri;

  /**
   * Optional. The conversion value associated with this Document. Must be set
   * if UserEvent.event_type is "conversion". For example, a value of 1000
   * signifies that 1000 seconds were spent viewing a Document for the `watch`
   * conversion type.
   *
   * @param float $conversionValue
   */
  public function setConversionValue($conversionValue)
  {
    $this->conversionValue = $conversionValue;
  }
  /**
   * @return float
   */
  public function getConversionValue()
  {
    return $this->conversionValue;
  }
  /**
   * The Document resource ID.
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
   * Output only. Whether the referenced Document can be found in the data
   * store.
   *
   * @param bool $joined
   */
  public function setJoined($joined)
  {
    $this->joined = $joined;
  }
  /**
   * @return bool
   */
  public function getJoined()
  {
    return $this->joined;
  }
  /**
   * The Document resource full name, of the form: `projects/{project}/locations
   * /{location}/collections/{collection_id}/dataStores/{data_store_id}/branches
   * /{branch_id}/documents/{document_id}`
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
   * The promotion IDs associated with this Document. Currently, this field is
   * restricted to at most one ID.
   *
   * @param string[] $promotionIds
   */
  public function setPromotionIds($promotionIds)
  {
    $this->promotionIds = $promotionIds;
  }
  /**
   * @return string[]
   */
  public function getPromotionIds()
  {
    return $this->promotionIds;
  }
  /**
   * Quantity of the Document associated with the user event. Defaults to 1. For
   * example, this field is 2 if two quantities of the same Document are
   * involved in a `add-to-cart` event. Required for events of the following
   * event types: * `add-to-cart` * `purchase`
   *
   * @param int $quantity
   */
  public function setQuantity($quantity)
  {
    $this->quantity = $quantity;
  }
  /**
   * @return int
   */
  public function getQuantity()
  {
    return $this->quantity;
  }
  /**
   * The Document URI - only allowed for website data stores.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1DocumentInfo::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1DocumentInfo');
