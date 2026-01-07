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

class AccessApproval extends \Google\Collection
{
  protected $collection_key = 'tickets';
  /**
   * Justification for data access based on justification enums.
   *
   * @var string[]
   */
  public $justificationReason;
  /**
   * Office location of Google staff requesting access such as "US".
   *
   * @var string
   */
  public $officeLocation;
  /**
   * Products within scope of the Access Approvals request.
   *
   * @var string[]
   */
  public $products;
  /**
   * ID of the Access Approvals request. This is a helpful field when requesting
   * support from Google.
   *
   * @var string
   */
  public $requestId;
  /**
   * Scope of access, also known as a resource. This is further narrowed down by
   * the product field.
   *
   * @var string
   */
  public $scope;
  protected $ticketsType = SupportTicket::class;
  protected $ticketsDataType = 'array';

  /**
   * Justification for data access based on justification enums.
   *
   * @param string[] $justificationReason
   */
  public function setJustificationReason($justificationReason)
  {
    $this->justificationReason = $justificationReason;
  }
  /**
   * @return string[]
   */
  public function getJustificationReason()
  {
    return $this->justificationReason;
  }
  /**
   * Office location of Google staff requesting access such as "US".
   *
   * @param string $officeLocation
   */
  public function setOfficeLocation($officeLocation)
  {
    $this->officeLocation = $officeLocation;
  }
  /**
   * @return string
   */
  public function getOfficeLocation()
  {
    return $this->officeLocation;
  }
  /**
   * Products within scope of the Access Approvals request.
   *
   * @param string[] $products
   */
  public function setProducts($products)
  {
    $this->products = $products;
  }
  /**
   * @return string[]
   */
  public function getProducts()
  {
    return $this->products;
  }
  /**
   * ID of the Access Approvals request. This is a helpful field when requesting
   * support from Google.
   *
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
  /**
   * Scope of access, also known as a resource. This is further narrowed down by
   * the product field.
   *
   * @param string $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return string
   */
  public function getScope()
  {
    return $this->scope;
  }
  /**
   * Support tickets related to this Access Approvals request. Populated if
   * there is an associated case number.
   *
   * @param SupportTicket[] $tickets
   */
  public function setTickets($tickets)
  {
    $this->tickets = $tickets;
  }
  /**
   * @return SupportTicket[]
   */
  public function getTickets()
  {
    return $this->tickets;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccessApproval::class, 'Google_Service_AlertCenter_AccessApproval');
