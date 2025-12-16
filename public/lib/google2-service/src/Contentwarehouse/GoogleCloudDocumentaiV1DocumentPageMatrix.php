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

namespace Google\Service\Contentwarehouse;

class GoogleCloudDocumentaiV1DocumentPageMatrix extends \Google\Model
{
  /**
   * Number of columns in the matrix.
   *
   * @var int
   */
  public $cols;
  /**
   * The matrix data.
   *
   * @var string
   */
  public $data;
  /**
   * Number of rows in the matrix.
   *
   * @var int
   */
  public $rows;
  /**
   * This encodes information about what data type the matrix uses. For example,
   * 0 (CV_8U) is an unsigned 8-bit image. For the full list of OpenCV primitive
   * data types, please refer to
   * https://docs.opencv.org/4.3.0/d1/d1b/group__core__hal__interface.html
   *
   * @var int
   */
  public $type;

  /**
   * Number of columns in the matrix.
   *
   * @param int $cols
   */
  public function setCols($cols)
  {
    $this->cols = $cols;
  }
  /**
   * @return int
   */
  public function getCols()
  {
    return $this->cols;
  }
  /**
   * The matrix data.
   *
   * @param string $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return string
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * Number of rows in the matrix.
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
  /**
   * This encodes information about what data type the matrix uses. For example,
   * 0 (CV_8U) is an unsigned 8-bit image. For the full list of OpenCV primitive
   * data types, please refer to
   * https://docs.opencv.org/4.3.0/d1/d1b/group__core__hal__interface.html
   *
   * @param int $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return int
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1DocumentPageMatrix::class, 'Google_Service_Contentwarehouse_GoogleCloudDocumentaiV1DocumentPageMatrix');
