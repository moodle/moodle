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

namespace Google\Service\AdExchangeBuyerII;

class Proposal extends \Google\Collection
{
  /**
   * A placeholder for an undefined buyer/seller role.
   */
  public const LAST_UPDATER_OR_COMMENTOR_ROLE_BUYER_SELLER_ROLE_UNSPECIFIED = 'BUYER_SELLER_ROLE_UNSPECIFIED';
  /**
   * Specifies the role as buyer.
   */
  public const LAST_UPDATER_OR_COMMENTOR_ROLE_BUYER = 'BUYER';
  /**
   * Specifies the role as seller.
   */
  public const LAST_UPDATER_OR_COMMENTOR_ROLE_SELLER = 'SELLER';
  /**
   * A placeholder for an undefined buyer/seller role.
   */
  public const ORIGINATOR_ROLE_BUYER_SELLER_ROLE_UNSPECIFIED = 'BUYER_SELLER_ROLE_UNSPECIFIED';
  /**
   * Specifies the role as buyer.
   */
  public const ORIGINATOR_ROLE_BUYER = 'BUYER';
  /**
   * Specifies the role as seller.
   */
  public const ORIGINATOR_ROLE_SELLER = 'SELLER';
  /**
   * A placeholder for an undefined proposal state.
   */
  public const PROPOSAL_STATE_PROPOSAL_STATE_UNSPECIFIED = 'PROPOSAL_STATE_UNSPECIFIED';
  /**
   * The proposal is under negotiation or renegotiation.
   */
  public const PROPOSAL_STATE_PROPOSED = 'PROPOSED';
  /**
   * The proposal has been accepted by the buyer.
   */
  public const PROPOSAL_STATE_BUYER_ACCEPTED = 'BUYER_ACCEPTED';
  /**
   * The proposal has been accepted by the seller.
   */
  public const PROPOSAL_STATE_SELLER_ACCEPTED = 'SELLER_ACCEPTED';
  /**
   * The negotiations on the proposal were canceled and the proposal was never
   * finalized.
   */
  public const PROPOSAL_STATE_CANCELED = 'CANCELED';
  /**
   * The proposal is finalized. During renegotiation, the proposal may not be in
   * this state.
   */
  public const PROPOSAL_STATE_FINALIZED = 'FINALIZED';
  protected $collection_key = 'sellerContacts';
  protected $billedBuyerType = Buyer::class;
  protected $billedBuyerDataType = '';
  protected $buyerType = Buyer::class;
  protected $buyerDataType = '';
  protected $buyerContactsType = ContactInformation::class;
  protected $buyerContactsDataType = 'array';
  protected $buyerPrivateDataType = PrivateData::class;
  protected $buyerPrivateDataDataType = '';
  protected $dealsType = Deal::class;
  protected $dealsDataType = 'array';
  /**
   * The name for the proposal.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. True if the proposal is being renegotiated.
   *
   * @var bool
   */
  public $isRenegotiating;
  /**
   * Output only. True, if the buyside inventory setup is complete for this
   * proposal.
   *
   * @deprecated
   * @var bool
   */
  public $isSetupComplete;
  /**
   * Output only. The role of the last user that either updated the proposal or
   * left a comment.
   *
   * @var string
   */
  public $lastUpdaterOrCommentorRole;
  protected $notesType = Note::class;
  protected $notesDataType = 'array';
  /**
   * Output only. Indicates whether the buyer/seller created the proposal.
   *
   * @var string
   */
  public $originatorRole;
  /**
   * Output only. Private auction ID if this proposal is a private auction
   * proposal.
   *
   * @var string
   */
  public $privateAuctionId;
  /**
   * Output only. The unique ID of the proposal.
   *
   * @var string
   */
  public $proposalId;
  /**
   * Output only. The revision number for the proposal. Each update to the
   * proposal or the deal causes the proposal revision number to auto-increment.
   * The buyer keeps track of the last revision number they know of and pass it
   * in when making an update. If the head revision number on the server has
   * since incremented, then an ABORTED error is returned during the update
   * operation to let the buyer know that a subsequent update was made.
   *
   * @var string
   */
  public $proposalRevision;
  /**
   * Output only. The current state of the proposal.
   *
   * @var string
   */
  public $proposalState;
  protected $sellerType = Seller::class;
  protected $sellerDataType = '';
  protected $sellerContactsType = ContactInformation::class;
  protected $sellerContactsDataType = 'array';
  /**
   * Output only. The terms and conditions set by the publisher for this
   * proposal.
   *
   * @var string
   */
  public $termsAndConditions;
  /**
   * Output only. The time when the proposal was last revised.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Reference to the buyer that will get billed for this proposal.
   *
   * @param Buyer $billedBuyer
   */
  public function setBilledBuyer(Buyer $billedBuyer)
  {
    $this->billedBuyer = $billedBuyer;
  }
  /**
   * @return Buyer
   */
  public function getBilledBuyer()
  {
    return $this->billedBuyer;
  }
  /**
   * Reference to the buyer on the proposal. Note: This field may be set only
   * when creating the resource. Modifying this field while updating the
   * resource will result in an error.
   *
   * @param Buyer $buyer
   */
  public function setBuyer(Buyer $buyer)
  {
    $this->buyer = $buyer;
  }
  /**
   * @return Buyer
   */
  public function getBuyer()
  {
    return $this->buyer;
  }
  /**
   * Contact information for the buyer.
   *
   * @param ContactInformation[] $buyerContacts
   */
  public function setBuyerContacts($buyerContacts)
  {
    $this->buyerContacts = $buyerContacts;
  }
  /**
   * @return ContactInformation[]
   */
  public function getBuyerContacts()
  {
    return $this->buyerContacts;
  }
  /**
   * Private data for buyer. (hidden from seller).
   *
   * @param PrivateData $buyerPrivateData
   */
  public function setBuyerPrivateData(PrivateData $buyerPrivateData)
  {
    $this->buyerPrivateData = $buyerPrivateData;
  }
  /**
   * @return PrivateData
   */
  public function getBuyerPrivateData()
  {
    return $this->buyerPrivateData;
  }
  /**
   * The deals associated with this proposal. For Private Auction proposals
   * (whose deals have NonGuaranteedAuctionTerms), there will only be one deal.
   *
   * @param Deal[] $deals
   */
  public function setDeals($deals)
  {
    $this->deals = $deals;
  }
  /**
   * @return Deal[]
   */
  public function getDeals()
  {
    return $this->deals;
  }
  /**
   * The name for the proposal.
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
   * Output only. True if the proposal is being renegotiated.
   *
   * @param bool $isRenegotiating
   */
  public function setIsRenegotiating($isRenegotiating)
  {
    $this->isRenegotiating = $isRenegotiating;
  }
  /**
   * @return bool
   */
  public function getIsRenegotiating()
  {
    return $this->isRenegotiating;
  }
  /**
   * Output only. True, if the buyside inventory setup is complete for this
   * proposal.
   *
   * @deprecated
   * @param bool $isSetupComplete
   */
  public function setIsSetupComplete($isSetupComplete)
  {
    $this->isSetupComplete = $isSetupComplete;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getIsSetupComplete()
  {
    return $this->isSetupComplete;
  }
  /**
   * Output only. The role of the last user that either updated the proposal or
   * left a comment.
   *
   * Accepted values: BUYER_SELLER_ROLE_UNSPECIFIED, BUYER, SELLER
   *
   * @param self::LAST_UPDATER_OR_COMMENTOR_ROLE_* $lastUpdaterOrCommentorRole
   */
  public function setLastUpdaterOrCommentorRole($lastUpdaterOrCommentorRole)
  {
    $this->lastUpdaterOrCommentorRole = $lastUpdaterOrCommentorRole;
  }
  /**
   * @return self::LAST_UPDATER_OR_COMMENTOR_ROLE_*
   */
  public function getLastUpdaterOrCommentorRole()
  {
    return $this->lastUpdaterOrCommentorRole;
  }
  /**
   * Output only. The notes associated with this proposal.
   *
   * @param Note[] $notes
   */
  public function setNotes($notes)
  {
    $this->notes = $notes;
  }
  /**
   * @return Note[]
   */
  public function getNotes()
  {
    return $this->notes;
  }
  /**
   * Output only. Indicates whether the buyer/seller created the proposal.
   *
   * Accepted values: BUYER_SELLER_ROLE_UNSPECIFIED, BUYER, SELLER
   *
   * @param self::ORIGINATOR_ROLE_* $originatorRole
   */
  public function setOriginatorRole($originatorRole)
  {
    $this->originatorRole = $originatorRole;
  }
  /**
   * @return self::ORIGINATOR_ROLE_*
   */
  public function getOriginatorRole()
  {
    return $this->originatorRole;
  }
  /**
   * Output only. Private auction ID if this proposal is a private auction
   * proposal.
   *
   * @param string $privateAuctionId
   */
  public function setPrivateAuctionId($privateAuctionId)
  {
    $this->privateAuctionId = $privateAuctionId;
  }
  /**
   * @return string
   */
  public function getPrivateAuctionId()
  {
    return $this->privateAuctionId;
  }
  /**
   * Output only. The unique ID of the proposal.
   *
   * @param string $proposalId
   */
  public function setProposalId($proposalId)
  {
    $this->proposalId = $proposalId;
  }
  /**
   * @return string
   */
  public function getProposalId()
  {
    return $this->proposalId;
  }
  /**
   * Output only. The revision number for the proposal. Each update to the
   * proposal or the deal causes the proposal revision number to auto-increment.
   * The buyer keeps track of the last revision number they know of and pass it
   * in when making an update. If the head revision number on the server has
   * since incremented, then an ABORTED error is returned during the update
   * operation to let the buyer know that a subsequent update was made.
   *
   * @param string $proposalRevision
   */
  public function setProposalRevision($proposalRevision)
  {
    $this->proposalRevision = $proposalRevision;
  }
  /**
   * @return string
   */
  public function getProposalRevision()
  {
    return $this->proposalRevision;
  }
  /**
   * Output only. The current state of the proposal.
   *
   * Accepted values: PROPOSAL_STATE_UNSPECIFIED, PROPOSED, BUYER_ACCEPTED,
   * SELLER_ACCEPTED, CANCELED, FINALIZED
   *
   * @param self::PROPOSAL_STATE_* $proposalState
   */
  public function setProposalState($proposalState)
  {
    $this->proposalState = $proposalState;
  }
  /**
   * @return self::PROPOSAL_STATE_*
   */
  public function getProposalState()
  {
    return $this->proposalState;
  }
  /**
   * Reference to the seller on the proposal. Note: This field may be set only
   * when creating the resource. Modifying this field while updating the
   * resource will result in an error.
   *
   * @param Seller $seller
   */
  public function setSeller(Seller $seller)
  {
    $this->seller = $seller;
  }
  /**
   * @return Seller
   */
  public function getSeller()
  {
    return $this->seller;
  }
  /**
   * Output only. Contact information for the seller.
   *
   * @param ContactInformation[] $sellerContacts
   */
  public function setSellerContacts($sellerContacts)
  {
    $this->sellerContacts = $sellerContacts;
  }
  /**
   * @return ContactInformation[]
   */
  public function getSellerContacts()
  {
    return $this->sellerContacts;
  }
  /**
   * Output only. The terms and conditions set by the publisher for this
   * proposal.
   *
   * @param string $termsAndConditions
   */
  public function setTermsAndConditions($termsAndConditions)
  {
    $this->termsAndConditions = $termsAndConditions;
  }
  /**
   * @return string
   */
  public function getTermsAndConditions()
  {
    return $this->termsAndConditions;
  }
  /**
   * Output only. The time when the proposal was last revised.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Proposal::class, 'Google_Service_AdExchangeBuyerII_Proposal');
