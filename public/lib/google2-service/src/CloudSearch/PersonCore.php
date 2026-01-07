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

namespace Google\Service\CloudSearch;

class PersonCore extends \Google\Collection
{
  public const AVAILABILITY_STATUS_UNKNOWN = 'UNKNOWN';
  public const AVAILABILITY_STATUS_OUT_OF_OFFICE = 'OUT_OF_OFFICE';
  public const AVAILABILITY_STATUS_OUTSIDE_WORKING_HOURS = 'OUTSIDE_WORKING_HOURS';
  public const AVAILABILITY_STATUS_AVAILABLE = 'AVAILABLE';
  protected $collection_key = 'phoneNumbers';
  /**
   * Instructions for how to address this person (e.g. custom pronouns). For
   * google.com this is a set of pronouns from a defined list of options.
   *
   * @var string
   */
  public $addressMeAs;
  protected $adminToType = PersonCore::class;
  protected $adminToDataType = 'array';
  protected $adminsType = PersonCore::class;
  protected $adminsDataType = 'array';
  /**
   * @var string
   */
  public $availabilityStatus;
  protected $birthdayType = Date::class;
  protected $birthdayDataType = '';
  protected $calendarUrlType = SafeUrlProto::class;
  protected $calendarUrlDataType = '';
  protected $chatUrlType = SafeUrlProto::class;
  protected $chatUrlDataType = '';
  /**
   * Person's cost center as a string, e.g. "926: Googler Apps".
   *
   * @var string
   */
  public $costCenter;
  /**
   * The person's Organization department, e.g. "People Operations". For
   * google.com this is usually called "area".
   *
   * @var string
   */
  public $department;
  protected $directReportsType = PersonCore::class;
  protected $directReportsDataType = 'array';
  protected $dottedLineManagersType = PersonCore::class;
  protected $dottedLineManagersDataType = 'array';
  protected $dottedLineReportsType = PersonCore::class;
  protected $dottedLineReportsDataType = 'array';
  /**
   * E-mail addresses of the person. The primary or preferred email should be
   * first.
   *
   * @var string[]
   */
  public $emails;
  /**
   * Person's employee number (external ID of type "organization") For
   * google.com this is the badge number (e.g. 2 for Larry Page).
   *
   * @var string
   */
  public $employeeId;
  /**
   * A fingerprint used by PAPI to reliably determine if a resource has changed
   * Externally it is used as part of the etag.
   *
   * @var string
   */
  public $fingerprint;
  /**
   * Full-time equivalent (in ‰) (e.g. 800 for a person who's working 80%).
   *
   * @var string
   */
  public $ftePermille;
  protected $geoLocationType = MapInfo::class;
  protected $geoLocationDataType = '';
  /**
   * @var string
   */
  public $gmailUrl;
  /**
   * Profile owner's job title (e.g. "Software Engineer"). For google.com this
   * is the Workday preferred job title.
   *
   * @var string
   */
  public $jobTitle;
  /**
   * List of keys to use from the map 'keywords'.
   *
   * @var string[]
   */
  public $keywordTypes;
  /**
   * Custom keywords the domain admin has added.
   *
   * @var string[]
   */
  public $keywords;
  protected $linksType = EnterpriseTopazFrontendTeamsLink::class;
  protected $linksDataType = 'array';
  /**
   * Detailed desk location within the company. For google.com this is the desk
   * location code (e.g. "DE-MUC-ARP-6T2-6T2C0C") if the person has a desk.
   *
   * @var string
   */
  public $location;
  protected $managersType = PersonCore::class;
  protected $managersDataType = 'array';
  /**
   * Custom mission statement the profile owner has added.
   *
   * @var string
   */
  public $mission;
  /**
   * Human-readable Unicode display name.
   *
   * @var string
   */
  public $name;
  /**
   * Office/building identifier within the company. For google.com this is the
   * office code (e.g. "DE-MUC-ARP").
   *
   * @var string
   */
  public $officeLocation;
  /**
   * The person's obfuscated Gaia ID.
   *
   * @var string
   */
  public $personId;
  protected $phoneNumbersType = EnterpriseTopazFrontendTeamsPersonCorePhoneNumber::class;
  protected $phoneNumbersDataType = 'array';
  protected $photoUrlType = SafeUrlProto::class;
  protected $photoUrlDataType = '';
  /**
   * Postal address of office/building.
   *
   * @var string
   */
  public $postalAddress;
  /**
   * Total count of the profile owner's direct reports.
   *
   * @var int
   */
  public $totalDirectReportsCount;
  /**
   * Total count of the profile owner's dotted-line reports.
   *
   * @var int
   */
  public $totalDlrCount;
  /**
   * The sum of all profile owner's reports and their own full-time-equivalents
   * in ‰ (e.g. 1800 if one report is working 80% and profile owner 100%).
   *
   * @var string
   */
  public $totalFteCount;
  /**
   * External ID of type "login_id" for the profile. For google.com this is the
   * username/LDAP.
   *
   * @var string
   */
  public $username;
  /**
   * @var string
   */
  public $waldoComeBackTime;

  /**
   * Instructions for how to address this person (e.g. custom pronouns). For
   * google.com this is a set of pronouns from a defined list of options.
   *
   * @param string $addressMeAs
   */
  public function setAddressMeAs($addressMeAs)
  {
    $this->addressMeAs = $addressMeAs;
  }
  /**
   * @return string
   */
  public function getAddressMeAs()
  {
    return $this->addressMeAs;
  }
  /**
   * People the profile owner is an admin to. Note that not all fields of these
   * PersonCores will be set, in particular, relationships will be empty.
   *
   * @param PersonCore[] $adminTo
   */
  public function setAdminTo($adminTo)
  {
    $this->adminTo = $adminTo;
  }
  /**
   * @return PersonCore[]
   */
  public function getAdminTo()
  {
    return $this->adminTo;
  }
  /**
   * The profile owner's admins in no particular order. Note that not all fields
   * of these PersonCores will be set, in particular, relationships will be
   * empty.
   *
   * @param PersonCore[] $admins
   */
  public function setAdmins($admins)
  {
    $this->admins = $admins;
  }
  /**
   * @return PersonCore[]
   */
  public function getAdmins()
  {
    return $this->admins;
  }
  /**
   * @param self::AVAILABILITY_STATUS_* $availabilityStatus
   */
  public function setAvailabilityStatus($availabilityStatus)
  {
    $this->availabilityStatus = $availabilityStatus;
  }
  /**
   * @return self::AVAILABILITY_STATUS_*
   */
  public function getAvailabilityStatus()
  {
    return $this->availabilityStatus;
  }
  /**
   * Person birthday.
   *
   * @param Date $birthday
   */
  public function setBirthday(Date $birthday)
  {
    $this->birthday = $birthday;
  }
  /**
   * @return Date
   */
  public function getBirthday()
  {
    return $this->birthday;
  }
  /**
   * The URL to open the profile owner's primary calendar.
   *
   * @param SafeUrlProto $calendarUrl
   */
  public function setCalendarUrl(SafeUrlProto $calendarUrl)
  {
    $this->calendarUrl = $calendarUrl;
  }
  /**
   * @return SafeUrlProto
   */
  public function getCalendarUrl()
  {
    return $this->calendarUrl;
  }
  /**
   * The URL to start a chat conversation with the profile owner. For google.com
   * this is a Hangouts URL.
   *
   * @param SafeUrlProto $chatUrl
   */
  public function setChatUrl(SafeUrlProto $chatUrl)
  {
    $this->chatUrl = $chatUrl;
  }
  /**
   * @return SafeUrlProto
   */
  public function getChatUrl()
  {
    return $this->chatUrl;
  }
  /**
   * Person's cost center as a string, e.g. "926: Googler Apps".
   *
   * @param string $costCenter
   */
  public function setCostCenter($costCenter)
  {
    $this->costCenter = $costCenter;
  }
  /**
   * @return string
   */
  public function getCostCenter()
  {
    return $this->costCenter;
  }
  /**
   * The person's Organization department, e.g. "People Operations". For
   * google.com this is usually called "area".
   *
   * @param string $department
   */
  public function setDepartment($department)
  {
    $this->department = $department;
  }
  /**
   * @return string
   */
  public function getDepartment()
  {
    return $this->department;
  }
  /**
   * A subset of the profile owner's direct reports. The number of entities here
   * may be less than total_direct_reports_count, because typically
   * ProfileResponse does not include all the person's reports, if there are too
   * many to retrieve efficiently. Note that not all fields of these PersonCores
   * will be set, in particular, relationships will be empty.
   *
   * @param PersonCore[] $directReports
   */
  public function setDirectReports($directReports)
  {
    $this->directReports = $directReports;
  }
  /**
   * @return PersonCore[]
   */
  public function getDirectReports()
  {
    return $this->directReports;
  }
  /**
   * The profile owner's direct dotted line managers in no particular order.
   * Note that not all fields of these PersonCores will be set, in particular,
   * relationships will be empty.
   *
   * @param PersonCore[] $dottedLineManagers
   */
  public function setDottedLineManagers($dottedLineManagers)
  {
    $this->dottedLineManagers = $dottedLineManagers;
  }
  /**
   * @return PersonCore[]
   */
  public function getDottedLineManagers()
  {
    return $this->dottedLineManagers;
  }
  /**
   * A subset of the profile owner's dotted-line reports. The number of entities
   * here may be less than total_dlr_count. Note that not all fields of these
   * PersonCores will be set, in particular, relationships will be empty.
   *
   * @param PersonCore[] $dottedLineReports
   */
  public function setDottedLineReports($dottedLineReports)
  {
    $this->dottedLineReports = $dottedLineReports;
  }
  /**
   * @return PersonCore[]
   */
  public function getDottedLineReports()
  {
    return $this->dottedLineReports;
  }
  /**
   * E-mail addresses of the person. The primary or preferred email should be
   * first.
   *
   * @param string[] $emails
   */
  public function setEmails($emails)
  {
    $this->emails = $emails;
  }
  /**
   * @return string[]
   */
  public function getEmails()
  {
    return $this->emails;
  }
  /**
   * Person's employee number (external ID of type "organization") For
   * google.com this is the badge number (e.g. 2 for Larry Page).
   *
   * @param string $employeeId
   */
  public function setEmployeeId($employeeId)
  {
    $this->employeeId = $employeeId;
  }
  /**
   * @return string
   */
  public function getEmployeeId()
  {
    return $this->employeeId;
  }
  /**
   * A fingerprint used by PAPI to reliably determine if a resource has changed
   * Externally it is used as part of the etag.
   *
   * @param string $fingerprint
   */
  public function setFingerprint($fingerprint)
  {
    $this->fingerprint = $fingerprint;
  }
  /**
   * @return string
   */
  public function getFingerprint()
  {
    return $this->fingerprint;
  }
  /**
   * Full-time equivalent (in ‰) (e.g. 800 for a person who's working 80%).
   *
   * @param string $ftePermille
   */
  public function setFtePermille($ftePermille)
  {
    $this->ftePermille = $ftePermille;
  }
  /**
   * @return string
   */
  public function getFtePermille()
  {
    return $this->ftePermille;
  }
  /**
   * @param MapInfo $geoLocation
   */
  public function setGeoLocation(MapInfo $geoLocation)
  {
    $this->geoLocation = $geoLocation;
  }
  /**
   * @return MapInfo
   */
  public function getGeoLocation()
  {
    return $this->geoLocation;
  }
  /**
   * @param string $gmailUrl
   */
  public function setGmailUrl($gmailUrl)
  {
    $this->gmailUrl = $gmailUrl;
  }
  /**
   * @return string
   */
  public function getGmailUrl()
  {
    return $this->gmailUrl;
  }
  /**
   * Profile owner's job title (e.g. "Software Engineer"). For google.com this
   * is the Workday preferred job title.
   *
   * @param string $jobTitle
   */
  public function setJobTitle($jobTitle)
  {
    $this->jobTitle = $jobTitle;
  }
  /**
   * @return string
   */
  public function getJobTitle()
  {
    return $this->jobTitle;
  }
  /**
   * List of keys to use from the map 'keywords'.
   *
   * @param string[] $keywordTypes
   */
  public function setKeywordTypes($keywordTypes)
  {
    $this->keywordTypes = $keywordTypes;
  }
  /**
   * @return string[]
   */
  public function getKeywordTypes()
  {
    return $this->keywordTypes;
  }
  /**
   * Custom keywords the domain admin has added.
   *
   * @param string[] $keywords
   */
  public function setKeywords($keywords)
  {
    $this->keywords = $keywords;
  }
  /**
   * @return string[]
   */
  public function getKeywords()
  {
    return $this->keywords;
  }
  /**
   * Custom links the profile owner has added.
   *
   * @param EnterpriseTopazFrontendTeamsLink[] $links
   */
  public function setLinks($links)
  {
    $this->links = $links;
  }
  /**
   * @return EnterpriseTopazFrontendTeamsLink[]
   */
  public function getLinks()
  {
    return $this->links;
  }
  /**
   * Detailed desk location within the company. For google.com this is the desk
   * location code (e.g. "DE-MUC-ARP-6T2-6T2C0C") if the person has a desk.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The profile owner's management chain from top to bottom, where managers[0]
   * is the CEO, manager[N-2] is the person's manager's manager and
   * managers[N-1] is the person's direct manager. Note that not all fields of
   * these PersonCores will be set, in particular, relationships will be empty.
   *
   * @param PersonCore[] $managers
   */
  public function setManagers($managers)
  {
    $this->managers = $managers;
  }
  /**
   * @return PersonCore[]
   */
  public function getManagers()
  {
    return $this->managers;
  }
  /**
   * Custom mission statement the profile owner has added.
   *
   * @param string $mission
   */
  public function setMission($mission)
  {
    $this->mission = $mission;
  }
  /**
   * @return string
   */
  public function getMission()
  {
    return $this->mission;
  }
  /**
   * Human-readable Unicode display name.
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
   * Office/building identifier within the company. For google.com this is the
   * office code (e.g. "DE-MUC-ARP").
   *
   * @param string $officeLocation
   */
  public function setOfficeLocation($officeLocation)
  {
    $this->officeLocation = $officeLocation;
  }
  /**
   * @return string
   */
  public function getOfficeLocation()
  {
    return $this->officeLocation;
  }
  /**
   * The person's obfuscated Gaia ID.
   *
   * @param string $personId
   */
  public function setPersonId($personId)
  {
    $this->personId = $personId;
  }
  /**
   * @return string
   */
  public function getPersonId()
  {
    return $this->personId;
  }
  /**
   * @param EnterpriseTopazFrontendTeamsPersonCorePhoneNumber[] $phoneNumbers
   */
  public function setPhoneNumbers($phoneNumbers)
  {
    $this->phoneNumbers = $phoneNumbers;
  }
  /**
   * @return EnterpriseTopazFrontendTeamsPersonCorePhoneNumber[]
   */
  public function getPhoneNumbers()
  {
    return $this->phoneNumbers;
  }
  /**
   * Person photo.
   *
   * @param SafeUrlProto $photoUrl
   */
  public function setPhotoUrl(SafeUrlProto $photoUrl)
  {
    $this->photoUrl = $photoUrl;
  }
  /**
   * @return SafeUrlProto
   */
  public function getPhotoUrl()
  {
    return $this->photoUrl;
  }
  /**
   * Postal address of office/building.
   *
   * @param string $postalAddress
   */
  public function setPostalAddress($postalAddress)
  {
    $this->postalAddress = $postalAddress;
  }
  /**
   * @return string
   */
  public function getPostalAddress()
  {
    return $this->postalAddress;
  }
  /**
   * Total count of the profile owner's direct reports.
   *
   * @param int $totalDirectReportsCount
   */
  public function setTotalDirectReportsCount($totalDirectReportsCount)
  {
    $this->totalDirectReportsCount = $totalDirectReportsCount;
  }
  /**
   * @return int
   */
  public function getTotalDirectReportsCount()
  {
    return $this->totalDirectReportsCount;
  }
  /**
   * Total count of the profile owner's dotted-line reports.
   *
   * @param int $totalDlrCount
   */
  public function setTotalDlrCount($totalDlrCount)
  {
    $this->totalDlrCount = $totalDlrCount;
  }
  /**
   * @return int
   */
  public function getTotalDlrCount()
  {
    return $this->totalDlrCount;
  }
  /**
   * The sum of all profile owner's reports and their own full-time-equivalents
   * in ‰ (e.g. 1800 if one report is working 80% and profile owner 100%).
   *
   * @param string $totalFteCount
   */
  public function setTotalFteCount($totalFteCount)
  {
    $this->totalFteCount = $totalFteCount;
  }
  /**
   * @return string
   */
  public function getTotalFteCount()
  {
    return $this->totalFteCount;
  }
  /**
   * External ID of type "login_id" for the profile. For google.com this is the
   * username/LDAP.
   *
   * @param string $username
   */
  public function setUsername($username)
  {
    $this->username = $username;
  }
  /**
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }
  /**
   * @param string $waldoComeBackTime
   */
  public function setWaldoComeBackTime($waldoComeBackTime)
  {
    $this->waldoComeBackTime = $waldoComeBackTime;
  }
  /**
   * @return string
   */
  public function getWaldoComeBackTime()
  {
    return $this->waldoComeBackTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PersonCore::class, 'Google_Service_CloudSearch_PersonCore');
