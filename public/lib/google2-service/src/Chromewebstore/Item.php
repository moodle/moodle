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

class Item extends \Google\Collection
{
  protected $collection_key = 'itemError';
  /**
   * @var string
   */
  public $crxVersion;
  /**
   * @var string
   */
  public $id;
  protected $itemErrorType = ItemError::class;
  protected $itemErrorDataType = 'array';
  /**
   * @var string
   */
  public $kind;
  /**
   * @var string
   */
  public $publicKey;
  /**
   * @var string
   */
  public $uploadState;

  /**
   * @param string
   */
  public function setCrxVersion($crxVersion)
  {
    $this->crxVersion = $crxVersion;
  }
  /**
   * @return string
   */
  public function getCrxVersion()
  {
    return $this->crxVersion;
  }
  /**
   * @param string
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
   * @param ItemError[]
   */
  public function setItemError($itemError)
  {
    $this->itemError = $itemError;
  }
  /**
   * @return ItemError[]
   */
  public function getItemError()
  {
    return $this->itemError;
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
   * @param string
   */
  public function setPublicKey($publicKey)
  {
    $this->publicKey = $publicKey;
  }
  /**
   * @return string
   */
  public function getPublicKey()
  {
    return $this->publicKey;
  }
  /**
   * @param string
   */
  public function setUploadState($uploadState)
  {
    $this->uploadState = $uploadState;
  }
  /**
   * @return string
   */
  public function getUploadState()
  {
    return $this->uploadState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Item::class, 'Google_Service_Chromewebstore_Item');
