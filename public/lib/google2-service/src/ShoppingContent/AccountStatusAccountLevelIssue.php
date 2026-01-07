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

class AccountStatusAccountLevelIssue extends \Google\Model
{
  /**
   * Country for which this issue is reported.
   *
   * @var string
   */
  public $country;
  /**
   * The destination the issue applies to. If this field is empty then the issue
   * applies to all available destinations.
   *
   * @var string
   */
  public $destination;
  /**
   * Additional details about the issue.
   *
   * @var string
   */
  public $detail;
  /**
   * The URL of a web page to help resolving this issue.
   *
   * @var string
   */
  public $documentation;
  /**
   * Issue identifier.
   *
   * @var string
   */
  public $id;
  /**
   * Severity of the issue. Acceptable values are: - "`critical`" - "`error`" -
   * "`suggestion`"
   *
   * @var string
   */
  public $severity;
  /**
   * Short description of the issue.
   *
   * @var string
   */
  public $title;

  /**
   * Country for which this issue is reported.
   *
   * @param string $country
   */
  public function setCountry($country)
  {
    $this->country = $country;
  }
  /**
   * @return string
   */
  public function getCountry()
  {
    return $this->country;
  }
  /**
   * The destination the issue applies to. If this field is empty then the issue
   * applies to all available destinations.
   *
   * @param string $destination
   */
  public function setDestination($destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return string
   */
  public function getDestination()
  {
    return $this->destination;
  }
  /**
   * Additional details about the issue.
   *
   * @param string $detail
   */
  public function setDetail($detail)
  {
    $this->detail = $detail;
  }
  /**
   * @return string
   */
  public function getDetail()
  {
    return $this->detail;
  }
  /**
   * The URL of a web page to help resolving this issue.
   *
   * @param string $documentation
   */
  public function setDocumentation($documentation)
  {
    $this->documentation = $documentation;
  }
  /**
   * @return string
   */
  public function getDocumentation()
  {
    return $this->documentation;
  }
  /**
   * Issue identifier.
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
   * Severity of the issue. Acceptable values are: - "`critical`" - "`error`" -
   * "`suggestion`"
   *
   * @param string $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return string
   */
  public function getSeverity()
  {
    return $this->severity;
  }
  /**
   * Short description of the issue.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountStatusAccountLevelIssue::class, 'Google_Service_ShoppingContent_AccountStatusAccountLevelIssue');
