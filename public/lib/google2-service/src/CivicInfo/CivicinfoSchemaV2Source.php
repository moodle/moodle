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

namespace Google\Service\CivicInfo;

class CivicinfoSchemaV2Source extends \Google\Model
{
  /**
   * The name of the data source.
   *
   * @var string
   */
  public $name;
  /**
   * Whether this data comes from an official government source.
   *
   * @var bool
   */
  public $official;

  /**
   * The name of the data source.
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
   * Whether this data comes from an official government source.
   *
   * @param bool $official
   */
  public function setOfficial($official)
  {
    $this->official = $official;
  }
  /**
   * @return bool
   */
  public function getOfficial()
  {
    return $this->official;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CivicinfoSchemaV2Source::class, 'Google_Service_CivicInfo_CivicinfoSchemaV2Source');
