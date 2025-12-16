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

class Count extends \Google\Model
{
  /**
   * Optional. Optional constraint on the maximum number of documents to count.
   * This provides a way to set an upper bound on the number of documents to
   * scan, limiting latency, and cost. Unspecified is interpreted as no bound.
   * High-Level Example: ``` AGGREGATE COUNT_UP_TO(1000) OVER ( SELECT * FROM k
   * ); ``` Requires: * Must be greater than zero when present.
   *
   * @var string
   */
  public $upTo;

  /**
   * Optional. Optional constraint on the maximum number of documents to count.
   * This provides a way to set an upper bound on the number of documents to
   * scan, limiting latency, and cost. Unspecified is interpreted as no bound.
   * High-Level Example: ``` AGGREGATE COUNT_UP_TO(1000) OVER ( SELECT * FROM k
   * ); ``` Requires: * Must be greater than zero when present.
   *
   * @param string $upTo
   */
  public function setUpTo($upTo)
  {
    $this->upTo = $upTo;
  }
  /**
   * @return string
   */
  public function getUpTo()
  {
    return $this->upTo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Count::class, 'Google_Service_Firestore_Count');
