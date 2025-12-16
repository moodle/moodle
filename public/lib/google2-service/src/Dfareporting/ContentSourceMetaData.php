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

class ContentSourceMetaData extends \Google\Collection
{
  protected $collection_key = 'fieldNames';
  /**
   * Output only. The charset of the content source.
   *
   * @var string
   */
  public $charset;
  /**
   * Output only. The list of column names in the content source.
   *
   * @var string[]
   */
  public $fieldNames;
  /**
   * Output only. The number of rows in the content source.
   *
   * @var int
   */
  public $rowNumber;
  /**
   * Output only. The separator of the content source.
   *
   * @var string
   */
  public $separator;

  /**
   * Output only. The charset of the content source.
   *
   * @param string $charset
   */
  public function setCharset($charset)
  {
    $this->charset = $charset;
  }
  /**
   * @return string
   */
  public function getCharset()
  {
    return $this->charset;
  }
  /**
   * Output only. The list of column names in the content source.
   *
   * @param string[] $fieldNames
   */
  public function setFieldNames($fieldNames)
  {
    $this->fieldNames = $fieldNames;
  }
  /**
   * @return string[]
   */
  public function getFieldNames()
  {
    return $this->fieldNames;
  }
  /**
   * Output only. The number of rows in the content source.
   *
   * @param int $rowNumber
   */
  public function setRowNumber($rowNumber)
  {
    $this->rowNumber = $rowNumber;
  }
  /**
   * @return int
   */
  public function getRowNumber()
  {
    return $this->rowNumber;
  }
  /**
   * Output only. The separator of the content source.
   *
   * @param string $separator
   */
  public function setSeparator($separator)
  {
    $this->separator = $separator;
  }
  /**
   * @return string
   */
  public function getSeparator()
  {
    return $this->separator;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContentSourceMetaData::class, 'Google_Service_Dfareporting_ContentSourceMetaData');
