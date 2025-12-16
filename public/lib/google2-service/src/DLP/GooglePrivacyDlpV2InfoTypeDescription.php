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

class GooglePrivacyDlpV2InfoTypeDescription extends \Google\Collection
{
  protected $collection_key = 'versions';
  protected $categoriesType = GooglePrivacyDlpV2InfoTypeCategory::class;
  protected $categoriesDataType = 'array';
  /**
   * Description of the infotype. Translated when language is provided in the
   * request.
   *
   * @var string
   */
  public $description;
  /**
   * Human readable form of the infoType name.
   *
   * @var string
   */
  public $displayName;
  /**
   * A sample that is a true positive for this infoType.
   *
   * @var string
   */
  public $example;
  protected $locationSupportType = GooglePrivacyDlpV2LocationSupport::class;
  protected $locationSupportDataType = '';
  /**
   * Internal name of the infoType.
   *
   * @var string
   */
  public $name;
  protected $sensitivityScoreType = GooglePrivacyDlpV2SensitivityScore::class;
  protected $sensitivityScoreDataType = '';
  /**
   * If this field is set, this infoType is a general infoType and these
   * specific infoTypes are contained within it. General infoTypes are infoTypes
   * that encompass multiple specific infoTypes. For example, the
   * "GEOGRAPHIC_DATA" general infoType would have set for this field
   * "LOCATION", "LOCATION_COORDINATES", and "STREET_ADDRESS".
   *
   * @var string[]
   */
  public $specificInfoTypes;
  /**
   * Which parts of the API supports this InfoType.
   *
   * @var string[]
   */
  public $supportedBy;
  protected $versionsType = GooglePrivacyDlpV2VersionDescription::class;
  protected $versionsDataType = 'array';

  /**
   * The category of the infoType.
   *
   * @param GooglePrivacyDlpV2InfoTypeCategory[] $categories
   */
  public function setCategories($categories)
  {
    $this->categories = $categories;
  }
  /**
   * @return GooglePrivacyDlpV2InfoTypeCategory[]
   */
  public function getCategories()
  {
    return $this->categories;
  }
  /**
   * Description of the infotype. Translated when language is provided in the
   * request.
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
   * Human readable form of the infoType name.
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
   * A sample that is a true positive for this infoType.
   *
   * @param string $example
   */
  public function setExample($example)
  {
    $this->example = $example;
  }
  /**
   * @return string
   */
  public function getExample()
  {
    return $this->example;
  }
  /**
   * Locations at which this feature can be used. May change over time.
   *
   * @param GooglePrivacyDlpV2LocationSupport $locationSupport
   */
  public function setLocationSupport(GooglePrivacyDlpV2LocationSupport $locationSupport)
  {
    $this->locationSupport = $locationSupport;
  }
  /**
   * @return GooglePrivacyDlpV2LocationSupport
   */
  public function getLocationSupport()
  {
    return $this->locationSupport;
  }
  /**
   * Internal name of the infoType.
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
   * The default sensitivity of the infoType.
   *
   * @param GooglePrivacyDlpV2SensitivityScore $sensitivityScore
   */
  public function setSensitivityScore(GooglePrivacyDlpV2SensitivityScore $sensitivityScore)
  {
    $this->sensitivityScore = $sensitivityScore;
  }
  /**
   * @return GooglePrivacyDlpV2SensitivityScore
   */
  public function getSensitivityScore()
  {
    return $this->sensitivityScore;
  }
  /**
   * If this field is set, this infoType is a general infoType and these
   * specific infoTypes are contained within it. General infoTypes are infoTypes
   * that encompass multiple specific infoTypes. For example, the
   * "GEOGRAPHIC_DATA" general infoType would have set for this field
   * "LOCATION", "LOCATION_COORDINATES", and "STREET_ADDRESS".
   *
   * @param string[] $specificInfoTypes
   */
  public function setSpecificInfoTypes($specificInfoTypes)
  {
    $this->specificInfoTypes = $specificInfoTypes;
  }
  /**
   * @return string[]
   */
  public function getSpecificInfoTypes()
  {
    return $this->specificInfoTypes;
  }
  /**
   * Which parts of the API supports this InfoType.
   *
   * @param string[] $supportedBy
   */
  public function setSupportedBy($supportedBy)
  {
    $this->supportedBy = $supportedBy;
  }
  /**
   * @return string[]
   */
  public function getSupportedBy()
  {
    return $this->supportedBy;
  }
  /**
   * A list of available versions for the infotype.
   *
   * @param GooglePrivacyDlpV2VersionDescription[] $versions
   */
  public function setVersions($versions)
  {
    $this->versions = $versions;
  }
  /**
   * @return GooglePrivacyDlpV2VersionDescription[]
   */
  public function getVersions()
  {
    return $this->versions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2InfoTypeDescription::class, 'Google_Service_DLP_GooglePrivacyDlpV2InfoTypeDescription');
