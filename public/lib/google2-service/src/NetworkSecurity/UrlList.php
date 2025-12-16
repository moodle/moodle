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

namespace Google\Service\NetworkSecurity;

class UrlList extends \Google\Collection
{
  protected $collection_key = 'values';
  /**
   * Output only. Time when the security policy was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Free-text description of the resource.
   *
   * @var string
   */
  public $description;
  /**
   * Required. Name of the resource provided by the user. Name is of the form
   * projects/{project}/locations/{location}/urlLists/{url_list} url_list should
   * match the pattern:(^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$).
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Time when the security policy was updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Required. FQDNs and URLs.
   *
   * @var string[]
   */
  public $values;

  /**
   * Output only. Time when the security policy was created.
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
   * Optional. Free-text description of the resource.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. Name of the resource provided by the user. Name is of the form
   * projects/{project}/locations/{location}/urlLists/{url_list} url_list should
   * match the pattern:(^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$).
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
   * Output only. Time when the security policy was updated.
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
   * Required. FQDNs and URLs.
   *
   * @param string[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return string[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UrlList::class, 'Google_Service_NetworkSecurity_UrlList');
