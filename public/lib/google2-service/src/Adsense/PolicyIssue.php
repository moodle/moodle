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

namespace Google\Service\Adsense;

class PolicyIssue extends \Google\Collection
{
  /**
   * The action is unspecified.
   */
  public const ACTION_ENFORCEMENT_ACTION_UNSPECIFIED = 'ENFORCEMENT_ACTION_UNSPECIFIED';
  /**
   * No ad serving enforcement is currently present, but enforcement will start
   * on the `warning_escalation_date` if the issue is not resolved.
   */
  public const ACTION_WARNED = 'WARNED';
  /**
   * Ad serving demand has been restricted on the entity.
   */
  public const ACTION_AD_SERVING_RESTRICTED = 'AD_SERVING_RESTRICTED';
  /**
   * Ad serving has been disabled on the entity.
   */
  public const ACTION_AD_SERVING_DISABLED = 'AD_SERVING_DISABLED';
  /**
   * Ads are being served for the entity but Confirmed Click is being applied to
   * the ads. See https://support.google.com/adsense/answer/10025624.
   */
  public const ACTION_AD_SERVED_WITH_CLICK_CONFIRMATION = 'AD_SERVED_WITH_CLICK_CONFIRMATION';
  /**
   * Ad personalization is restricted because the ad requests coming from the
   * EEA and UK do not have a TCF string or the Consent Management Platform
   * (CMP) indicated by the TCF string is not Google certified. As a result,
   * basic/limited ads will be served. See
   * https://support.google.com/adsense/answer/13554116.
   */
  public const ACTION_AD_PERSONALIZATION_RESTRICTED = 'AD_PERSONALIZATION_RESTRICTED';
  /**
   * The entity type is unspecified.
   */
  public const ENTITY_TYPE_ENTITY_TYPE_UNSPECIFIED = 'ENTITY_TYPE_UNSPECIFIED';
  /**
   * The enforced entity is an entire website.
   */
  public const ENTITY_TYPE_SITE = 'SITE';
  /**
   * The enforced entity is a particular section of a website. All the pages
   * with this prefix are enforced.
   */
  public const ENTITY_TYPE_SITE_SECTION = 'SITE_SECTION';
  /**
   * The enforced entity is a single web page.
   */
  public const ENTITY_TYPE_PAGE = 'PAGE';
  protected $collection_key = 'policyTopics';
  /**
   * Required. The most severe action taken on the entity over the past seven
   * days.
   *
   * @var string
   */
  public $action;
  /**
   * Optional. List of ad clients associated with the policy issue (either as
   * the primary ad client or an associated host/secondary ad client). In the
   * latter case, this will be an ad client that is not owned by the current
   * account.
   *
   * @var string[]
   */
  public $adClients;
  /**
   * Required. Total number of ad requests affected by the policy violations
   * over the past seven days.
   *
   * @var string
   */
  public $adRequestCount;
  /**
   * Required. Type of the entity indicating if the entity is a site, site-
   * section, or page.
   *
   * @var string
   */
  public $entityType;
  protected $firstDetectedDateType = Date::class;
  protected $firstDetectedDateDataType = '';
  protected $lastDetectedDateType = Date::class;
  protected $lastDetectedDateDataType = '';
  /**
   * Required. Resource name of the entity with policy issues. Format:
   * accounts/{account}/policyIssues/{policy_issue}
   *
   * @var string
   */
  public $name;
  protected $policyTopicsType = PolicyTopic::class;
  protected $policyTopicsDataType = 'array';
  /**
   * Required. Hostname/domain of the entity (for example "foo.com" or
   * "www.foo.com"). This _should_ be a bare domain/host name without any
   * protocol. This will be present for all policy issues.
   *
   * @var string
   */
  public $site;
  /**
   * Optional. Prefix of the site-section having policy issues (For example
   * "foo.com/bar-section"). This will be present if the `entity_type` is
   * `SITE_SECTION` and will be absent for other entity types.
   *
   * @var string
   */
  public $siteSection;
  /**
   * Optional. URI of the page having policy violations (for example
   * "foo.com/bar" or "www.foo.com/bar"). This will be present if the
   * `entity_type` is `PAGE` and will be absent for other entity types.
   *
   * @var string
   */
  public $uri;
  protected $warningEscalationDateType = Date::class;
  protected $warningEscalationDateDataType = '';

  /**
   * Required. The most severe action taken on the entity over the past seven
   * days.
   *
   * Accepted values: ENFORCEMENT_ACTION_UNSPECIFIED, WARNED,
   * AD_SERVING_RESTRICTED, AD_SERVING_DISABLED,
   * AD_SERVED_WITH_CLICK_CONFIRMATION, AD_PERSONALIZATION_RESTRICTED
   *
   * @param self::ACTION_* $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return self::ACTION_*
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Optional. List of ad clients associated with the policy issue (either as
   * the primary ad client or an associated host/secondary ad client). In the
   * latter case, this will be an ad client that is not owned by the current
   * account.
   *
   * @param string[] $adClients
   */
  public function setAdClients($adClients)
  {
    $this->adClients = $adClients;
  }
  /**
   * @return string[]
   */
  public function getAdClients()
  {
    return $this->adClients;
  }
  /**
   * Required. Total number of ad requests affected by the policy violations
   * over the past seven days.
   *
   * @param string $adRequestCount
   */
  public function setAdRequestCount($adRequestCount)
  {
    $this->adRequestCount = $adRequestCount;
  }
  /**
   * @return string
   */
  public function getAdRequestCount()
  {
    return $this->adRequestCount;
  }
  /**
   * Required. Type of the entity indicating if the entity is a site, site-
   * section, or page.
   *
   * Accepted values: ENTITY_TYPE_UNSPECIFIED, SITE, SITE_SECTION, PAGE
   *
   * @param self::ENTITY_TYPE_* $entityType
   */
  public function setEntityType($entityType)
  {
    $this->entityType = $entityType;
  }
  /**
   * @return self::ENTITY_TYPE_*
   */
  public function getEntityType()
  {
    return $this->entityType;
  }
  /**
   * Required. The date (in the America/Los_Angeles timezone) when policy
   * violations were first detected on the entity.
   *
   * @param Date $firstDetectedDate
   */
  public function setFirstDetectedDate(Date $firstDetectedDate)
  {
    $this->firstDetectedDate = $firstDetectedDate;
  }
  /**
   * @return Date
   */
  public function getFirstDetectedDate()
  {
    return $this->firstDetectedDate;
  }
  /**
   * Required. The date (in the America/Los_Angeles timezone) when policy
   * violations were last detected on the entity.
   *
   * @param Date $lastDetectedDate
   */
  public function setLastDetectedDate(Date $lastDetectedDate)
  {
    $this->lastDetectedDate = $lastDetectedDate;
  }
  /**
   * @return Date
   */
  public function getLastDetectedDate()
  {
    return $this->lastDetectedDate;
  }
  /**
   * Required. Resource name of the entity with policy issues. Format:
   * accounts/{account}/policyIssues/{policy_issue}
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
   * Required. Unordered list. The policy topics that this entity was found to
   * violate over the past seven days.
   *
   * @param PolicyTopic[] $policyTopics
   */
  public function setPolicyTopics($policyTopics)
  {
    $this->policyTopics = $policyTopics;
  }
  /**
   * @return PolicyTopic[]
   */
  public function getPolicyTopics()
  {
    return $this->policyTopics;
  }
  /**
   * Required. Hostname/domain of the entity (for example "foo.com" or
   * "www.foo.com"). This _should_ be a bare domain/host name without any
   * protocol. This will be present for all policy issues.
   *
   * @param string $site
   */
  public function setSite($site)
  {
    $this->site = $site;
  }
  /**
   * @return string
   */
  public function getSite()
  {
    return $this->site;
  }
  /**
   * Optional. Prefix of the site-section having policy issues (For example
   * "foo.com/bar-section"). This will be present if the `entity_type` is
   * `SITE_SECTION` and will be absent for other entity types.
   *
   * @param string $siteSection
   */
  public function setSiteSection($siteSection)
  {
    $this->siteSection = $siteSection;
  }
  /**
   * @return string
   */
  public function getSiteSection()
  {
    return $this->siteSection;
  }
  /**
   * Optional. URI of the page having policy violations (for example
   * "foo.com/bar" or "www.foo.com/bar"). This will be present if the
   * `entity_type` is `PAGE` and will be absent for other entity types.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
  /**
   * Optional. The date (in the America/Los_Angeles timezone) when the entity
   * will have ad serving demand restricted or ad serving disabled. This is
   * present only for issues with a `WARNED` enforcement action. See
   * https://support.google.com/adsense/answer/11066888.
   *
   * @param Date $warningEscalationDate
   */
  public function setWarningEscalationDate(Date $warningEscalationDate)
  {
    $this->warningEscalationDate = $warningEscalationDate;
  }
  /**
   * @return Date
   */
  public function getWarningEscalationDate()
  {
    return $this->warningEscalationDate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PolicyIssue::class, 'Google_Service_Adsense_PolicyIssue');
