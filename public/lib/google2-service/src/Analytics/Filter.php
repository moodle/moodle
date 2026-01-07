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

class Filter extends \Google\Model
{
  /**
   * Account ID to which this filter belongs.
   *
   * @var string
   */
  public $accountId;
  protected $advancedDetailsType = FilterAdvancedDetails::class;
  protected $advancedDetailsDataType = '';
  /**
   * Time this filter was created.
   *
   * @var string
   */
  public $created;
  protected $excludeDetailsType = FilterExpression::class;
  protected $excludeDetailsDataType = '';
  /**
   * Filter ID.
   *
   * @var string
   */
  public $id;
  protected $includeDetailsType = FilterExpression::class;
  protected $includeDetailsDataType = '';
  /**
   * Resource type for Analytics filter.
   *
   * @var string
   */
  public $kind;
  protected $lowercaseDetailsType = FilterLowercaseDetails::class;
  protected $lowercaseDetailsDataType = '';
  /**
   * Name of this filter.
   *
   * @var string
   */
  public $name;
  protected $parentLinkType = FilterParentLink::class;
  protected $parentLinkDataType = '';
  protected $searchAndReplaceDetailsType = FilterSearchAndReplaceDetails::class;
  protected $searchAndReplaceDetailsDataType = '';
  /**
   * Link for this filter.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Type of this filter. Possible values are INCLUDE, EXCLUDE, LOWERCASE,
   * UPPERCASE, SEARCH_AND_REPLACE and ADVANCED.
   *
   * @var string
   */
  public $type;
  /**
   * Time this filter was last modified.
   *
   * @var string
   */
  public $updated;
  protected $uppercaseDetailsType = FilterUppercaseDetails::class;
  protected $uppercaseDetailsDataType = '';

  /**
   * Account ID to which this filter belongs.
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
   * Details for the filter of the type ADVANCED.
   *
   * @param FilterAdvancedDetails $advancedDetails
   */
  public function setAdvancedDetails(FilterAdvancedDetails $advancedDetails)
  {
    $this->advancedDetails = $advancedDetails;
  }
  /**
   * @return FilterAdvancedDetails
   */
  public function getAdvancedDetails()
  {
    return $this->advancedDetails;
  }
  /**
   * Time this filter was created.
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
   * Details for the filter of the type EXCLUDE.
   *
   * @param FilterExpression $excludeDetails
   */
  public function setExcludeDetails(FilterExpression $excludeDetails)
  {
    $this->excludeDetails = $excludeDetails;
  }
  /**
   * @return FilterExpression
   */
  public function getExcludeDetails()
  {
    return $this->excludeDetails;
  }
  /**
   * Filter ID.
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
   * Details for the filter of the type INCLUDE.
   *
   * @param FilterExpression $includeDetails
   */
  public function setIncludeDetails(FilterExpression $includeDetails)
  {
    $this->includeDetails = $includeDetails;
  }
  /**
   * @return FilterExpression
   */
  public function getIncludeDetails()
  {
    return $this->includeDetails;
  }
  /**
   * Resource type for Analytics filter.
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
   * Details for the filter of the type LOWER.
   *
   * @param FilterLowercaseDetails $lowercaseDetails
   */
  public function setLowercaseDetails(FilterLowercaseDetails $lowercaseDetails)
  {
    $this->lowercaseDetails = $lowercaseDetails;
  }
  /**
   * @return FilterLowercaseDetails
   */
  public function getLowercaseDetails()
  {
    return $this->lowercaseDetails;
  }
  /**
   * Name of this filter.
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
   * Parent link for this filter. Points to the account to which this filter
   * belongs.
   *
   * @param FilterParentLink $parentLink
   */
  public function setParentLink(FilterParentLink $parentLink)
  {
    $this->parentLink = $parentLink;
  }
  /**
   * @return FilterParentLink
   */
  public function getParentLink()
  {
    return $this->parentLink;
  }
  /**
   * Details for the filter of the type SEARCH_AND_REPLACE.
   *
   * @param FilterSearchAndReplaceDetails $searchAndReplaceDetails
   */
  public function setSearchAndReplaceDetails(FilterSearchAndReplaceDetails $searchAndReplaceDetails)
  {
    $this->searchAndReplaceDetails = $searchAndReplaceDetails;
  }
  /**
   * @return FilterSearchAndReplaceDetails
   */
  public function getSearchAndReplaceDetails()
  {
    return $this->searchAndReplaceDetails;
  }
  /**
   * Link for this filter.
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
   * Type of this filter. Possible values are INCLUDE, EXCLUDE, LOWERCASE,
   * UPPERCASE, SEARCH_AND_REPLACE and ADVANCED.
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
   * Time this filter was last modified.
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
   * Details for the filter of the type UPPER.
   *
   * @param FilterUppercaseDetails $uppercaseDetails
   */
  public function setUppercaseDetails(FilterUppercaseDetails $uppercaseDetails)
  {
    $this->uppercaseDetails = $uppercaseDetails;
  }
  /**
   * @return FilterUppercaseDetails
   */
  public function getUppercaseDetails()
  {
    return $this->uppercaseDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Filter::class, 'Google_Service_Analytics_Filter');
