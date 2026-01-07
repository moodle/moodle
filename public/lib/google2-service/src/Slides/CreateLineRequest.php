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

namespace Google\Service\Slides;

class CreateLineRequest extends \Google\Model
{
  /**
   * Unspecified line category.
   */
  public const CATEGORY_LINE_CATEGORY_UNSPECIFIED = 'LINE_CATEGORY_UNSPECIFIED';
  /**
   * Straight connectors, including straight connector 1.
   */
  public const CATEGORY_STRAIGHT = 'STRAIGHT';
  /**
   * Bent connectors, including bent connector 2 to 5.
   */
  public const CATEGORY_BENT = 'BENT';
  /**
   * Curved connectors, including curved connector 2 to 5.
   */
  public const CATEGORY_CURVED = 'CURVED';
  /**
   * Straight connectors, including straight connector 1. The is the default
   * category when one is not specified.
   */
  public const LINE_CATEGORY_STRAIGHT = 'STRAIGHT';
  /**
   * Bent connectors, including bent connector 2 to 5.
   */
  public const LINE_CATEGORY_BENT = 'BENT';
  /**
   * Curved connectors, including curved connector 2 to 5.
   */
  public const LINE_CATEGORY_CURVED = 'CURVED';
  /**
   * The category of the line to be created. The exact line type created is
   * determined based on the category and how it's routed to connect to other
   * page elements. If you specify both a `category` and a `line_category`, the
   * `category` takes precedence. If you do not specify a value for `category`,
   * but specify a value for `line_category`, then the specified `line_category`
   * value is used. If you do not specify either, then STRAIGHT is used.
   *
   * @var string
   */
  public $category;
  protected $elementPropertiesType = PageElementProperties::class;
  protected $elementPropertiesDataType = '';
  /**
   * The category of the line to be created. *Deprecated*: use `category`
   * instead. The exact line type created is determined based on the category
   * and how it's routed to connect to other page elements. If you specify both
   * a `category` and a `line_category`, the `category` takes precedence.
   *
   * @deprecated
   * @var string
   */
  public $lineCategory;
  /**
   * A user-supplied object ID. If you specify an ID, it must be unique among
   * all pages and page elements in the presentation. The ID must start with an
   * alphanumeric character or an underscore (matches regex `[a-zA-Z0-9_]`);
   * remaining characters may include those as well as a hyphen or colon
   * (matches regex `[a-zA-Z0-9_-:]`). The length of the ID must not be less
   * than 5 or greater than 50. If you don't specify an ID, a unique one is
   * generated.
   *
   * @var string
   */
  public $objectId;

  /**
   * The category of the line to be created. The exact line type created is
   * determined based on the category and how it's routed to connect to other
   * page elements. If you specify both a `category` and a `line_category`, the
   * `category` takes precedence. If you do not specify a value for `category`,
   * but specify a value for `line_category`, then the specified `line_category`
   * value is used. If you do not specify either, then STRAIGHT is used.
   *
   * Accepted values: LINE_CATEGORY_UNSPECIFIED, STRAIGHT, BENT, CURVED
   *
   * @param self::CATEGORY_* $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return self::CATEGORY_*
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * The element properties for the line.
   *
   * @param PageElementProperties $elementProperties
   */
  public function setElementProperties(PageElementProperties $elementProperties)
  {
    $this->elementProperties = $elementProperties;
  }
  /**
   * @return PageElementProperties
   */
  public function getElementProperties()
  {
    return $this->elementProperties;
  }
  /**
   * The category of the line to be created. *Deprecated*: use `category`
   * instead. The exact line type created is determined based on the category
   * and how it's routed to connect to other page elements. If you specify both
   * a `category` and a `line_category`, the `category` takes precedence.
   *
   * Accepted values: STRAIGHT, BENT, CURVED
   *
   * @deprecated
   * @param self::LINE_CATEGORY_* $lineCategory
   */
  public function setLineCategory($lineCategory)
  {
    $this->lineCategory = $lineCategory;
  }
  /**
   * @deprecated
   * @return self::LINE_CATEGORY_*
   */
  public function getLineCategory()
  {
    return $this->lineCategory;
  }
  /**
   * A user-supplied object ID. If you specify an ID, it must be unique among
   * all pages and page elements in the presentation. The ID must start with an
   * alphanumeric character or an underscore (matches regex `[a-zA-Z0-9_]`);
   * remaining characters may include those as well as a hyphen or colon
   * (matches regex `[a-zA-Z0-9_-:]`). The length of the ID must not be less
   * than 5 or greater than 50. If you don't specify an ID, a unique one is
   * generated.
   *
   * @param string $objectId
   */
  public function setObjectId($objectId)
  {
    $this->objectId = $objectId;
  }
  /**
   * @return string
   */
  public function getObjectId()
  {
    return $this->objectId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateLineRequest::class, 'Google_Service_Slides_CreateLineRequest');
