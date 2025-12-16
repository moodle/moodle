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

class CreateTableRequest extends \Google\Model
{
  /**
   * Number of columns in the table.
   *
   * @var int
   */
  public $columns;
  protected $elementPropertiesType = PageElementProperties::class;
  protected $elementPropertiesDataType = '';
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
   * Number of rows in the table.
   *
   * @var int
   */
  public $rows;

  /**
   * Number of columns in the table.
   *
   * @param int $columns
   */
  public function setColumns($columns)
  {
    $this->columns = $columns;
  }
  /**
   * @return int
   */
  public function getColumns()
  {
    return $this->columns;
  }
  /**
   * The element properties for the table. The table will be created at the
   * provided size, subject to a minimum size. If no size is provided, the table
   * will be automatically sized. Table transforms must have a scale of 1 and no
   * shear components. If no transform is provided, the table will be centered
   * on the page.
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
  /**
   * Number of rows in the table.
   *
   * @param int $rows
   */
  public function setRows($rows)
  {
    $this->rows = $rows;
  }
  /**
   * @return int
   */
  public function getRows()
  {
    return $this->rows;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateTableRequest::class, 'Google_Service_Slides_CreateTableRequest');
