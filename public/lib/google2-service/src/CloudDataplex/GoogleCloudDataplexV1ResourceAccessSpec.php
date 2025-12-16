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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1ResourceAccessSpec extends \Google\Collection
{
  protected $collection_key = 'writers';
  /**
   * Optional. The set of principals to be granted owner role on the resource.
   *
   * @var string[]
   */
  public $owners;
  /**
   * Optional. The format of strings follows the pattern followed by IAM in the
   * bindings. user:{email}, serviceAccount:{email} group:{email}. The set of
   * principals to be granted reader role on the resource.
   *
   * @var string[]
   */
  public $readers;
  /**
   * Optional. The set of principals to be granted writer role on the resource.
   *
   * @var string[]
   */
  public $writers;

  /**
   * Optional. The set of principals to be granted owner role on the resource.
   *
   * @param string[] $owners
   */
  public function setOwners($owners)
  {
    $this->owners = $owners;
  }
  /**
   * @return string[]
   */
  public function getOwners()
  {
    return $this->owners;
  }
  /**
   * Optional. The format of strings follows the pattern followed by IAM in the
   * bindings. user:{email}, serviceAccount:{email} group:{email}. The set of
   * principals to be granted reader role on the resource.
   *
   * @param string[] $readers
   */
  public function setReaders($readers)
  {
    $this->readers = $readers;
  }
  /**
   * @return string[]
   */
  public function getReaders()
  {
    return $this->readers;
  }
  /**
   * Optional. The set of principals to be granted writer role on the resource.
   *
   * @param string[] $writers
   */
  public function setWriters($writers)
  {
    $this->writers = $writers;
  }
  /**
   * @return string[]
   */
  public function getWriters()
  {
    return $this->writers;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1ResourceAccessSpec::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1ResourceAccessSpec');
