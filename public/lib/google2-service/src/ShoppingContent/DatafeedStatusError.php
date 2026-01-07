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

namespace Google\Service\ShoppingContent;

class DatafeedStatusError extends \Google\Collection
{
  protected $collection_key = 'examples';
  /**
   * The code of the error, for example, "validation/invalid_value".
   *
   * @var string
   */
  public $code;
  /**
   * The number of occurrences of the error in the feed.
   *
   * @var string
   */
  public $count;
  protected $examplesType = DatafeedStatusExample::class;
  protected $examplesDataType = 'array';
  /**
   * The error message, for example, "Invalid price".
   *
   * @var string
   */
  public $message;

  /**
   * The code of the error, for example, "validation/invalid_value".
   *
   * @param string $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * The number of occurrences of the error in the feed.
   *
   * @param string $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return string
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * A list of example occurrences of the error, grouped by product.
   *
   * @param DatafeedStatusExample[] $examples
   */
  public function setExamples($examples)
  {
    $this->examples = $examples;
  }
  /**
   * @return DatafeedStatusExample[]
   */
  public function getExamples()
  {
    return $this->examples;
  }
  /**
   * The error message, for example, "Invalid price".
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatafeedStatusError::class, 'Google_Service_ShoppingContent_DatafeedStatusError');
