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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailLoggingImportErrorContext extends \Google\Model
{
  /**
   * The detailed content which caused the error on importing a catalog item.
   *
   * @var string
   */
  public $catalogItem;
  /**
   * Cloud Storage file path of the import source. Can be set for batch
   * operation error.
   *
   * @var string
   */
  public $gcsPath;
  /**
   * Line number of the content in file. Should be empty for permission or batch
   * operation error.
   *
   * @var string
   */
  public $lineNumber;
  /**
   * The operation resource name of the LRO.
   *
   * @var string
   */
  public $operationName;
  /**
   * The detailed content which caused the error on importing a product.
   *
   * @var string
   */
  public $product;
  /**
   * The detailed content which caused the error on importing a user event.
   *
   * @var string
   */
  public $userEvent;

  /**
   * The detailed content which caused the error on importing a catalog item.
   *
   * @param string $catalogItem
   */
  public function setCatalogItem($catalogItem)
  {
    $this->catalogItem = $catalogItem;
  }
  /**
   * @return string
   */
  public function getCatalogItem()
  {
    return $this->catalogItem;
  }
  /**
   * Cloud Storage file path of the import source. Can be set for batch
   * operation error.
   *
   * @param string $gcsPath
   */
  public function setGcsPath($gcsPath)
  {
    $this->gcsPath = $gcsPath;
  }
  /**
   * @return string
   */
  public function getGcsPath()
  {
    return $this->gcsPath;
  }
  /**
   * Line number of the content in file. Should be empty for permission or batch
   * operation error.
   *
   * @param string $lineNumber
   */
  public function setLineNumber($lineNumber)
  {
    $this->lineNumber = $lineNumber;
  }
  /**
   * @return string
   */
  public function getLineNumber()
  {
    return $this->lineNumber;
  }
  /**
   * The operation resource name of the LRO.
   *
   * @param string $operationName
   */
  public function setOperationName($operationName)
  {
    $this->operationName = $operationName;
  }
  /**
   * @return string
   */
  public function getOperationName()
  {
    return $this->operationName;
  }
  /**
   * The detailed content which caused the error on importing a product.
   *
   * @param string $product
   */
  public function setProduct($product)
  {
    $this->product = $product;
  }
  /**
   * @return string
   */
  public function getProduct()
  {
    return $this->product;
  }
  /**
   * The detailed content which caused the error on importing a user event.
   *
   * @param string $userEvent
   */
  public function setUserEvent($userEvent)
  {
    $this->userEvent = $userEvent;
  }
  /**
   * @return string
   */
  public function getUserEvent()
  {
    return $this->userEvent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailLoggingImportErrorContext::class, 'Google_Service_CloudRetail_GoogleCloudRetailLoggingImportErrorContext');
