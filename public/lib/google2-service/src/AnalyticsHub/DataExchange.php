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

namespace Google\Service\AnalyticsHub;

class DataExchange extends \Google\Model
{
  /**
   * Unspecified. Defaults to DISCOVERY_TYPE_PRIVATE.
   */
  public const DISCOVERY_TYPE_DISCOVERY_TYPE_UNSPECIFIED = 'DISCOVERY_TYPE_UNSPECIFIED';
  /**
   * The Data exchange/listing can be discovered in the 'Private' results list.
   */
  public const DISCOVERY_TYPE_DISCOVERY_TYPE_PRIVATE = 'DISCOVERY_TYPE_PRIVATE';
  /**
   * The Data exchange/listing can be discovered in the 'Public' results list.
   */
  public const DISCOVERY_TYPE_DISCOVERY_TYPE_PUBLIC = 'DISCOVERY_TYPE_PUBLIC';
  /**
   * Optional. Description of the data exchange. The description must not
   * contain Unicode non-characters as well as C0 and C1 control codes except
   * tabs (HT), new lines (LF), carriage returns (CR), and page breaks (FF).
   * Default value is an empty string. Max length: 2000 bytes.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Type of discovery on the discovery page for all the listings
   * under this exchange. Updating this field also updates (overwrites) the
   * discovery_type field for all the listings under this exchange.
   *
   * @var string
   */
  public $discoveryType;
  /**
   * Required. Human-readable display name of the data exchange. The display
   * name must contain only Unicode letters, numbers (0-9), underscores (_),
   * dashes (-), spaces ( ), ampersands (&) and must not start or end with
   * spaces. Default value is an empty string. Max length: 63 bytes.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. Documentation describing the data exchange.
   *
   * @var string
   */
  public $documentation;
  /**
   * Optional. Base64 encoded image representing the data exchange. Max Size:
   * 3.0MiB Expected image dimensions are 512x512 pixels, however the API only
   * performs validation on size of the encoded data. Note: For byte fields, the
   * content of the fields are base64-encoded (which increases the size of the
   * data by 33-36%) when using JSON on the wire.
   *
   * @var string
   */
  public $icon;
  /**
   * Output only. Number of listings contained in the data exchange.
   *
   * @var int
   */
  public $listingCount;
  /**
   * Optional. By default, false. If true, the DataExchange has an email sharing
   * mandate enabled.
   *
   * @var bool
   */
  public $logLinkedDatasetQueryUserEmail;
  /**
   * Output only. The resource name of the data exchange. e.g.
   * `projects/myproject/locations/us/dataExchanges/123`.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Email or URL of the primary point of contact of the data
   * exchange. Max Length: 1000 bytes.
   *
   * @var string
   */
  public $primaryContact;
  protected $sharingEnvironmentConfigType = SharingEnvironmentConfig::class;
  protected $sharingEnvironmentConfigDataType = '';

  /**
   * Optional. Description of the data exchange. The description must not
   * contain Unicode non-characters as well as C0 and C1 control codes except
   * tabs (HT), new lines (LF), carriage returns (CR), and page breaks (FF).
   * Default value is an empty string. Max length: 2000 bytes.
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
   * Optional. Type of discovery on the discovery page for all the listings
   * under this exchange. Updating this field also updates (overwrites) the
   * discovery_type field for all the listings under this exchange.
   *
   * Accepted values: DISCOVERY_TYPE_UNSPECIFIED, DISCOVERY_TYPE_PRIVATE,
   * DISCOVERY_TYPE_PUBLIC
   *
   * @param self::DISCOVERY_TYPE_* $discoveryType
   */
  public function setDiscoveryType($discoveryType)
  {
    $this->discoveryType = $discoveryType;
  }
  /**
   * @return self::DISCOVERY_TYPE_*
   */
  public function getDiscoveryType()
  {
    return $this->discoveryType;
  }
  /**
   * Required. Human-readable display name of the data exchange. The display
   * name must contain only Unicode letters, numbers (0-9), underscores (_),
   * dashes (-), spaces ( ), ampersands (&) and must not start or end with
   * spaces. Default value is an empty string. Max length: 63 bytes.
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
   * Optional. Documentation describing the data exchange.
   *
   * @param string $documentation
   */
  public function setDocumentation($documentation)
  {
    $this->documentation = $documentation;
  }
  /**
   * @return string
   */
  public function getDocumentation()
  {
    return $this->documentation;
  }
  /**
   * Optional. Base64 encoded image representing the data exchange. Max Size:
   * 3.0MiB Expected image dimensions are 512x512 pixels, however the API only
   * performs validation on size of the encoded data. Note: For byte fields, the
   * content of the fields are base64-encoded (which increases the size of the
   * data by 33-36%) when using JSON on the wire.
   *
   * @param string $icon
   */
  public function setIcon($icon)
  {
    $this->icon = $icon;
  }
  /**
   * @return string
   */
  public function getIcon()
  {
    return $this->icon;
  }
  /**
   * Output only. Number of listings contained in the data exchange.
   *
   * @param int $listingCount
   */
  public function setListingCount($listingCount)
  {
    $this->listingCount = $listingCount;
  }
  /**
   * @return int
   */
  public function getListingCount()
  {
    return $this->listingCount;
  }
  /**
   * Optional. By default, false. If true, the DataExchange has an email sharing
   * mandate enabled.
   *
   * @param bool $logLinkedDatasetQueryUserEmail
   */
  public function setLogLinkedDatasetQueryUserEmail($logLinkedDatasetQueryUserEmail)
  {
    $this->logLinkedDatasetQueryUserEmail = $logLinkedDatasetQueryUserEmail;
  }
  /**
   * @return bool
   */
  public function getLogLinkedDatasetQueryUserEmail()
  {
    return $this->logLinkedDatasetQueryUserEmail;
  }
  /**
   * Output only. The resource name of the data exchange. e.g.
   * `projects/myproject/locations/us/dataExchanges/123`.
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
   * Optional. Email or URL of the primary point of contact of the data
   * exchange. Max Length: 1000 bytes.
   *
   * @param string $primaryContact
   */
  public function setPrimaryContact($primaryContact)
  {
    $this->primaryContact = $primaryContact;
  }
  /**
   * @return string
   */
  public function getPrimaryContact()
  {
    return $this->primaryContact;
  }
  /**
   * Optional. Configurable data sharing environment option for a data exchange.
   *
   * @param SharingEnvironmentConfig $sharingEnvironmentConfig
   */
  public function setSharingEnvironmentConfig(SharingEnvironmentConfig $sharingEnvironmentConfig)
  {
    $this->sharingEnvironmentConfig = $sharingEnvironmentConfig;
  }
  /**
   * @return SharingEnvironmentConfig
   */
  public function getSharingEnvironmentConfig()
  {
    return $this->sharingEnvironmentConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataExchange::class, 'Google_Service_AnalyticsHub_DataExchange');
