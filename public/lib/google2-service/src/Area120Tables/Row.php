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

class Row extends \Google\Model
{
  /**
   * Time when the row was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * The resource name of the row. Row names have the form
   * `tables/{table}/rows/{row}`. The name is ignored when creating a row.
   *
   * @var string
   */
  public $name;
  /**
   * Time when the row was last updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * The values of the row. This is a map of column key to value. Key is user
   * entered name(default) or the internal column id based on the view in the
   * request.
   *
   * @var array[]
   */
  public $values;

  /**
   * Time when the row was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The resource name of the row. Row names have the form
   * `tables/{table}/rows/{row}`. The name is ignored when creating a row.
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
  /**
   * Time when the row was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * The values of the row. This is a map of column key to value. Key is user
   * entered name(default) or the internal column id based on the view in the
   * request.
   *
   * @param array[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return array[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Row::class, 'Google_Service_Area120Tables_Row');
