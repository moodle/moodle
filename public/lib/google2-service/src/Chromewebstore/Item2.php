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

namespace Google\Service\Chromewebstore;

class Item2 extends \Google\Collection
{
  protected $collection_key = 'statusDetail';
  protected $internal_gapi_mappings = [
        "itemId" => "item_id",
  ];
  /**
   * @var string
   */
  public $itemId;
  /**
   * @var string
   */
  public $kind;
  /**
   * @var string[]
   */
  public $status;
  /**
   * @var string[]
   */
  public $statusDetail;

  /**
   * @param string
   */
  public function setItemId($itemId)
  {
    $this->itemId = $itemId;
  }
  /**
   * @return string
   */
  public function getItemId()
  {
    return $this->itemId;
  }
  /**
   * @param string
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
   * @param string[]
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return string[]
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * @param string[]
   */
  public function setStatusDetail($statusDetail)
  {
    $this->statusDetail = $statusDetail;
  }
  /**
   * @return string[]
   */
  public function getStatusDetail()
  {
    return $this->statusDetail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Item2::class, 'Google_Service_Chromewebstore_Item2');
