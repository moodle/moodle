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

namespace Google\Service\DatabaseMigrationService;

class SqlAclEntry extends \Google\Model
{
  /**
   * The time when this access control entry expires in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example:
   * `2012-11-15T16:19:00.094Z`.
   *
   * @var string
   */
  public $expireTime;
  /**
   * A label to identify this entry.
   *
   * @var string
   */
  public $label;
  /**
   * Input only. The time-to-leave of this access control entry.
   *
   * @var string
   */
  public $ttl;
  /**
   * The allowlisted value for the access control list.
   *
   * @var string
   */
  public $value;

  /**
   * The time when this access control entry expires in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example:
   * `2012-11-15T16:19:00.094Z`.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * A label to identify this entry.
   *
   * @param string $label
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }
  /**
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }
  /**
   * Input only. The time-to-leave of this access control entry.
   *
   * @param string $ttl
   */
  public function setTtl($ttl)
  {
    $this->ttl = $ttl;
  }
  /**
   * @return string
   */
  public function getTtl()
  {
    return $this->ttl;
  }
  /**
   * The allowlisted value for the access control list.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SqlAclEntry::class, 'Google_Service_DatabaseMigrationService_SqlAclEntry');
