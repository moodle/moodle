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

class Organization extends \Google\Model
{
  /**
   * The person's cost center at the organization.
   *
   * @var string
   */
  public $costCenter;
  /**
   * True if the organization is the person's current organization; false if the
   * organization is a past organization.
   *
   * @var bool
   */
  public $current;
  /**
   * The person's department at the organization.
   *
   * @var string
   */
  public $department;
  /**
   * The domain name associated with the organization; for example,
   * `google.com`.
   *
   * @var string
   */
  public $domain;
  protected $endDateType = Date::class;
  protected $endDateDataType = '';
  /**
   * Output only. The type of the organization translated and formatted in the
   * viewer's account locale or the `Accept-Language` HTTP header locale.
   *
   * @var string
   */
  public $formattedType;
  /**
   * The person's full-time equivalent millipercent within the organization
   * (100000 = 100%).
   *
   * @var int
   */
  public $fullTimeEquivalentMillipercent;
  /**
   * The person's job description at the organization.
   *
   * @var string
   */
  public $jobDescription;
  /**
   * The location of the organization office the person works at.
   *
   * @var string
   */
  public $location;
  protected $metadataType = FieldMetadata::class;
  protected $metadataDataType = '';
  /**
   * The name of the organization.
   *
   * @var string
   */
  public $name;
  /**
   * The phonetic name of the organization.
   *
   * @var string
   */
  public $phoneticName;
  protected $startDateType = Date::class;
  protected $startDateDataType = '';
  /**
   * The symbol associated with the organization; for example, a stock ticker
   * symbol, abbreviation, or acronym.
   *
   * @var string
   */
  public $symbol;
  /**
   * The person's job title at the organization.
   *
   * @var string
   */
  public $title;
  /**
   * The type of the organization. The type can be custom or one of these
   * predefined values: * `work` * `school`
   *
   * @var string
   */
  public $type;

  /**
   * The person's cost center at the organization.
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
   * True if the organization is the person's current organization; false if the
   * organization is a past organization.
   *
   * @param bool $current
   */
  public function setCurrent($current)
  {
    $this->current = $current;
  }
  /**
   * @return bool
   */
  public function getCurrent()
  {
    return $this->current;
  }
  /**
   * The person's department at the organization.
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
   * The domain name associated with the organization; for example,
   * `google.com`.
   *
   * @param string $domain
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * The end date when the person left the organization.
   *
   * @param Date $endDate
   */
  public function setEndDate(Date $endDate)
  {
    $this->endDate = $endDate;
  }
  /**
   * @return Date
   */
  public function getEndDate()
  {
    return $this->endDate;
  }
  /**
   * Output only. The type of the organization translated and formatted in the
   * viewer's account locale or the `Accept-Language` HTTP header locale.
   *
   * @param string $formattedType
   */
  public function setFormattedType($formattedType)
  {
    $this->formattedType = $formattedType;
  }
  /**
   * @return string
   */
  public function getFormattedType()
  {
    return $this->formattedType;
  }
  /**
   * The person's full-time equivalent millipercent within the organization
   * (100000 = 100%).
   *
   * @param int $fullTimeEquivalentMillipercent
   */
  public function setFullTimeEquivalentMillipercent($fullTimeEquivalentMillipercent)
  {
    $this->fullTimeEquivalentMillipercent = $fullTimeEquivalentMillipercent;
  }
  /**
   * @return int
   */
  public function getFullTimeEquivalentMillipercent()
  {
    return $this->fullTimeEquivalentMillipercent;
  }
  /**
   * The person's job description at the organization.
   *
   * @param string $jobDescription
   */
  public function setJobDescription($jobDescription)
  {
    $this->jobDescription = $jobDescription;
  }
  /**
   * @return string
   */
  public function getJobDescription()
  {
    return $this->jobDescription;
  }
  /**
   * The location of the organization office the person works at.
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
   * Metadata about the organization.
   *
   * @param FieldMetadata $metadata
   */
  public function setMetadata(FieldMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return FieldMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * The name of the organization.
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
   * The phonetic name of the organization.
   *
   * @param string $phoneticName
   */
  public function setPhoneticName($phoneticName)
  {
    $this->phoneticName = $phoneticName;
  }
  /**
   * @return string
   */
  public function getPhoneticName()
  {
    return $this->phoneticName;
  }
  /**
   * The start date when the person joined the organization.
   *
   * @param Date $startDate
   */
  public function setStartDate(Date $startDate)
  {
    $this->startDate = $startDate;
  }
  /**
   * @return Date
   */
  public function getStartDate()
  {
    return $this->startDate;
  }
  /**
   * The symbol associated with the organization; for example, a stock ticker
   * symbol, abbreviation, or acronym.
   *
   * @param string $symbol
   */
  public function setSymbol($symbol)
  {
    $this->symbol = $symbol;
  }
  /**
   * @return string
   */
  public function getSymbol()
  {
    return $this->symbol;
  }
  /**
   * The person's job title at the organization.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * The type of the organization. The type can be custom or one of these
   * predefined values: * `work` * `school`
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Organization::class, 'Google_Service_PeopleService_Organization');
