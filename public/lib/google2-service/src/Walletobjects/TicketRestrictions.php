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

namespace Google\Service\Walletobjects;

class TicketRestrictions extends \Google\Model
{
  protected $otherRestrictionsType = LocalizedString::class;
  protected $otherRestrictionsDataType = '';
  protected $routeRestrictionsType = LocalizedString::class;
  protected $routeRestrictionsDataType = '';
  protected $routeRestrictionsDetailsType = LocalizedString::class;
  protected $routeRestrictionsDetailsDataType = '';
  protected $timeRestrictionsType = LocalizedString::class;
  protected $timeRestrictionsDataType = '';

  /**
   * Extra restrictions that don't fall under the "route" or "time" categories.
   *
   * @param LocalizedString $otherRestrictions
   */
  public function setOtherRestrictions(LocalizedString $otherRestrictions)
  {
    $this->otherRestrictions = $otherRestrictions;
  }
  /**
   * @return LocalizedString
   */
  public function getOtherRestrictions()
  {
    return $this->otherRestrictions;
  }
  /**
   * Restrictions about routes that may be taken. For example, this may be the
   * string "Reserved CrossCountry trains only".
   *
   * @param LocalizedString $routeRestrictions
   */
  public function setRouteRestrictions(LocalizedString $routeRestrictions)
  {
    $this->routeRestrictions = $routeRestrictions;
  }
  /**
   * @return LocalizedString
   */
  public function getRouteRestrictions()
  {
    return $this->routeRestrictions;
  }
  /**
   * More details about the above `routeRestrictions`.
   *
   * @param LocalizedString $routeRestrictionsDetails
   */
  public function setRouteRestrictionsDetails(LocalizedString $routeRestrictionsDetails)
  {
    $this->routeRestrictionsDetails = $routeRestrictionsDetails;
  }
  /**
   * @return LocalizedString
   */
  public function getRouteRestrictionsDetails()
  {
    return $this->routeRestrictionsDetails;
  }
  /**
   * Restrictions about times this ticket may be used.
   *
   * @param LocalizedString $timeRestrictions
   */
  public function setTimeRestrictions(LocalizedString $timeRestrictions)
  {
    $this->timeRestrictions = $timeRestrictions;
  }
  /**
   * @return LocalizedString
   */
  public function getTimeRestrictions()
  {
    return $this->timeRestrictions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TicketRestrictions::class, 'Google_Service_Walletobjects_TicketRestrictions');
