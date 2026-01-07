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

namespace Google\Service\Analytics;

class RemarketingAudience extends \Google\Collection
{
  protected $collection_key = 'linkedViews';
  /**
   * Account ID to which this remarketing audience belongs.
   *
   * @var string
   */
  public $accountId;
  protected $audienceDefinitionType = RemarketingAudienceAudienceDefinition::class;
  protected $audienceDefinitionDataType = '';
  /**
   * The type of audience, either SIMPLE or STATE_BASED.
   *
   * @var string
   */
  public $audienceType;
  /**
   * Time this remarketing audience was created.
   *
   * @var string
   */
  public $created;
  /**
   * The description of this remarketing audience.
   *
   * @var string
   */
  public $description;
  /**
   * Remarketing Audience ID.
   *
   * @var string
   */
  public $id;
  /**
   * Internal ID for the web property to which this remarketing audience
   * belongs.
   *
   * @var string
   */
  public $internalWebPropertyId;
  /**
   * Collection type.
   *
   * @var string
   */
  public $kind;
  protected $linkedAdAccountsType = LinkedForeignAccount::class;
  protected $linkedAdAccountsDataType = 'array';
  /**
   * The views (profiles) that this remarketing audience is linked to.
   *
   * @var string[]
   */
  public $linkedViews;
  /**
   * The name of this remarketing audience.
   *
   * @var string
   */
  public $name;
  protected $stateBasedAudienceDefinitionType = RemarketingAudienceStateBasedAudienceDefinition::class;
  protected $stateBasedAudienceDefinitionDataType = '';
  /**
   * Time this remarketing audience was last modified.
   *
   * @var string
   */
  public $updated;
  /**
   * Web property ID of the form UA-XXXXX-YY to which this remarketing audience
   * belongs.
   *
   * @var string
   */
  public $webPropertyId;

  /**
   * Account ID to which this remarketing audience belongs.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * The simple audience definition that will cause a user to be added to an
   * audience.
   *
   * @param RemarketingAudienceAudienceDefinition $audienceDefinition
   */
  public function setAudienceDefinition(RemarketingAudienceAudienceDefinition $audienceDefinition)
  {
    $this->audienceDefinition = $audienceDefinition;
  }
  /**
   * @return RemarketingAudienceAudienceDefinition
   */
  public function getAudienceDefinition()
  {
    return $this->audienceDefinition;
  }
  /**
   * The type of audience, either SIMPLE or STATE_BASED.
   *
   * @param string $audienceType
   */
  public function setAudienceType($audienceType)
  {
    $this->audienceType = $audienceType;
  }
  /**
   * @return string
   */
  public function getAudienceType()
  {
    return $this->audienceType;
  }
  /**
   * Time this remarketing audience was created.
   *
   * @param string $created
   */
  public function setCreated($created)
  {
    $this->created = $created;
  }
  /**
   * @return string
   */
  public function getCreated()
  {
    return $this->created;
  }
  /**
   * The description of this remarketing audience.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Remarketing Audience ID.
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
   * Internal ID for the web property to which this remarketing audience
   * belongs.
   *
   * @param string $internalWebPropertyId
   */
  public function setInternalWebPropertyId($internalWebPropertyId)
  {
    $this->internalWebPropertyId = $internalWebPropertyId;
  }
  /**
   * @return string
   */
  public function getInternalWebPropertyId()
  {
    return $this->internalWebPropertyId;
  }
  /**
   * Collection type.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The linked ad accounts associated with this remarketing audience. A
   * remarketing audience can have only one linkedAdAccount currently.
   *
   * @param LinkedForeignAccount[] $linkedAdAccounts
   */
  public function setLinkedAdAccounts($linkedAdAccounts)
  {
    $this->linkedAdAccounts = $linkedAdAccounts;
  }
  /**
   * @return LinkedForeignAccount[]
   */
  public function getLinkedAdAccounts()
  {
    return $this->linkedAdAccounts;
  }
  /**
   * The views (profiles) that this remarketing audience is linked to.
   *
   * @param string[] $linkedViews
   */
  public function setLinkedViews($linkedViews)
  {
    $this->linkedViews = $linkedViews;
  }
  /**
   * @return string[]
   */
  public function getLinkedViews()
  {
    return $this->linkedViews;
  }
  /**
   * The name of this remarketing audience.
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
   * A state based audience definition that will cause a user to be added or
   * removed from an audience.
   *
   * @param RemarketingAudienceStateBasedAudienceDefinition $stateBasedAudienceDefinition
   */
  public function setStateBasedAudienceDefinition(RemarketingAudienceStateBasedAudienceDefinition $stateBasedAudienceDefinition)
  {
    $this->stateBasedAudienceDefinition = $stateBasedAudienceDefinition;
  }
  /**
   * @return RemarketingAudienceStateBasedAudienceDefinition
   */
  public function getStateBasedAudienceDefinition()
  {
    return $this->stateBasedAudienceDefinition;
  }
  /**
   * Time this remarketing audience was last modified.
   *
   * @param string $updated
   */
  public function setUpdated($updated)
  {
    $this->updated = $updated;
  }
  /**
   * @return string
   */
  public function getUpdated()
  {
    return $this->updated;
  }
  /**
   * Web property ID of the form UA-XXXXX-YY to which this remarketing audience
   * belongs.
   *
   * @param string $webPropertyId
   */
  public function setWebPropertyId($webPropertyId)
  {
    $this->webPropertyId = $webPropertyId;
  }
  /**
   * @return string
   */
  public function getWebPropertyId()
  {
    return $this->webPropertyId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RemarketingAudience::class, 'Google_Service_Analytics_RemarketingAudience');
