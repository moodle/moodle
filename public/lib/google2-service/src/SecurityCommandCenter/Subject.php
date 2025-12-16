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

namespace Google\Service\SecurityCommandCenter;

class Subject extends \Google\Model
{
  /**
   * Authentication is not specified.
   */
  public const KIND_AUTH_TYPE_UNSPECIFIED = 'AUTH_TYPE_UNSPECIFIED';
  /**
   * User with valid certificate.
   */
  public const KIND_USER = 'USER';
  /**
   * Users managed by Kubernetes API with credentials stored as secrets.
   */
  public const KIND_SERVICEACCOUNT = 'SERVICEACCOUNT';
  /**
   * Collection of users.
   */
  public const KIND_GROUP = 'GROUP';
  /**
   * Authentication type for the subject.
   *
   * @var string
   */
  public $kind;
  /**
   * Name for the subject.
   *
   * @var string
   */
  public $name;
  /**
   * Namespace for the subject.
   *
   * @var string
   */
  public $ns;

  /**
   * Authentication type for the subject.
   *
   * Accepted values: AUTH_TYPE_UNSPECIFIED, USER, SERVICEACCOUNT, GROUP
   *
   * @param self::KIND_* $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return self::KIND_*
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Name for the subject.
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
   * Namespace for the subject.
   *
   * @param string $ns
   */
  public function setNs($ns)
  {
    $this->ns = $ns;
  }
  /**
   * @return string
   */
  public function getNs()
  {
    return $this->ns;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Subject::class, 'Google_Service_SecurityCommandCenter_Subject');
