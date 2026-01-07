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

class CreativeAssetMetadata extends \Google\Collection
{
  protected $collection_key = 'warnedValidationRules';
  protected $assetIdentifierType = CreativeAssetId::class;
  protected $assetIdentifierDataType = '';
  protected $clickTagsType = ClickTag::class;
  protected $clickTagsDataType = 'array';
  protected $counterCustomEventsType = CreativeCustomEvent::class;
  protected $counterCustomEventsDataType = 'array';
  /**
   * List of feature dependencies for the creative asset that are detected by
   * Campaign Manager. Feature dependencies are features that a browser must be
   * able to support in order to render your HTML5 creative correctly. This is a
   * read-only, auto-generated field.
   *
   * @var string[]
   */
  public $detectedFeatures;
  protected $exitCustomEventsType = CreativeCustomEvent::class;
  protected $exitCustomEventsDataType = 'array';
  /**
   * Numeric ID of the asset. This is a read-only, auto-generated field.
   *
   * @var string
   */
  public $id;
  protected $idDimensionValueType = DimensionValue::class;
  protected $idDimensionValueDataType = '';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#creativeAssetMetadata".
   *
   * @var string
   */
  public $kind;
  /**
   * True if the uploaded asset is a rich media asset. This is a read-only,
   * auto-generated field.
   *
   * @var bool
   */
  public $richMedia;
  protected $timerCustomEventsType = CreativeCustomEvent::class;
  protected $timerCustomEventsDataType = 'array';
  /**
   * Rules validated during code generation that generated a warning. This is a
   * read-only, auto-generated field. Possible values are: - "ADMOB_REFERENCED"
   * - "ASSET_FORMAT_UNSUPPORTED_DCM" - "ASSET_INVALID" - "CLICK_TAG_HARD_CODED"
   * - "CLICK_TAG_INVALID" - "CLICK_TAG_IN_GWD" - "CLICK_TAG_MISSING" -
   * "CLICK_TAG_MORE_THAN_ONE" - "CLICK_TAG_NON_TOP_LEVEL" -
   * "COMPONENT_UNSUPPORTED_DCM" - "ENABLER_UNSUPPORTED_METHOD_DCM" -
   * "EXTERNAL_FILE_REFERENCED" - "FILE_DETAIL_EMPTY" - "FILE_TYPE_INVALID" -
   * "GWD_PROPERTIES_INVALID" - "HTML5_FEATURE_UNSUPPORTED" -
   * "LINKED_FILE_NOT_FOUND" - "MAX_FLASH_VERSION_11" - "MRAID_REFERENCED" -
   * "NOT_SSL_COMPLIANT" - "ORPHANED_ASSET" - "PRIMARY_HTML_MISSING" -
   * "SVG_INVALID" - "ZIP_INVALID"
   *
   * @var string[]
   */
  public $warnedValidationRules;

  /**
   * ID of the creative asset. This is a required field.
   *
   * @param CreativeAssetId $assetIdentifier
   */
  public function setAssetIdentifier(CreativeAssetId $assetIdentifier)
  {
    $this->assetIdentifier = $assetIdentifier;
  }
  /**
   * @return CreativeAssetId
   */
  public function getAssetIdentifier()
  {
    return $this->assetIdentifier;
  }
  /**
   * List of detected click tags for assets. This is a read-only, auto-generated
   * field. This field is empty for a rich media asset.
   *
   * @param ClickTag[] $clickTags
   */
  public function setClickTags($clickTags)
  {
    $this->clickTags = $clickTags;
  }
  /**
   * @return ClickTag[]
   */
  public function getClickTags()
  {
    return $this->clickTags;
  }
  /**
   * List of counter events configured for the asset. This is a read-only, auto-
   * generated field and only applicable to a rich media asset.
   *
   * @param CreativeCustomEvent[] $counterCustomEvents
   */
  public function setCounterCustomEvents($counterCustomEvents)
  {
    $this->counterCustomEvents = $counterCustomEvents;
  }
  /**
   * @return CreativeCustomEvent[]
   */
  public function getCounterCustomEvents()
  {
    return $this->counterCustomEvents;
  }
  /**
   * List of feature dependencies for the creative asset that are detected by
   * Campaign Manager. Feature dependencies are features that a browser must be
   * able to support in order to render your HTML5 creative correctly. This is a
   * read-only, auto-generated field.
   *
   * @param string[] $detectedFeatures
   */
  public function setDetectedFeatures($detectedFeatures)
  {
    $this->detectedFeatures = $detectedFeatures;
  }
  /**
   * @return string[]
   */
  public function getDetectedFeatures()
  {
    return $this->detectedFeatures;
  }
  /**
   * List of exit events configured for the asset. This is a read-only, auto-
   * generated field and only applicable to a rich media asset.
   *
   * @param CreativeCustomEvent[] $exitCustomEvents
   */
  public function setExitCustomEvents($exitCustomEvents)
  {
    $this->exitCustomEvents = $exitCustomEvents;
  }
  /**
   * @return CreativeCustomEvent[]
   */
  public function getExitCustomEvents()
  {
    return $this->exitCustomEvents;
  }
  /**
   * Numeric ID of the asset. This is a read-only, auto-generated field.
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
   * Dimension value for the numeric ID of the asset. This is a read-only, auto-
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
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#creativeAssetMetadata".
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
   * True if the uploaded asset is a rich media asset. This is a read-only,
   * auto-generated field.
   *
   * @param bool $richMedia
   */
  public function setRichMedia($richMedia)
  {
    $this->richMedia = $richMedia;
  }
  /**
   * @return bool
   */
  public function getRichMedia()
  {
    return $this->richMedia;
  }
  /**
   * List of timer events configured for the asset. This is a read-only, auto-
   * generated field and only applicable to a rich media asset.
   *
   * @param CreativeCustomEvent[] $timerCustomEvents
   */
  public function setTimerCustomEvents($timerCustomEvents)
  {
    $this->timerCustomEvents = $timerCustomEvents;
  }
  /**
   * @return CreativeCustomEvent[]
   */
  public function getTimerCustomEvents()
  {
    return $this->timerCustomEvents;
  }
  /**
   * Rules validated during code generation that generated a warning. This is a
   * read-only, auto-generated field. Possible values are: - "ADMOB_REFERENCED"
   * - "ASSET_FORMAT_UNSUPPORTED_DCM" - "ASSET_INVALID" - "CLICK_TAG_HARD_CODED"
   * - "CLICK_TAG_INVALID" - "CLICK_TAG_IN_GWD" - "CLICK_TAG_MISSING" -
   * "CLICK_TAG_MORE_THAN_ONE" - "CLICK_TAG_NON_TOP_LEVEL" -
   * "COMPONENT_UNSUPPORTED_DCM" - "ENABLER_UNSUPPORTED_METHOD_DCM" -
   * "EXTERNAL_FILE_REFERENCED" - "FILE_DETAIL_EMPTY" - "FILE_TYPE_INVALID" -
   * "GWD_PROPERTIES_INVALID" - "HTML5_FEATURE_UNSUPPORTED" -
   * "LINKED_FILE_NOT_FOUND" - "MAX_FLASH_VERSION_11" - "MRAID_REFERENCED" -
   * "NOT_SSL_COMPLIANT" - "ORPHANED_ASSET" - "PRIMARY_HTML_MISSING" -
   * "SVG_INVALID" - "ZIP_INVALID"
   *
   * @param string[] $warnedValidationRules
   */
  public function setWarnedValidationRules($warnedValidationRules)
  {
    $this->warnedValidationRules = $warnedValidationRules;
  }
  /**
   * @return string[]
   */
  public function getWarnedValidationRules()
  {
    return $this->warnedValidationRules;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreativeAssetMetadata::class, 'Google_Service_Dfareporting_CreativeAssetMetadata');
