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

namespace Google\Service\CloudMemorystoreforMemcached;

class MemcacheParameters extends \Google\Model
{
  /**
   * Output only. The unique ID associated with this set of parameters. Users
   * can use this id to determine if the parameters associated with the instance
   * differ from the parameters associated with the nodes. A discrepancy between
   * parameter ids can inform users that they may need to take action to apply
   * parameters on nodes.
   *
   * @var string
   */
  public $id;
  /**
   * User defined set of parameters to use in the memcached process.
   *
   * @var string[]
   */
  public $params;

  /**
   * Output only. The unique ID associated with this set of parameters. Users
   * can use this id to determine if the parameters associated with the instance
   * differ from the parameters associated with the nodes. A discrepancy between
   * parameter ids can inform users that they may need to take action to apply
   * parameters on nodes.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * User defined set of parameters to use in the memcached process.
   *
   * @param string[] $params
   */
  public function setParams($params)
  {
    $this->params = $params;
  }
  /**
   * @return string[]
   */
  public function getParams()
  {
    return $this->params;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MemcacheParameters::class, 'Google_Service_CloudMemorystoreforMemcached_MemcacheParameters');
