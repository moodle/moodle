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

namespace Google\Service\ServiceUsage;

class ServiceValue extends \Google\Collection
{
  protected $collection_key = 'tos';
  /**
   * @var string
   */
  public $dnsAddress;
  /**
   * @var string
   */
  public $name;
  /**
   * @var string
   */
  public $pricingLink;
  protected $tosType = TermsOfService::class;
  protected $tosDataType = 'array';

  /**
   * @param string
   */
  public function setDnsAddress($dnsAddress)
  {
    $this->dnsAddress = $dnsAddress;
  }
  /**
   * @return string
   */
  public function getDnsAddress()
  {
    return $this->dnsAddress;
  }
  /**
   * @param string
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
   * @param string
   */
  public function setPricingLink($pricingLink)
  {
    $this->pricingLink = $pricingLink;
  }
  /**
   * @return string
   */
  public function getPricingLink()
  {
    return $this->pricingLink;
  }
  /**
   * @param TermsOfService[]
   */
  public function setTos($tos)
  {
    $this->tos = $tos;
  }
  /**
   * @return TermsOfService[]
   */
  public function getTos()
  {
    return $this->tos;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceValue::class, 'Google_Service_ServiceUsage_ServiceValue');
