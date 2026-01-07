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

namespace Google\Service\Firestore;

class WriteResult extends \Google\Collection
{
  protected $collection_key = 'transformResults';
  protected $transformResultsType = Value::class;
  protected $transformResultsDataType = 'array';
  /**
   * The last update time of the document after applying the write. Not set
   * after a `delete`. If the write did not actually change the document, this
   * will be the previous update_time.
   *
   * @var string
   */
  public $updateTime;

  /**
   * The results of applying each DocumentTransform.FieldTransform, in the same
   * order.
   *
   * @param Value[] $transformResults
   */
  public function setTransformResults($transformResults)
  {
    $this->transformResults = $transformResults;
  }
  /**
   * @return Value[]
   */
  public function getTransformResults()
  {
    return $this->transformResults;
  }
  /**
   * The last update time of the document after applying the write. Not set
   * after a `delete`. If the write did not actually change the document, this
   * will be the previous update_time.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WriteResult::class, 'Google_Service_Firestore_WriteResult');
