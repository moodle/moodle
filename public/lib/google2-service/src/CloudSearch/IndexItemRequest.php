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

namespace Google\Service\CloudSearch;

class IndexItemRequest extends \Google\Model
{
  /**
   * The priority is not specified in the update request. Leaving priority
   * unspecified results in an update failure.
   */
  public const MODE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * For real-time updates.
   */
  public const MODE_SYNCHRONOUS = 'SYNCHRONOUS';
  /**
   * For changes that are executed after the response is sent back to the
   * caller.
   */
  public const MODE_ASYNCHRONOUS = 'ASYNCHRONOUS';
  /**
   * The name of connector making this call. Format:
   * datasources/{source_id}/connectors/{ID}
   *
   * @var string
   */
  public $connectorName;
  protected $debugOptionsType = DebugOptions::class;
  protected $debugOptionsDataType = '';
  protected $indexItemOptionsType = IndexItemOptions::class;
  protected $indexItemOptionsDataType = '';
  protected $itemType = Item::class;
  protected $itemDataType = '';
  /**
   * Required. The RequestMode for this request.
   *
   * @var string
   */
  public $mode;

  /**
   * The name of connector making this call. Format:
   * datasources/{source_id}/connectors/{ID}
   *
   * @param string $connectorName
   */
  public function setConnectorName($connectorName)
  {
    $this->connectorName = $connectorName;
  }
  /**
   * @return string
   */
  public function getConnectorName()
  {
    return $this->connectorName;
  }
  /**
   * Common debug options.
   *
   * @param DebugOptions $debugOptions
   */
  public function setDebugOptions(DebugOptions $debugOptions)
  {
    $this->debugOptions = $debugOptions;
  }
  /**
   * @return DebugOptions
   */
  public function getDebugOptions()
  {
    return $this->debugOptions;
  }
  /**
   * @param IndexItemOptions $indexItemOptions
   */
  public function setIndexItemOptions(IndexItemOptions $indexItemOptions)
  {
    $this->indexItemOptions = $indexItemOptions;
  }
  /**
   * @return IndexItemOptions
   */
  public function getIndexItemOptions()
  {
    return $this->indexItemOptions;
  }
  /**
   * The name of the item. Format: datasources/{source_id}/items/{item_id}
   *
   * @param Item $item
   */
  public function setItem(Item $item)
  {
    $this->item = $item;
  }
  /**
   * @return Item
   */
  public function getItem()
  {
    return $this->item;
  }
  /**
   * Required. The RequestMode for this request.
   *
   * Accepted values: UNSPECIFIED, SYNCHRONOUS, ASYNCHRONOUS
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IndexItemRequest::class, 'Google_Service_CloudSearch_IndexItemRequest');
