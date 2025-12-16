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

namespace Google\Service\Dfareporting;

class Site extends \Google\Collection
{
  protected $collection_key = 'siteContacts';
  /**
   * Account ID of this site. This is a read-only field that can be left blank.
   *
   * @var string
   */
  public $accountId;
  /**
   * Optional. Ad serving platform ID to identify the ad serving platform used
   * by the site. Measurement partners can use this field to add ad-server
   * specific macros. If set, this value acts as the default during placement
   * creation. Possible values are: * `1`, Adelphic * `2`, Adform * `3`, Adobe *
   * `4`, Amobee * `5`, Basis (Centro) * `6`, Beeswax * `7`, Amazon * `8`, DV360
   * (DBM) * `9`, Innovid * `10`, MediaMath * `11`, Roku OneView DSP * `12`,
   * TabMo Hawk * `13`, The Trade Desk * `14`, Xandr Invest DSP * `15`, Yahoo
   * DSP * `16`, Zeta Global * `17`, Scaleout * `18`, Bidtellect * `19`, Unicorn
   * * `20`, Teads * `21`, Quantcast * `22`, Cognitiv * `23`, AdTheorent * `24`,
   * DeepIntent * `25`, Pulsepoint
   *
   * @var string
   */
  public $adServingPlatformId;
  /**
   * Whether this site is approved.
   *
   * @var bool
   */
  public $approved;
  /**
   * Directory site associated with this site. This is a required field that is
   * read-only after insertion.
   *
   * @var string
   */
  public $directorySiteId;
  protected $directorySiteIdDimensionValueType = DimensionValue::class;
  protected $directorySiteIdDimensionValueDataType = '';
  /**
   * ID of this site. This is a read-only, auto-generated field.
   *
   * @var string
   */
  public $id;
  protected $idDimensionValueType = DimensionValue::class;
  protected $idDimensionValueDataType = '';
  /**
   * Key name of this site. This is a read-only, auto-generated field.
   *
   * @var string
   */
  public $keyName;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#site".
   *
   * @var string
   */
  public $kind;
  /**
   * Name of this site.This is a required field. Must be less than 128
   * characters long. If this site is under a subaccount, the name must be
   * unique among sites of the same subaccount. Otherwise, this site is a top-
   * level site, and the name must be unique among top-level sites of the same
   * account.
   *
   * @var string
   */
  public $name;
  protected $siteContactsType = SiteContact::class;
  protected $siteContactsDataType = 'array';
  protected $siteSettingsType = SiteSettings::class;
  protected $siteSettingsDataType = '';
  /**
   * Subaccount ID of this site. This is a read-only field that can be left
   * blank.
   *
   * @var string
   */
  public $subaccountId;
  protected $videoSettingsType = SiteVideoSettings::class;
  protected $videoSettingsDataType = '';

  /**
   * Account ID of this site. This is a read-only field that can be left blank.
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
   * Optional. Ad serving platform ID to identify the ad serving platform used
   * by the site. Measurement partners can use this field to add ad-server
   * specific macros. If set, this value acts as the default during placement
   * creation. Possible values are: * `1`, Adelphic * `2`, Adform * `3`, Adobe *
   * `4`, Amobee * `5`, Basis (Centro) * `6`, Beeswax * `7`, Amazon * `8`, DV360
   * (DBM) * `9`, Innovid * `10`, MediaMath * `11`, Roku OneView DSP * `12`,
   * TabMo Hawk * `13`, The Trade Desk * `14`, Xandr Invest DSP * `15`, Yahoo
   * DSP * `16`, Zeta Global * `17`, Scaleout * `18`, Bidtellect * `19`, Unicorn
   * * `20`, Teads * `21`, Quantcast * `22`, Cognitiv * `23`, AdTheorent * `24`,
   * DeepIntent * `25`, Pulsepoint
   *
   * @param string $adServingPlatformId
   */
  public function setAdServingPlatformId($adServingPlatformId)
  {
    $this->adServingPlatformId = $adServingPlatformId;
  }
  /**
   * @return string
   */
  public function getAdServingPlatformId()
  {
    return $this->adServingPlatformId;
  }
  /**
   * Whether this site is approved.
   *
   * @param bool $approved
   */
  public function setApproved($approved)
  {
    $this->approved = $approved;
  }
  /**
   * @return bool
   */
  public function getApproved()
  {
    return $this->approved;
  }
  /**
   * Directory site associated with this site. This is a required field that is
   * read-only after insertion.
   *
   * @param string $directorySiteId
   */
  public function setDirectorySiteId($directorySiteId)
  {
    $this->directorySiteId = $directorySiteId;
  }
  /**
   * @return string
   */
  public function getDirectorySiteId()
  {
    return $this->directorySiteId;
  }
  /**
   * Dimension value for the ID of the directory site. This is a read-only,
   * auto-generated field.
   *
   * @param DimensionValue $directorySiteIdDimensionValue
   */
  public function setDirectorySiteIdDimensionValue(DimensionValue $directorySiteIdDimensionValue)
  {
    $this->directorySiteIdDimensionValue = $directorySiteIdDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getDirectorySiteIdDimensionValue()
  {
    return $this->directorySiteIdDimensionValue;
  }
  /**
   * ID of this site. This is a read-only, auto-generated field.
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
   * Dimension value for the ID of this site. This is a read-only, auto-
   * generated field.
   *
   * @param DimensionValue $idDimensionValue
   */
  public function setIdDimensionValue(DimensionValue $idDimensionValue)
  {
    $this->idDimensionValue = $idDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getIdDimensionValue()
  {
    return $this->idDimensionValue;
  }
  /**
   * Key name of this site. This is a read-only, auto-generated field.
   *
   * @param string $keyName
   */
  public function setKeyName($keyName)
  {
    $this->keyName = $keyName;
  }
  /**
   * @return string
   */
  public function getKeyName()
  {
    return $this->keyName;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#site".
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
   * Name of this site.This is a required field. Must be less than 128
   * characters long. If this site is under a subaccount, the name must be
   * unique among sites of the same subaccount. Otherwise, this site is a top-
   * level site, and the name must be unique among top-level sites of the same
   * account.
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
   * Site contacts.
   *
   * @param SiteContact[] $siteContacts
   */
  public function setSiteContacts($siteContacts)
  {
    $this->siteContacts = $siteContacts;
  }
  /**
   * @return SiteContact[]
   */
  public function getSiteContacts()
  {
    return $this->siteContacts;
  }
  /**
   * Site-wide settings.
   *
   * @param SiteSettings $siteSettings
   */
  public function setSiteSettings(SiteSettings $siteSettings)
  {
    $this->siteSettings = $siteSettings;
  }
  /**
   * @return SiteSettings
   */
  public function getSiteSettings()
  {
    return $this->siteSettings;
  }
  /**
   * Subaccount ID of this site. This is a read-only field that can be left
   * blank.
   *
   * @param string $subaccountId
   */
  public function setSubaccountId($subaccountId)
  {
    $this->subaccountId = $subaccountId;
  }
  /**
   * @return string
   */
  public function getSubaccountId()
  {
    return $this->subaccountId;
  }
  /**
   * Default video settings for new placements created under this site. This
   * value will be used to populate the placements.videoSettings field, when no
   * value is specified for the new placement.
   *
   * @param SiteVideoSettings $videoSettings
   */
  public function setVideoSettings(SiteVideoSettings $videoSettings)
  {
    $this->videoSettings = $videoSettings;
  }
  /**
   * @return SiteVideoSettings
   */
  public function getVideoSettings()
  {
    return $this->videoSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Site::class, 'Google_Service_Dfareporting_Site');
