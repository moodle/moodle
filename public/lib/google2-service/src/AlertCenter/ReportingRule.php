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

namespace Google\Service\AlertCenter;

class ReportingRule extends \Google\Model
{
  /**
   * Any other associated alert details, for example, AlertConfiguration.
   *
   * @var string
   */
  public $alertDetails;
  /**
   * Rule name
   *
   * @var string
   */
  public $name;
  /**
   * Alert Rule query Sample Query query { condition { filter {
   * expected_application_id: 777491262838 expected_event_name:
   * "indexable_content_change" filter_op: IN } } conjunction_operator: OR }
   *
   * @var string
   */
  public $query;

  /**
   * Any other associated alert details, for example, AlertConfiguration.
   *
   * @param string $alertDetails
   */
  public function setAlertDetails($alertDetails)
  {
    $this->alertDetails = $alertDetails;
  }
  /**
   * @return string
   */
  public function getAlertDetails()
  {
    return $this->alertDetails;
  }
  /**
   * Rule name
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
   * Alert Rule query Sample Query query { condition { filter {
   * expected_application_id: 777491262838 expected_event_name:
   * "indexable_content_change" filter_op: IN } } conjunction_operator: OR }
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportingRule::class, 'Google_Service_AlertCenter_ReportingRule');
