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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2DataProfileFinding extends \Google\Model
{
  /**
   * Unused.
   */
  public const RESOURCE_VISIBILITY_RESOURCE_VISIBILITY_UNSPECIFIED = 'RESOURCE_VISIBILITY_UNSPECIFIED';
  /**
   * Visible to any user.
   */
  public const RESOURCE_VISIBILITY_RESOURCE_VISIBILITY_PUBLIC = 'RESOURCE_VISIBILITY_PUBLIC';
  /**
   * May contain public items. For example, if a Cloud Storage bucket has
   * uniform bucket level access disabled, some objects inside it may be public,
   * but none are known yet.
   */
  public const RESOURCE_VISIBILITY_RESOURCE_VISIBILITY_INCONCLUSIVE = 'RESOURCE_VISIBILITY_INCONCLUSIVE';
  /**
   * Visible only to specific users.
   */
  public const RESOURCE_VISIBILITY_RESOURCE_VISIBILITY_RESTRICTED = 'RESOURCE_VISIBILITY_RESTRICTED';
  /**
   * Resource name of the data profile associated with the finding.
   *
   * @var string
   */
  public $dataProfileResourceName;
  protected $dataSourceTypeType = GooglePrivacyDlpV2DataSourceType::class;
  protected $dataSourceTypeDataType = '';
  /**
   * A unique identifier for the finding.
   *
   * @var string
   */
  public $findingId;
  /**
   * The [full resource name](https://cloud.google.com/apis/design/resource_name
   * s#full_resource_name) of the resource profiled for this finding.
   *
   * @var string
   */
  public $fullResourceName;
  protected $infotypeType = GooglePrivacyDlpV2InfoType::class;
  protected $infotypeDataType = '';
  protected $locationType = GooglePrivacyDlpV2DataProfileFindingLocation::class;
  protected $locationDataType = '';
  /**
   * The content that was found. Even if the content is not textual, it may be
   * converted to a textual representation here. If the finding exceeds 4096
   * bytes in length, the quote may be omitted.
   *
   * @var string
   */
  public $quote;
  protected $quoteInfoType = GooglePrivacyDlpV2QuoteInfo::class;
  protected $quoteInfoDataType = '';
  /**
   * How broadly a resource has been shared.
   *
   * @var string
   */
  public $resourceVisibility;
  /**
   * Timestamp when the finding was detected.
   *
   * @var string
   */
  public $timestamp;

  /**
   * Resource name of the data profile associated with the finding.
   *
   * @param string $dataProfileResourceName
   */
  public function setDataProfileResourceName($dataProfileResourceName)
  {
    $this->dataProfileResourceName = $dataProfileResourceName;
  }
  /**
   * @return string
   */
  public function getDataProfileResourceName()
  {
    return $this->dataProfileResourceName;
  }
  /**
   * The type of the resource that was profiled.
   *
   * @param GooglePrivacyDlpV2DataSourceType $dataSourceType
   */
  public function setDataSourceType(GooglePrivacyDlpV2DataSourceType $dataSourceType)
  {
    $this->dataSourceType = $dataSourceType;
  }
  /**
   * @return GooglePrivacyDlpV2DataSourceType
   */
  public function getDataSourceType()
  {
    return $this->dataSourceType;
  }
  /**
   * A unique identifier for the finding.
   *
   * @param string $findingId
   */
  public function setFindingId($findingId)
  {
    $this->findingId = $findingId;
  }
  /**
   * @return string
   */
  public function getFindingId()
  {
    return $this->findingId;
  }
  /**
   * The [full resource name](https://cloud.google.com/apis/design/resource_name
   * s#full_resource_name) of the resource profiled for this finding.
   *
   * @param string $fullResourceName
   */
  public function setFullResourceName($fullResourceName)
  {
    $this->fullResourceName = $fullResourceName;
  }
  /**
   * @return string
   */
  public function getFullResourceName()
  {
    return $this->fullResourceName;
  }
  /**
   * The [type of content](https://cloud.google.com/sensitive-data-
   * protection/docs/infotypes-reference) that might have been found.
   *
   * @param GooglePrivacyDlpV2InfoType $infotype
   */
  public function setInfotype(GooglePrivacyDlpV2InfoType $infotype)
  {
    $this->infotype = $infotype;
  }
  /**
   * @return GooglePrivacyDlpV2InfoType
   */
  public function getInfotype()
  {
    return $this->infotype;
  }
  /**
   * Where the content was found.
   *
   * @param GooglePrivacyDlpV2DataProfileFindingLocation $location
   */
  public function setLocation(GooglePrivacyDlpV2DataProfileFindingLocation $location)
  {
    $this->location = $location;
  }
  /**
   * @return GooglePrivacyDlpV2DataProfileFindingLocation
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The content that was found. Even if the content is not textual, it may be
   * converted to a textual representation here. If the finding exceeds 4096
   * bytes in length, the quote may be omitted.
   *
   * @param string $quote
   */
  public function setQuote($quote)
  {
    $this->quote = $quote;
  }
  /**
   * @return string
   */
  public function getQuote()
  {
    return $this->quote;
  }
  /**
   * Contains data parsed from quotes. Currently supported infoTypes: DATE,
   * DATE_OF_BIRTH, and TIME.
   *
   * @param GooglePrivacyDlpV2QuoteInfo $quoteInfo
   */
  public function setQuoteInfo(GooglePrivacyDlpV2QuoteInfo $quoteInfo)
  {
    $this->quoteInfo = $quoteInfo;
  }
  /**
   * @return GooglePrivacyDlpV2QuoteInfo
   */
  public function getQuoteInfo()
  {
    return $this->quoteInfo;
  }
  /**
   * How broadly a resource has been shared.
   *
   * Accepted values: RESOURCE_VISIBILITY_UNSPECIFIED,
   * RESOURCE_VISIBILITY_PUBLIC, RESOURCE_VISIBILITY_INCONCLUSIVE,
   * RESOURCE_VISIBILITY_RESTRICTED
   *
   * @param self::RESOURCE_VISIBILITY_* $resourceVisibility
   */
  public function setResourceVisibility($resourceVisibility)
  {
    $this->resourceVisibility = $resourceVisibility;
  }
  /**
   * @return self::RESOURCE_VISIBILITY_*
   */
  public function getResourceVisibility()
  {
    return $this->resourceVisibility;
  }
  /**
   * Timestamp when the finding was detected.
   *
   * @param string $timestamp
   */
  public function setTimestamp($timestamp)
  {
    $this->timestamp = $timestamp;
  }
  /**
   * @return string
   */
  public function getTimestamp()
  {
    return $this->timestamp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DataProfileFinding::class, 'Google_Service_DLP_GooglePrivacyDlpV2DataProfileFinding');
