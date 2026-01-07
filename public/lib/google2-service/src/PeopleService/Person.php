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

namespace Google\Service\PeopleService;

class Person extends \Google\Collection
{
  /**
   * Unspecified.
   */
  public const AGE_RANGE_AGE_RANGE_UNSPECIFIED = 'AGE_RANGE_UNSPECIFIED';
  /**
   * Younger than eighteen.
   */
  public const AGE_RANGE_LESS_THAN_EIGHTEEN = 'LESS_THAN_EIGHTEEN';
  /**
   * Between eighteen and twenty.
   */
  public const AGE_RANGE_EIGHTEEN_TO_TWENTY = 'EIGHTEEN_TO_TWENTY';
  /**
   * Twenty-one and older.
   */
  public const AGE_RANGE_TWENTY_ONE_OR_OLDER = 'TWENTY_ONE_OR_OLDER';
  protected $collection_key = 'userDefined';
  protected $addressesType = Address::class;
  protected $addressesDataType = 'array';
  /**
   * Output only. **DEPRECATED** (Please use `person.ageRanges` instead) The
   * person's age range.
   *
   * @deprecated
   * @var string
   */
  public $ageRange;
  protected $ageRangesType = AgeRangeType::class;
  protected $ageRangesDataType = 'array';
  protected $biographiesType = Biography::class;
  protected $biographiesDataType = 'array';
  protected $birthdaysType = Birthday::class;
  protected $birthdaysDataType = 'array';
  protected $braggingRightsType = BraggingRights::class;
  protected $braggingRightsDataType = 'array';
  protected $calendarUrlsType = CalendarUrl::class;
  protected $calendarUrlsDataType = 'array';
  protected $clientDataType = ClientData::class;
  protected $clientDataDataType = 'array';
  protected $coverPhotosType = CoverPhoto::class;
  protected $coverPhotosDataType = 'array';
  protected $emailAddressesType = EmailAddress::class;
  protected $emailAddressesDataType = 'array';
  /**
   * The [HTTP entity tag](https://en.wikipedia.org/wiki/HTTP_ETag) of the
   * resource. Used for web cache validation.
   *
   * @var string
   */
  public $etag;
  protected $eventsType = Event::class;
  protected $eventsDataType = 'array';
  protected $externalIdsType = ExternalId::class;
  protected $externalIdsDataType = 'array';
  protected $fileAsesType = FileAs::class;
  protected $fileAsesDataType = 'array';
  protected $gendersType = Gender::class;
  protected $gendersDataType = 'array';
  protected $imClientsType = ImClient::class;
  protected $imClientsDataType = 'array';
  protected $interestsType = Interest::class;
  protected $interestsDataType = 'array';
  protected $localesType = Locale::class;
  protected $localesDataType = 'array';
  protected $locationsType = Location::class;
  protected $locationsDataType = 'array';
  protected $membershipsType = Membership::class;
  protected $membershipsDataType = 'array';
  protected $metadataType = PersonMetadata::class;
  protected $metadataDataType = '';
  protected $miscKeywordsType = MiscKeyword::class;
  protected $miscKeywordsDataType = 'array';
  protected $namesType = Name::class;
  protected $namesDataType = 'array';
  protected $nicknamesType = Nickname::class;
  protected $nicknamesDataType = 'array';
  protected $occupationsType = Occupation::class;
  protected $occupationsDataType = 'array';
  protected $organizationsType = Organization::class;
  protected $organizationsDataType = 'array';
  protected $phoneNumbersType = PhoneNumber::class;
  protected $phoneNumbersDataType = 'array';
  protected $photosType = Photo::class;
  protected $photosDataType = 'array';
  protected $relationsType = Relation::class;
  protected $relationsDataType = 'array';
  protected $relationshipInterestsType = RelationshipInterest::class;
  protected $relationshipInterestsDataType = 'array';
  protected $relationshipStatusesType = RelationshipStatus::class;
  protected $relationshipStatusesDataType = 'array';
  protected $residencesType = Residence::class;
  protected $residencesDataType = 'array';
  /**
   * The resource name for the person, assigned by the server. An ASCII string
   * in the form of `people/{person_id}`.
   *
   * @var string
   */
  public $resourceName;
  protected $sipAddressesType = SipAddress::class;
  protected $sipAddressesDataType = 'array';
  protected $skillsType = Skill::class;
  protected $skillsDataType = 'array';
  protected $taglinesType = Tagline::class;
  protected $taglinesDataType = 'array';
  protected $urlsType = Url::class;
  protected $urlsDataType = 'array';
  protected $userDefinedType = UserDefined::class;
  protected $userDefinedDataType = 'array';

  /**
   * The person's street addresses.
   *
   * @param Address[] $addresses
   */
  public function setAddresses($addresses)
  {
    $this->addresses = $addresses;
  }
  /**
   * @return Address[]
   */
  public function getAddresses()
  {
    return $this->addresses;
  }
  /**
   * Output only. **DEPRECATED** (Please use `person.ageRanges` instead) The
   * person's age range.
   *
   * Accepted values: AGE_RANGE_UNSPECIFIED, LESS_THAN_EIGHTEEN,
   * EIGHTEEN_TO_TWENTY, TWENTY_ONE_OR_OLDER
   *
   * @deprecated
   * @param self::AGE_RANGE_* $ageRange
   */
  public function setAgeRange($ageRange)
  {
    $this->ageRange = $ageRange;
  }
  /**
   * @deprecated
   * @return self::AGE_RANGE_*
   */
  public function getAgeRange()
  {
    return $this->ageRange;
  }
  /**
   * Output only. The person's age ranges.
   *
   * @param AgeRangeType[] $ageRanges
   */
  public function setAgeRanges($ageRanges)
  {
    $this->ageRanges = $ageRanges;
  }
  /**
   * @return AgeRangeType[]
   */
  public function getAgeRanges()
  {
    return $this->ageRanges;
  }
  /**
   * The person's biographies. This field is a singleton for contact sources.
   *
   * @param Biography[] $biographies
   */
  public function setBiographies($biographies)
  {
    $this->biographies = $biographies;
  }
  /**
   * @return Biography[]
   */
  public function getBiographies()
  {
    return $this->biographies;
  }
  /**
   * The person's birthdays. This field is a singleton for contact sources.
   *
   * @param Birthday[] $birthdays
   */
  public function setBirthdays($birthdays)
  {
    $this->birthdays = $birthdays;
  }
  /**
   * @return Birthday[]
   */
  public function getBirthdays()
  {
    return $this->birthdays;
  }
  /**
   * **DEPRECATED**: No data will be returned The person's bragging rights.
   *
   * @deprecated
   * @param BraggingRights[] $braggingRights
   */
  public function setBraggingRights($braggingRights)
  {
    $this->braggingRights = $braggingRights;
  }
  /**
   * @deprecated
   * @return BraggingRights[]
   */
  public function getBraggingRights()
  {
    return $this->braggingRights;
  }
  /**
   * The person's calendar URLs.
   *
   * @param CalendarUrl[] $calendarUrls
   */
  public function setCalendarUrls($calendarUrls)
  {
    $this->calendarUrls = $calendarUrls;
  }
  /**
   * @return CalendarUrl[]
   */
  public function getCalendarUrls()
  {
    return $this->calendarUrls;
  }
  /**
   * The person's client data.
   *
   * @param ClientData[] $clientData
   */
  public function setClientData($clientData)
  {
    $this->clientData = $clientData;
  }
  /**
   * @return ClientData[]
   */
  public function getClientData()
  {
    return $this->clientData;
  }
  /**
   * Output only. The person's cover photos.
   *
   * @param CoverPhoto[] $coverPhotos
   */
  public function setCoverPhotos($coverPhotos)
  {
    $this->coverPhotos = $coverPhotos;
  }
  /**
   * @return CoverPhoto[]
   */
  public function getCoverPhotos()
  {
    return $this->coverPhotos;
  }
  /**
   * The person's email addresses. For `people.connections.list` and
   * `otherContacts.list` the number of email addresses is limited to 100. If a
   * Person has more email addresses the entire set can be obtained by calling
   * GetPeople.
   *
   * @param EmailAddress[] $emailAddresses
   */
  public function setEmailAddresses($emailAddresses)
  {
    $this->emailAddresses = $emailAddresses;
  }
  /**
   * @return EmailAddress[]
   */
  public function getEmailAddresses()
  {
    return $this->emailAddresses;
  }
  /**
   * The [HTTP entity tag](https://en.wikipedia.org/wiki/HTTP_ETag) of the
   * resource. Used for web cache validation.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * The person's events.
   *
   * @param Event[] $events
   */
  public function setEvents($events)
  {
    $this->events = $events;
  }
  /**
   * @return Event[]
   */
  public function getEvents()
  {
    return $this->events;
  }
  /**
   * The person's external IDs.
   *
   * @param ExternalId[] $externalIds
   */
  public function setExternalIds($externalIds)
  {
    $this->externalIds = $externalIds;
  }
  /**
   * @return ExternalId[]
   */
  public function getExternalIds()
  {
    return $this->externalIds;
  }
  /**
   * The person's file-ases.
   *
   * @param FileAs[] $fileAses
   */
  public function setFileAses($fileAses)
  {
    $this->fileAses = $fileAses;
  }
  /**
   * @return FileAs[]
   */
  public function getFileAses()
  {
    return $this->fileAses;
  }
  /**
   * The person's genders. This field is a singleton for contact sources.
   *
   * @param Gender[] $genders
   */
  public function setGenders($genders)
  {
    $this->genders = $genders;
  }
  /**
   * @return Gender[]
   */
  public function getGenders()
  {
    return $this->genders;
  }
  /**
   * The person's instant messaging clients.
   *
   * @param ImClient[] $imClients
   */
  public function setImClients($imClients)
  {
    $this->imClients = $imClients;
  }
  /**
   * @return ImClient[]
   */
  public function getImClients()
  {
    return $this->imClients;
  }
  /**
   * The person's interests.
   *
   * @param Interest[] $interests
   */
  public function setInterests($interests)
  {
    $this->interests = $interests;
  }
  /**
   * @return Interest[]
   */
  public function getInterests()
  {
    return $this->interests;
  }
  /**
   * The person's locale preferences.
   *
   * @param Locale[] $locales
   */
  public function setLocales($locales)
  {
    $this->locales = $locales;
  }
  /**
   * @return Locale[]
   */
  public function getLocales()
  {
    return $this->locales;
  }
  /**
   * The person's locations.
   *
   * @param Location[] $locations
   */
  public function setLocations($locations)
  {
    $this->locations = $locations;
  }
  /**
   * @return Location[]
   */
  public function getLocations()
  {
    return $this->locations;
  }
  /**
   * The person's group memberships.
   *
   * @param Membership[] $memberships
   */
  public function setMemberships($memberships)
  {
    $this->memberships = $memberships;
  }
  /**
   * @return Membership[]
   */
  public function getMemberships()
  {
    return $this->memberships;
  }
  /**
   * Output only. Metadata about the person.
   *
   * @param PersonMetadata $metadata
   */
  public function setMetadata(PersonMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return PersonMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * The person's miscellaneous keywords.
   *
   * @param MiscKeyword[] $miscKeywords
   */
  public function setMiscKeywords($miscKeywords)
  {
    $this->miscKeywords = $miscKeywords;
  }
  /**
   * @return MiscKeyword[]
   */
  public function getMiscKeywords()
  {
    return $this->miscKeywords;
  }
  /**
   * The person's names. This field is a singleton for contact sources.
   *
   * @param Name[] $names
   */
  public function setNames($names)
  {
    $this->names = $names;
  }
  /**
   * @return Name[]
   */
  public function getNames()
  {
    return $this->names;
  }
  /**
   * The person's nicknames.
   *
   * @param Nickname[] $nicknames
   */
  public function setNicknames($nicknames)
  {
    $this->nicknames = $nicknames;
  }
  /**
   * @return Nickname[]
   */
  public function getNicknames()
  {
    return $this->nicknames;
  }
  /**
   * The person's occupations.
   *
   * @param Occupation[] $occupations
   */
  public function setOccupations($occupations)
  {
    $this->occupations = $occupations;
  }
  /**
   * @return Occupation[]
   */
  public function getOccupations()
  {
    return $this->occupations;
  }
  /**
   * The person's past or current organizations.
   *
   * @param Organization[] $organizations
   */
  public function setOrganizations($organizations)
  {
    $this->organizations = $organizations;
  }
  /**
   * @return Organization[]
   */
  public function getOrganizations()
  {
    return $this->organizations;
  }
  /**
   * The person's phone numbers. For `people.connections.list` and
   * `otherContacts.list` the number of phone numbers is limited to 100. If a
   * Person has more phone numbers the entire set can be obtained by calling
   * GetPeople.
   *
   * @param PhoneNumber[] $phoneNumbers
   */
  public function setPhoneNumbers($phoneNumbers)
  {
    $this->phoneNumbers = $phoneNumbers;
  }
  /**
   * @return PhoneNumber[]
   */
  public function getPhoneNumbers()
  {
    return $this->phoneNumbers;
  }
  /**
   * Output only. The person's photos.
   *
   * @param Photo[] $photos
   */
  public function setPhotos($photos)
  {
    $this->photos = $photos;
  }
  /**
   * @return Photo[]
   */
  public function getPhotos()
  {
    return $this->photos;
  }
  /**
   * The person's relations.
   *
   * @param Relation[] $relations
   */
  public function setRelations($relations)
  {
    $this->relations = $relations;
  }
  /**
   * @return Relation[]
   */
  public function getRelations()
  {
    return $this->relations;
  }
  /**
   * Output only. **DEPRECATED**: No data will be returned The person's
   * relationship interests.
   *
   * @deprecated
   * @param RelationshipInterest[] $relationshipInterests
   */
  public function setRelationshipInterests($relationshipInterests)
  {
    $this->relationshipInterests = $relationshipInterests;
  }
  /**
   * @deprecated
   * @return RelationshipInterest[]
   */
  public function getRelationshipInterests()
  {
    return $this->relationshipInterests;
  }
  /**
   * Output only. **DEPRECATED**: No data will be returned The person's
   * relationship statuses.
   *
   * @deprecated
   * @param RelationshipStatus[] $relationshipStatuses
   */
  public function setRelationshipStatuses($relationshipStatuses)
  {
    $this->relationshipStatuses = $relationshipStatuses;
  }
  /**
   * @deprecated
   * @return RelationshipStatus[]
   */
  public function getRelationshipStatuses()
  {
    return $this->relationshipStatuses;
  }
  /**
   * **DEPRECATED**: (Please use `person.locations` instead) The person's
   * residences.
   *
   * @deprecated
   * @param Residence[] $residences
   */
  public function setResidences($residences)
  {
    $this->residences = $residences;
  }
  /**
   * @deprecated
   * @return Residence[]
   */
  public function getResidences()
  {
    return $this->residences;
  }
  /**
   * The resource name for the person, assigned by the server. An ASCII string
   * in the form of `people/{person_id}`.
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * The person's SIP addresses.
   *
   * @param SipAddress[] $sipAddresses
   */
  public function setSipAddresses($sipAddresses)
  {
    $this->sipAddresses = $sipAddresses;
  }
  /**
   * @return SipAddress[]
   */
  public function getSipAddresses()
  {
    return $this->sipAddresses;
  }
  /**
   * The person's skills.
   *
   * @param Skill[] $skills
   */
  public function setSkills($skills)
  {
    $this->skills = $skills;
  }
  /**
   * @return Skill[]
   */
  public function getSkills()
  {
    return $this->skills;
  }
  /**
   * Output only. **DEPRECATED**: No data will be returned The person's
   * taglines.
   *
   * @deprecated
   * @param Tagline[] $taglines
   */
  public function setTaglines($taglines)
  {
    $this->taglines = $taglines;
  }
  /**
   * @deprecated
   * @return Tagline[]
   */
  public function getTaglines()
  {
    return $this->taglines;
  }
  /**
   * The person's associated URLs.
   *
   * @param Url[] $urls
   */
  public function setUrls($urls)
  {
    $this->urls = $urls;
  }
  /**
   * @return Url[]
   */
  public function getUrls()
  {
    return $this->urls;
  }
  /**
   * The person's user defined data.
   *
   * @param UserDefined[] $userDefined
   */
  public function setUserDefined($userDefined)
  {
    $this->userDefined = $userDefined;
  }
  /**
   * @return UserDefined[]
   */
  public function getUserDefined()
  {
    return $this->userDefined;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Person::class, 'Google_Service_PeopleService_Person');
