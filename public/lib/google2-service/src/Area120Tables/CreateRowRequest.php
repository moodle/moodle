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

namespace Google\Service\Area120Tables;

class CreateRowRequest extends \Google\Model
{
  /**
   * Defaults to user entered text.
   */
  public const VIEW_VIEW_UNSPECIFIED = 'VIEW_UNSPECIFIED';
  /**
   * Uses internally generated column id to identify values.
   */
  public const VIEW_COLUMN_ID_VIEW = 'COLUMN_ID_VIEW';
  /**
   * Required. The parent table where this row will be created. Format:
   * tables/{table}
   *
   * @var string
   */
  public $parent;
  protected $rowType = Row::class;
  protected $rowDataType = '';
  /**
   * Optional. Column key to use for values in the row. Defaults to user entered
   * name.
   *
   * @var string
   */
  public $view;

  /**
   * Required. The parent table where this row will be created. Format:
   * tables/{table}
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * Required. The row to create.
   *
   * @param Row $row
   */
  public function setRow(Row $row)
  {
    $this->row = $row;
  }
  /**
   * @return Row
   */
  public function getRow()
  {
    return $this->row;
  }
  /**
   * Optional. Column key to use for values in the row. Defaults to user entered
   * name.
   *
   * Accepted values: VIEW_UNSPECIFIED, COLUMN_ID_VIEW
   *
   * @param self::VIEW_* $view
   */
  public function setView($view)
  {
    $this->view = $view;
  }
  /**
   * @return self::VIEW_*
   */
  public function getView()
  {
    return $this->view;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateRowRequest::class, 'Google_Service_Area120Tables_CreateRowRequest');
