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

class Product extends \Google\Model
{
  /**
   * Contains a URI which is vendor-specific. Example: The artifact repository
   * URL of an image.
   *
   * @var string
   */
  public $genericUri;
  /**
   * Token that identifies a product so that it can be referred to from other
   * parts in the document. There is no predefined format as long as it uniquely
   * identifies a group in the context of the current document.
   *
   * @var string
   */
  public $id;
  /**
   * Name of the product.
   *
   * @var string
   */
  public $name;

  /**
   * Contains a URI which is vendor-specific. Example: The artifact repository
   * URL of an image.
   *
   * @param string $genericUri
   */
  public function setGenericUri($genericUri)
  {
    $this->genericUri = $genericUri;
  }
  /**
   * @return string
   */
  public function getGenericUri()
  {
    return $this->genericUri;
  }
  /**
   * Token that identifies a product so that it can be referred to from other
   * parts in the document. There is no predefined format as long as it uniquely
   * identifies a group in the context of the current document.
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
   * Name of the product.
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
class_alias(Product::class, 'Google_Service_ContainerAnalysis_Product');
