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

namespace Google\Service\Spanner;

class Session extends \Google\Model
{
  /**
   * Output only. The approximate timestamp when the session is last used. It's
   * typically earlier than the actual last use time.
   *
   * @var string
   */
  public $approximateLastUseTime;
  /**
   * Output only. The timestamp when the session is created.
   *
   * @var string
   */
  public $createTime;
  /**
   * The database role which created this session.
   *
   * @var string
   */
  public $creatorRole;
  /**
   * The labels for the session. * Label keys must be between 1 and 63
   * characters long and must conform to the following regular expression:
   * `[a-z]([-a-z0-9]*[a-z0-9])?`. * Label values must be between 0 and 63
   * characters long and must conform to the regular expression
   * `([a-z]([-a-z0-9]*[a-z0-9])?)?`. * No more than 64 labels can be associated
   * with a given session. See https://goo.gl/xmQnxf for more information on and
   * examples of labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. If `true`, specifies a multiplexed session. Use a multiplexed
   * session for multiple, concurrent operations including any combination of
   * read-only and read-write transactions. Use `sessions.create` to create
   * multiplexed sessions. Don't use BatchCreateSessions to create a multiplexed
   * session. You can't delete or list multiplexed sessions.
   *
   * @var bool
   */
  public $multiplexed;
  /**
   * Output only. The name of the session. This is always system-assigned.
   *
   * @var string
   */
  public $name;

  /**
   * Output only. The approximate timestamp when the session is last used. It's
   * typically earlier than the actual last use time.
   *
   * @param string $approximateLastUseTime
   */
  public function setApproximateLastUseTime($approximateLastUseTime)
  {
    $this->approximateLastUseTime = $approximateLastUseTime;
  }
  /**
   * @return string
   */
  public function getApproximateLastUseTime()
  {
    return $this->approximateLastUseTime;
  }
  /**
   * Output only. The timestamp when the session is created.
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
   * The database role which created this session.
   *
   * @param string $creatorRole
   */
  public function setCreatorRole($creatorRole)
  {
    $this->creatorRole = $creatorRole;
  }
  /**
   * @return string
   */
  public function getCreatorRole()
  {
    return $this->creatorRole;
  }
  /**
   * The labels for the session. * Label keys must be between 1 and 63
   * characters long and must conform to the following regular expression:
   * `[a-z]([-a-z0-9]*[a-z0-9])?`. * Label values must be between 0 and 63
   * characters long and must conform to the regular expression
   * `([a-z]([-a-z0-9]*[a-z0-9])?)?`. * No more than 64 labels can be associated
   * with a given session. See https://goo.gl/xmQnxf for more information on and
   * examples of labels.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Optional. If `true`, specifies a multiplexed session. Use a multiplexed
   * session for multiple, concurrent operations including any combination of
   * read-only and read-write transactions. Use `sessions.create` to create
   * multiplexed sessions. Don't use BatchCreateSessions to create a multiplexed
   * session. You can't delete or list multiplexed sessions.
   *
   * @param bool $multiplexed
   */
  public function setMultiplexed($multiplexed)
  {
    $this->multiplexed = $multiplexed;
  }
  /**
   * @return bool
   */
  public function getMultiplexed()
  {
    return $this->multiplexed;
  }
  /**
   * Output only. The name of the session. This is always system-assigned.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Session::class, 'Google_Service_Spanner_Session');
