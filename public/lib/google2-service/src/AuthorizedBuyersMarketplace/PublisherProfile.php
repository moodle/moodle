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

class PublisherProfile extends \Google\Collection
{
  protected $collection_key = 'topHeadlines';
  /**
   * Description on the publisher's audience.
   *
   * @var string
   */
  public $audienceDescription;
  /**
   * Contact information for direct reservation deals. This is free text entered
   * by the publisher and may include information like names, phone numbers and
   * email addresses.
   *
   * @var string
   */
  public $directDealsContact;
  /**
   * Display name of the publisher profile. Can be used to filter the response
   * of the publisherProfiles.list method.
   *
   * @var string
   */
  public $displayName;
  /**
   * The list of domains represented in this publisher profile. Empty if this is
   * a parent profile. These are top private domains, meaning that these will
   * not contain a string like "photos.google.co.uk/123", but will instead
   * contain "google.co.uk". Can be used to filter the response of the
   * publisherProfiles.list method.
   *
   * @var string[]
   */
  public $domains;
  /**
   * Indicates if this profile is the parent profile of the seller. A parent
   * profile represents all the inventory from the seller, as opposed to child
   * profile that is created to brand a portion of inventory. One seller has
   * only one parent publisher profile, and can have multiple child profiles.
   * See https://support.google.com/admanager/answer/6035806 for details. Can be
   * used to filter the response of the publisherProfiles.list method by setting
   * the filter to "is_parent: true".
   *
   * @var bool
   */
  public $isParent;
  /**
   * A Google public URL to the logo for this publisher profile. The logo is
   * stored as a PNG, JPG, or GIF image.
   *
   * @var string
   */
  public $logoUrl;
  /**
   * URL to additional marketing and sales materials.
   *
   * @var string
   */
  public $mediaKitUrl;
  protected $mobileAppsType = PublisherProfileMobileApplication::class;
  protected $mobileAppsDataType = 'array';
  /**
   * Name of the publisher profile. Format:
   * `buyers/{buyer}/publisherProfiles/{publisher_profile}`
   *
   * @var string
   */
  public $name;
  /**
   * Overview of the publisher.
   *
   * @var string
   */
  public $overview;
  /**
   * Statement explaining what's unique about publisher's business, and why
   * buyers should partner with the publisher.
   *
   * @var string
   */
  public $pitchStatement;
  /**
   * Contact information for programmatic deals. This is free text entered by
   * the publisher and may include information like names, phone numbers and
   * email addresses.
   *
   * @var string
   */
  public $programmaticDealsContact;
  /**
   * A unique identifying code for the seller. This value is the same for all of
   * the seller's parent and child publisher profiles. Can be used to filter the
   * response of the publisherProfiles.list method.
   *
   * @var string
   */
  public $publisherCode;
  /**
   * URL to a sample content page.
   *
   * @var string
   */
  public $samplePageUrl;
  /**
   * Up to three key metrics and rankings. For example, "#1 Mobile News Site for
   * 20 Straight Months".
   *
   * @var string[]
   */
  public $topHeadlines;

  /**
   * Description on the publisher's audience.
   *
   * @param string $audienceDescription
   */
  public function setAudienceDescription($audienceDescription)
  {
    $this->audienceDescription = $audienceDescription;
  }
  /**
   * @return string
   */
  public function getAudienceDescription()
  {
    return $this->audienceDescription;
  }
  /**
   * Contact information for direct reservation deals. This is free text entered
   * by the publisher and may include information like names, phone numbers and
   * email addresses.
   *
   * @param string $directDealsContact
   */
  public function setDirectDealsContact($directDealsContact)
  {
    $this->directDealsContact = $directDealsContact;
  }
  /**
   * @return string
   */
  public function getDirectDealsContact()
  {
    return $this->directDealsContact;
  }
  /**
   * Display name of the publisher profile. Can be used to filter the response
   * of the publisherProfiles.list method.
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
   * The list of domains represented in this publisher profile. Empty if this is
   * a parent profile. These are top private domains, meaning that these will
   * not contain a string like "photos.google.co.uk/123", but will instead
   * contain "google.co.uk". Can be used to filter the response of the
   * publisherProfiles.list method.
   *
   * @param string[] $domains
   */
  public function setDomains($domains)
  {
    $this->domains = $domains;
  }
  /**
   * @return string[]
   */
  public function getDomains()
  {
    return $this->domains;
  }
  /**
   * Indicates if this profile is the parent profile of the seller. A parent
   * profile represents all the inventory from the seller, as opposed to child
   * profile that is created to brand a portion of inventory. One seller has
   * only one parent publisher profile, and can have multiple child profiles.
   * See https://support.google.com/admanager/answer/6035806 for details. Can be
   * used to filter the response of the publisherProfiles.list method by setting
   * the filter to "is_parent: true".
   *
   * @param bool $isParent
   */
  public function setIsParent($isParent)
  {
    $this->isParent = $isParent;
  }
  /**
   * @return bool
   */
  public function getIsParent()
  {
    return $this->isParent;
  }
  /**
   * A Google public URL to the logo for this publisher profile. The logo is
   * stored as a PNG, JPG, or GIF image.
   *
   * @param string $logoUrl
   */
  public function setLogoUrl($logoUrl)
  {
    $this->logoUrl = $logoUrl;
  }
  /**
   * @return string
   */
  public function getLogoUrl()
  {
    return $this->logoUrl;
  }
  /**
   * URL to additional marketing and sales materials.
   *
   * @param string $mediaKitUrl
   */
  public function setMediaKitUrl($mediaKitUrl)
  {
    $this->mediaKitUrl = $mediaKitUrl;
  }
  /**
   * @return string
   */
  public function getMediaKitUrl()
  {
    return $this->mediaKitUrl;
  }
  /**
   * The list of apps represented in this publisher profile. Empty if this is a
   * parent profile.
   *
   * @param PublisherProfileMobileApplication[] $mobileApps
   */
  public function setMobileApps($mobileApps)
  {
    $this->mobileApps = $mobileApps;
  }
  /**
   * @return PublisherProfileMobileApplication[]
   */
  public function getMobileApps()
  {
    return $this->mobileApps;
  }
  /**
   * Name of the publisher profile. Format:
   * `buyers/{buyer}/publisherProfiles/{publisher_profile}`
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
   * Overview of the publisher.
   *
   * @param string $overview
   */
  public function setOverview($overview)
  {
    $this->overview = $overview;
  }
  /**
   * @return string
   */
  public function getOverview()
  {
    return $this->overview;
  }
  /**
   * Statement explaining what's unique about publisher's business, and why
   * buyers should partner with the publisher.
   *
   * @param string $pitchStatement
   */
  public function setPitchStatement($pitchStatement)
  {
    $this->pitchStatement = $pitchStatement;
  }
  /**
   * @return string
   */
  public function getPitchStatement()
  {
    return $this->pitchStatement;
  }
  /**
   * Contact information for programmatic deals. This is free text entered by
   * the publisher and may include information like names, phone numbers and
   * email addresses.
   *
   * @param string $programmaticDealsContact
   */
  public function setProgrammaticDealsContact($programmaticDealsContact)
  {
    $this->programmaticDealsContact = $programmaticDealsContact;
  }
  /**
   * @return string
   */
  public function getProgrammaticDealsContact()
  {
    return $this->programmaticDealsContact;
  }
  /**
   * A unique identifying code for the seller. This value is the same for all of
   * the seller's parent and child publisher profiles. Can be used to filter the
   * response of the publisherProfiles.list method.
   *
   * @param string $publisherCode
   */
  public function setPublisherCode($publisherCode)
  {
    $this->publisherCode = $publisherCode;
  }
  /**
   * @return string
   */
  public function getPublisherCode()
  {
    return $this->publisherCode;
  }
  /**
   * URL to a sample content page.
   *
   * @param string $samplePageUrl
   */
  public function setSamplePageUrl($samplePageUrl)
  {
    $this->samplePageUrl = $samplePageUrl;
  }
  /**
   * @return string
   */
  public function getSamplePageUrl()
  {
    return $this->samplePageUrl;
  }
  /**
   * Up to three key metrics and rankings. For example, "#1 Mobile News Site for
   * 20 Straight Months".
   *
   * @param string[] $topHeadlines
   */
  public function setTopHeadlines($topHeadlines)
  {
    $this->topHeadlines = $topHeadlines;
  }
  /**
   * @return string[]
   */
  public function getTopHeadlines()
  {
    return $this->topHeadlines;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PublisherProfile::class, 'Google_Service_AuthorizedBuyersMarketplace_PublisherProfile');
