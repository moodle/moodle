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

class Goal extends \Google\Model
{
  /**
   * Account ID to which this goal belongs.
   *
   * @var string
   */
  public $accountId;
  /**
   * Determines whether this goal is active.
   *
   * @var bool
   */
  public $active;
  /**
   * Time this goal was created.
   *
   * @var string
   */
  public $created;
  protected $eventDetailsType = GoalEventDetails::class;
  protected $eventDetailsDataType = '';
  /**
   * Goal ID.
   *
   * @var string
   */
  public $id;
  /**
   * Internal ID for the web property to which this goal belongs.
   *
   * @var string
   */
  public $internalWebPropertyId;
  /**
   * Resource type for an Analytics goal.
   *
   * @var string
   */
  public $kind;
  /**
   * Goal name.
   *
   * @var string
   */
  public $name;
  protected $parentLinkType = GoalParentLink::class;
  protected $parentLinkDataType = '';
  /**
   * View (Profile) ID to which this goal belongs.
   *
   * @var string
   */
  public $profileId;
  /**
   * Link for this goal.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Goal type. Possible values are URL_DESTINATION, VISIT_TIME_ON_SITE,
   * VISIT_NUM_PAGES, AND EVENT.
   *
   * @var string
   */
  public $type;
  /**
   * Time this goal was last modified.
   *
   * @var string
   */
  public $updated;
  protected $urlDestinationDetailsType = GoalUrlDestinationDetails::class;
  protected $urlDestinationDetailsDataType = '';
  /**
   * Goal value.
   *
   * @var float
   */
  public $value;
  protected $visitNumPagesDetailsType = GoalVisitNumPagesDetails::class;
  protected $visitNumPagesDetailsDataType = '';
  protected $visitTimeOnSiteDetailsType = GoalVisitTimeOnSiteDetails::class;
  protected $visitTimeOnSiteDetailsDataType = '';
  /**
   * Web property ID to which this goal belongs. The web property ID is of the
   * form UA-XXXXX-YY.
   *
   * @var string
   */
  public $webPropertyId;

  /**
   * Account ID to which this goal belongs.
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
   * Determines whether this goal is active.
   *
   * @param bool $active
   */
  public function setActive($active)
  {
    $this->active = $active;
  }
  /**
   * @return bool
   */
  public function getActive()
  {
    return $this->active;
  }
  /**
   * Time this goal was created.
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
   * Details for the goal of the type EVENT.
   *
   * @param GoalEventDetails $eventDetails
   */
  public function setEventDetails(GoalEventDetails $eventDetails)
  {
    $this->eventDetails = $eventDetails;
  }
  /**
   * @return GoalEventDetails
   */
  public function getEventDetails()
  {
    return $this->eventDetails;
  }
  /**
   * Goal ID.
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
   * Internal ID for the web property to which this goal belongs.
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
   * Resource type for an Analytics goal.
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
   * Goal name.
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
   * Parent link for a goal. Points to the view (profile) to which this goal
   * belongs.
   *
   * @param GoalParentLink $parentLink
   */
  public function setParentLink(GoalParentLink $parentLink)
  {
    $this->parentLink = $parentLink;
  }
  /**
   * @return GoalParentLink
   */
  public function getParentLink()
  {
    return $this->parentLink;
  }
  /**
   * View (Profile) ID to which this goal belongs.
   *
   * @param string $profileId
   */
  public function setProfileId($profileId)
  {
    $this->profileId = $profileId;
  }
  /**
   * @return string
   */
  public function getProfileId()
  {
    return $this->profileId;
  }
  /**
   * Link for this goal.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Goal type. Possible values are URL_DESTINATION, VISIT_TIME_ON_SITE,
   * VISIT_NUM_PAGES, AND EVENT.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Time this goal was last modified.
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
   * Details for the goal of the type URL_DESTINATION.
   *
   * @param GoalUrlDestinationDetails $urlDestinationDetails
   */
  public function setUrlDestinationDetails(GoalUrlDestinationDetails $urlDestinationDetails)
  {
    $this->urlDestinationDetails = $urlDestinationDetails;
  }
  /**
   * @return GoalUrlDestinationDetails
   */
  public function getUrlDestinationDetails()
  {
    return $this->urlDestinationDetails;
  }
  /**
   * Goal value.
   *
   * @param float $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return float
   */
  public function getValue()
  {
    return $this->value;
  }
  /**
   * Details for the goal of the type VISIT_NUM_PAGES.
   *
   * @param GoalVisitNumPagesDetails $visitNumPagesDetails
   */
  public function setVisitNumPagesDetails(GoalVisitNumPagesDetails $visitNumPagesDetails)
  {
    $this->visitNumPagesDetails = $visitNumPagesDetails;
  }
  /**
   * @return GoalVisitNumPagesDetails
   */
  public function getVisitNumPagesDetails()
  {
    return $this->visitNumPagesDetails;
  }
  /**
   * Details for the goal of the type VISIT_TIME_ON_SITE.
   *
   * @param GoalVisitTimeOnSiteDetails $visitTimeOnSiteDetails
   */
  public function setVisitTimeOnSiteDetails(GoalVisitTimeOnSiteDetails $visitTimeOnSiteDetails)
  {
    $this->visitTimeOnSiteDetails = $visitTimeOnSiteDetails;
  }
  /**
   * @return GoalVisitTimeOnSiteDetails
   */
  public function getVisitTimeOnSiteDetails()
  {
    return $this->visitTimeOnSiteDetails;
  }
  /**
   * Web property ID to which this goal belongs. The web property ID is of the
   * form UA-XXXXX-YY.
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
class_alias(Goal::class, 'Google_Service_Analytics_Goal');
