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

class Proposal extends \Google\Collection
{
  /**
   * Default, unspecified deal type.
   */
  public const DEAL_TYPE_DEAL_TYPE_UNSPECIFIED = 'DEAL_TYPE_UNSPECIFIED';
  /**
   * Preferred deals.
   */
  public const DEAL_TYPE_PREFERRED_DEAL = 'PREFERRED_DEAL';
  /**
   * Private auction deals.
   */
  public const DEAL_TYPE_PRIVATE_AUCTION = 'PRIVATE_AUCTION';
  /**
   * Programmatic guaranteed deals.
   */
  public const DEAL_TYPE_PROGRAMMATIC_GUARANTEED = 'PROGRAMMATIC_GUARANTEED';
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
   * Unspecified proposal state
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * When a proposal is waiting for buyer to review.
   */
  public const STATE_BUYER_REVIEW_REQUESTED = 'BUYER_REVIEW_REQUESTED';
  /**
   * When the proposal is waiting for the seller to review.
   */
  public const STATE_SELLER_REVIEW_REQUESTED = 'SELLER_REVIEW_REQUESTED';
  /**
   * When the seller accepted the proposal and sent it to the buyer for review.
   */
  public const STATE_BUYER_ACCEPTANCE_REQUESTED = 'BUYER_ACCEPTANCE_REQUESTED';
  /**
   * When both buyer and seller has accepted the proposal
   */
  public const STATE_FINALIZED = 'FINALIZED';
  /**
   * When either buyer or seller has cancelled the proposal
   */
  public const STATE_TERMINATED = 'TERMINATED';
  protected $collection_key = 'sellerContacts';
  /**
   * Output only. When the client field is populated, this field refers to the
   * buyer who creates and manages the client buyer and gets billed on behalf of
   * the client buyer; when the buyer field is populated, this field is the same
   * value as buyer. Format : `buyers/{buyerAccountId}`
   *
   * @var string
   */
  public $billedBuyer;
  /**
   * Output only. Refers to a buyer in The Realtime-bidding API. Format:
   * `buyers/{buyerAccountId}`
   *
   * @var string
   */
  public $buyer;
  protected $buyerContactsType = Contact::class;
  protected $buyerContactsDataType = 'array';
  protected $buyerPrivateDataType = PrivateData::class;
  protected $buyerPrivateDataDataType = '';
  /**
   * Output only. Refers to a Client. Format:
   * `buyers/{buyerAccountId}/clients/{clientAccountid}`
   *
   * @var string
   */
  public $client;
  /**
   * Output only. Type of deal the proposal contains.
   *
   * @var string
   */
  public $dealType;
  /**
   * Output only. The descriptive name for the proposal. Maximum length of 255
   * unicode characters is allowed. Control characters are not allowed. Buyers
   * cannot update this field. Note: Not to be confused with name, which is a
   * unique identifier of the proposal.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. True if the proposal was previously finalized and is now being
   * renegotiated.
   *
   * @var bool
   */
  public $isRenegotiating;
  /**
   * Output only. The role of the last user that either updated the proposal or
   * left a comment.
   *
   * @var string
   */
  public $lastUpdaterOrCommentorRole;
  /**
   * Immutable. The name of the proposal serving as a unique identifier. Format:
   * buyers/{accountId}/proposals/{proposalId}
   *
   * @var string
   */
  public $name;
  protected $notesType = Note::class;
  protected $notesDataType = 'array';
  /**
   * Output only. Indicates whether the buyer/seller created the proposal.
   *
   * @var string
   */
  public $originatorRole;
  /**
   * Whether pausing is allowed for the proposal. This is a negotiable term
   * between buyers and publishers.
   *
   * @var bool
   */
  public $pausingConsented;
  /**
   * Output only. The revision number for the proposal. Each update to the
   * proposal or deal causes the proposal revision number to auto-increment. The
   * buyer keeps track of the last revision number they know of and pass it in
   * when making an update. If the head revision number on the server has since
   * incremented, then an ABORTED error is returned during the update operation
   * to let the buyer know that a subsequent update was made.
   *
   * @var string
   */
  public $proposalRevision;
  /**
   * Immutable. Reference to the seller on the proposal. Format:
   * `buyers/{buyerAccountId}/publisherProfiles/{publisherProfileId}` Note: This
   * field may be set only when creating the resource. Modifying this field
   * while updating the resource will result in an error.
   *
   * @var string
   */
  public $publisherProfile;
  protected $sellerContactsType = Contact::class;
  protected $sellerContactsDataType = 'array';
  /**
   * Output only. Indicates the state of the proposal.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The terms and conditions associated with this proposal.
   * Accepting a proposal implies acceptance of this field. This is created by
   * the seller, the buyer can only view it.
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
   * Output only. When the client field is populated, this field refers to the
   * buyer who creates and manages the client buyer and gets billed on behalf of
   * the client buyer; when the buyer field is populated, this field is the same
   * value as buyer. Format : `buyers/{buyerAccountId}`
   *
   * @param string $billedBuyer
   */
  public function setBilledBuyer($billedBuyer)
  {
    $this->billedBuyer = $billedBuyer;
  }
  /**
   * @return string
   */
  public function getBilledBuyer()
  {
    return $this->billedBuyer;
  }
  /**
   * Output only. Refers to a buyer in The Realtime-bidding API. Format:
   * `buyers/{buyerAccountId}`
   *
   * @param string $buyer
   */
  public function setBuyer($buyer)
  {
    $this->buyer = $buyer;
  }
  /**
   * @return string
   */
  public function getBuyer()
  {
    return $this->buyer;
  }
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
   * Buyer private data (hidden from seller).
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
   * Output only. Refers to a Client. Format:
   * `buyers/{buyerAccountId}/clients/{clientAccountid}`
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
   * Output only. Type of deal the proposal contains.
   *
   * Accepted values: DEAL_TYPE_UNSPECIFIED, PREFERRED_DEAL, PRIVATE_AUCTION,
   * PROGRAMMATIC_GUARANTEED
   *
   * @param self::DEAL_TYPE_* $dealType
   */
  public function setDealType($dealType)
  {
    $this->dealType = $dealType;
  }
  /**
   * @return self::DEAL_TYPE_*
   */
  public function getDealType()
  {
    return $this->dealType;
  }
  /**
   * Output only. The descriptive name for the proposal. Maximum length of 255
   * unicode characters is allowed. Control characters are not allowed. Buyers
   * cannot update this field. Note: Not to be confused with name, which is a
   * unique identifier of the proposal.
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
   * Output only. True if the proposal was previously finalized and is now being
   * renegotiated.
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
   * Immutable. The name of the proposal serving as a unique identifier. Format:
   * buyers/{accountId}/proposals/{proposalId}
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
   * A list of notes from the buyer and the seller attached to this proposal.
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
   * Whether pausing is allowed for the proposal. This is a negotiable term
   * between buyers and publishers.
   *
   * @param bool $pausingConsented
   */
  public function setPausingConsented($pausingConsented)
  {
    $this->pausingConsented = $pausingConsented;
  }
  /**
   * @return bool
   */
  public function getPausingConsented()
  {
    return $this->pausingConsented;
  }
  /**
   * Output only. The revision number for the proposal. Each update to the
   * proposal or deal causes the proposal revision number to auto-increment. The
   * buyer keeps track of the last revision number they know of and pass it in
   * when making an update. If the head revision number on the server has since
   * incremented, then an ABORTED error is returned during the update operation
   * to let the buyer know that a subsequent update was made.
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
   * Immutable. Reference to the seller on the proposal. Format:
   * `buyers/{buyerAccountId}/publisherProfiles/{publisherProfileId}` Note: This
   * field may be set only when creating the resource. Modifying this field
   * while updating the resource will result in an error.
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
  /**
   * Output only. Contact information for the seller.
   *
   * @param Contact[] $sellerContacts
   */
  public function setSellerContacts($sellerContacts)
  {
    $this->sellerContacts = $sellerContacts;
  }
  /**
   * @return Contact[]
   */
  public function getSellerContacts()
  {
    return $this->sellerContacts;
  }
  /**
   * Output only. Indicates the state of the proposal.
   *
   * Accepted values: STATE_UNSPECIFIED, BUYER_REVIEW_REQUESTED,
   * SELLER_REVIEW_REQUESTED, BUYER_ACCEPTANCE_REQUESTED, FINALIZED, TERMINATED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. The terms and conditions associated with this proposal.
   * Accepting a proposal implies acceptance of this field. This is created by
   * the seller, the buyer can only view it.
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
class_alias(Proposal::class, 'Google_Service_AuthorizedBuyersMarketplace_Proposal');
