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

namespace Google\Service\AuthorizedBuyersMarketplace;

class SendRfpRequest extends \Google\Collection
{
  protected $collection_key = 'buyerContacts';
  protected $buyerContactsType = Contact::class;
  protected $buyerContactsDataType = 'array';
  /**
   * If the current buyer is sending the RFP on behalf of its client, use this
   * field to specify the name of the client in the format:
   * `buyers/{accountId}/clients/{clientAccountid}`.
   *
   * @var string
   */
  public $client;
  /**
   * Required. The display name of the proposal being created by this RFP.
   *
   * @var string
   */
  public $displayName;
  protected $estimatedGrossSpendType = Money::class;
  protected $estimatedGrossSpendDataType = '';
  /**
   * Required. Proposed flight end time of the RFP. A timestamp in RFC3339 UTC
   * "Zulu" format. Note that the specified value will be truncated to a
   * granularity of one second.
   *
   * @var string
   */
  public $flightEndTime;
  /**
   * Required. Proposed flight start time of the RFP. A timestamp in RFC3339 UTC
   * "Zulu" format. Note that the specified value will be truncated to a
   * granularity of one second.
   *
   * @var string
   */
  public $flightStartTime;
  protected $geoTargetingType = CriteriaTargeting::class;
  protected $geoTargetingDataType = '';
  protected $inventorySizeTargetingType = InventorySizeTargeting::class;
  protected $inventorySizeTargetingDataType = '';
  /**
   * A message that is sent to the publisher. Maximum length is 1024 characters.
   *
   * @var string
   */
  public $note;
  protected $preferredDealTermsType = PreferredDealTerms::class;
  protected $preferredDealTermsDataType = '';
  protected $programmaticGuaranteedTermsType = ProgrammaticGuaranteedTerms::class;
  protected $programmaticGuaranteedTermsDataType = '';
  /**
   * Required. The profile of the publisher who will receive this RFP in the
   * format: `buyers/{accountId}/publisherProfiles/{publisherProfileId}`.
   *
   * @var string
   */
  public $publisherProfile;

  /**
   * Contact information for the buyer.
   *
   * @param Contact[] $buyerContacts
   */
  public function setBuyerContacts($buyerContacts)
  {
    $this->buyerContacts = $buyerContacts;
  }
  /**
   * @return Contact[]
   */
  public function getBuyerContacts()
  {
    return $this->buyerContacts;
  }
  /**
   * If the current buyer is sending the RFP on behalf of its client, use this
   * field to specify the name of the client in the format:
   * `buyers/{accountId}/clients/{clientAccountid}`.
   *
   * @param string $client
   */
  public function setClient($client)
  {
    $this->client = $client;
  }
  /**
   * @return string
   */
  public function getClient()
  {
    return $this->client;
  }
  /**
   * Required. The display name of the proposal being created by this RFP.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Specified by buyers in request for proposal (RFP) to notify publisher the
   * total estimated spend for the proposal. Publishers will receive this
   * information and send back proposed deals accordingly.
   *
   * @param Money $estimatedGrossSpend
   */
  public function setEstimatedGrossSpend(Money $estimatedGrossSpend)
  {
    $this->estimatedGrossSpend = $estimatedGrossSpend;
  }
  /**
   * @return Money
   */
  public function getEstimatedGrossSpend()
  {
    return $this->estimatedGrossSpend;
  }
  /**
   * Required. Proposed flight end time of the RFP. A timestamp in RFC3339 UTC
   * "Zulu" format. Note that the specified value will be truncated to a
   * granularity of one second.
   *
   * @param string $flightEndTime
   */
  public function setFlightEndTime($flightEndTime)
  {
    $this->flightEndTime = $flightEndTime;
  }
  /**
   * @return string
   */
  public function getFlightEndTime()
  {
    return $this->flightEndTime;
  }
  /**
   * Required. Proposed flight start time of the RFP. A timestamp in RFC3339 UTC
   * "Zulu" format. Note that the specified value will be truncated to a
   * granularity of one second.
   *
   * @param string $flightStartTime
   */
  public function setFlightStartTime($flightStartTime)
  {
    $this->flightStartTime = $flightStartTime;
  }
  /**
   * @return string
   */
  public function getFlightStartTime()
  {
    return $this->flightStartTime;
  }
  /**
   * Geo criteria IDs to be targeted. Refer to Geo tables.
   *
   * @param CriteriaTargeting $geoTargeting
   */
  public function setGeoTargeting(CriteriaTargeting $geoTargeting)
  {
    $this->geoTargeting = $geoTargeting;
  }
  /**
   * @return CriteriaTargeting
   */
  public function getGeoTargeting()
  {
    return $this->geoTargeting;
  }
  /**
   * Inventory sizes to be targeted. Only PIXEL inventory size type is
   * supported.
   *
   * @param InventorySizeTargeting $inventorySizeTargeting
   */
  public function setInventorySizeTargeting(InventorySizeTargeting $inventorySizeTargeting)
  {
    $this->inventorySizeTargeting = $inventorySizeTargeting;
  }
  /**
   * @return InventorySizeTargeting
   */
  public function getInventorySizeTargeting()
  {
    return $this->inventorySizeTargeting;
  }
  /**
   * A message that is sent to the publisher. Maximum length is 1024 characters.
   *
   * @param string $note
   */
  public function setNote($note)
  {
    $this->note = $note;
  }
  /**
   * @return string
   */
  public function getNote()
  {
    return $this->note;
  }
  /**
   * The terms for preferred deals.
   *
   * @param PreferredDealTerms $preferredDealTerms
   */
  public function setPreferredDealTerms(PreferredDealTerms $preferredDealTerms)
  {
    $this->preferredDealTerms = $preferredDealTerms;
  }
  /**
   * @return PreferredDealTerms
   */
  public function getPreferredDealTerms()
  {
    return $this->preferredDealTerms;
  }
  /**
   * The terms for programmatic guaranteed deals.
   *
   * @param ProgrammaticGuaranteedTerms $programmaticGuaranteedTerms
   */
  public function setProgrammaticGuaranteedTerms(ProgrammaticGuaranteedTerms $programmaticGuaranteedTerms)
  {
    $this->programmaticGuaranteedTerms = $programmaticGuaranteedTerms;
  }
  /**
   * @return ProgrammaticGuaranteedTerms
   */
  public function getProgrammaticGuaranteedTerms()
  {
    return $this->programmaticGuaranteedTerms;
  }
  /**
   * Required. The profile of the publisher who will receive this RFP in the
   * format: `buyers/{accountId}/publisherProfiles/{publisherProfileId}`.
   *
   * @param string $publisherProfile
   */
  public function setPublisherProfile($publisherProfile)
  {
    $this->publisherProfile = $publisherProfile;
  }
  /**
   * @return string
   */
  public function getPublisherProfile()
  {
    return $this->publisherProfile;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SendRfpRequest::class, 'Google_Service_AuthorizedBuyersMarketplace_SendRfpRequest');
