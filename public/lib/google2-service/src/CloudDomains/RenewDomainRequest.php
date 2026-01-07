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

namespace Google\Service\CloudDomains;

class RenewDomainRequest extends \Google\Model
{
  /**
   * Optional. When true, only validation is performed, without actually
   * renewing the domain. For more information, see [Request validation](https:/
   * /cloud.google.com/apis/design/design_patterns#request_validation)
   *
   * @var bool
   */
  public $validateOnly;
  protected $yearlyPriceType = Money::class;
  protected $yearlyPriceDataType = '';

  /**
   * Optional. When true, only validation is performed, without actually
   * renewing the domain. For more information, see [Request validation](https:/
   * /cloud.google.com/apis/design/design_patterns#request_validation)
   *
   * @param bool $validateOnly
   */
  public function setValidateOnly($validateOnly)
  {
    $this->validateOnly = $validateOnly;
  }
  /**
   * @return bool
   */
  public function getValidateOnly()
  {
    return $this->validateOnly;
  }
  /**
   * Required. Acknowledgement of the price to renew the domain for one year. To
   * get the price, see [Cloud Domains
   * pricing](https://cloud.google.com/domains/pricing). If not provided, the
   * expected price is returned in the error message.
   *
   * @param Money $yearlyPrice
   */
  public function setYearlyPrice(Money $yearlyPrice)
  {
    $this->yearlyPrice = $yearlyPrice;
  }
  /**
   * @return Money
   */
  public function getYearlyPrice()
  {
    return $this->yearlyPrice;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RenewDomainRequest::class, 'Google_Service_CloudDomains_RenewDomainRequest');
