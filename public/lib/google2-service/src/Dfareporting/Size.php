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

namespace Google\Service\Dfareporting;

class Size extends \Google\Model
{
  /**
   * Height of this size. Acceptable values are 0 to 32767, inclusive.
   *
   * @var int
   */
  public $height;
  /**
   * IAB standard size. This is a read-only, auto-generated field.
   *
   * @var bool
   */
  public $iab;
  /**
   * ID of this size. This is a read-only, auto-generated field.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#size".
   *
   * @var string
   */
  public $kind;
  /**
   * Width of this size. Acceptable values are 0 to 32767, inclusive.
   *
   * @var int
   */
  public $width;

  /**
   * Height of this size. Acceptable values are 0 to 32767, inclusive.
   *
   * @param int $height
   */
  public function setHeight($height)
  {
    $this->height = $height;
  }
  /**
   * @return int
   */
  public function getHeight()
  {
    return $this->height;
  }
  /**
   * IAB standard size. This is a read-only, auto-generated field.
   *
   * @param bool $iab
   */
  public function setIab($iab)
  {
    $this->iab = $iab;
  }
  /**
   * @return bool
   */
  public function getIab()
  {
    return $this->iab;
  }
  /**
   * ID of this size. This is a read-only, auto-generated field.
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
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#size".
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
   * Width of this size. Acceptable values are 0 to 32767, inclusive.
   *
   * @param int $width
   */
  public function setWidth($width)
  {
    $this->width = $width;
  }
  /**
   * @return int
   */
  public function getWidth()
  {
    return $this->width;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Size::class, 'Google_Service_Dfareporting_Size');
