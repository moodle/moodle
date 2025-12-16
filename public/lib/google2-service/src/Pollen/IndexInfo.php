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

namespace Google\Service\Pollen;

class IndexInfo extends \Google\Model
{
  /**
   * Unspecified index.
   */
  public const CODE_INDEX_UNSPECIFIED = 'INDEX_UNSPECIFIED';
  /**
   * Universal Pollen Index.
   */
  public const CODE_UPI = 'UPI';
  /**
   * Text classification of index numerical score interpretation. The index
   * consists of six categories: * 0: "None" * 1: "Very low" * 2: "Low" * 3:
   * "Moderate" * 4: "High" * 5: "Very high
   *
   * @var string
   */
  public $category;
  /**
   * The index's code. This field represents the index for programming purposes
   * by using snake cases instead of spaces. Example: "UPI".
   *
   * @var string
   */
  public $code;
  protected $colorType = Color::class;
  protected $colorDataType = '';
  /**
   * A human readable representation of the index name. Example: "Universal
   * Pollen Index".
   *
   * @var string
   */
  public $displayName;
  /**
   * Textual explanation of current index level.
   *
   * @var string
   */
  public $indexDescription;
  /**
   * The index's numeric score. Numeric range is between 0 and 5.
   *
   * @var int
   */
  public $value;

  /**
   * Text classification of index numerical score interpretation. The index
   * consists of six categories: * 0: "None" * 1: "Very low" * 2: "Low" * 3:
   * "Moderate" * 4: "High" * 5: "Very high
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
   * The index's code. This field represents the index for programming purposes
   * by using snake cases instead of spaces. Example: "UPI".
   *
   * Accepted values: INDEX_UNSPECIFIED, UPI
   *
   * @param self::CODE_* $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return self::CODE_*
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * The color used to represent the Pollen Index numeric score.
   *
   * @param Color $color
   */
  public function setColor(Color $color)
  {
    $this->color = $color;
  }
  /**
   * @return Color
   */
  public function getColor()
  {
    return $this->color;
  }
  /**
   * A human readable representation of the index name. Example: "Universal
   * Pollen Index".
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Textual explanation of current index level.
   *
   * @param string $indexDescription
   */
  public function setIndexDescription($indexDescription)
  {
    $this->indexDescription = $indexDescription;
  }
  /**
   * @return string
   */
  public function getIndexDescription()
  {
    return $this->indexDescription;
  }
  /**
   * The index's numeric score. Numeric range is between 0 and 5.
   *
   * @param int $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return int
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IndexInfo::class, 'Google_Service_Pollen_IndexInfo');
