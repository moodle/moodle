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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ResourcesAssetGroupListingGroupFilter extends \Google\Model
{
  /**
   * Not specified.
   */
  public const TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Subdivision of products along some listing dimensions.
   */
  public const TYPE_SUBDIVISION = 'SUBDIVISION';
  /**
   * An included listing group filter leaf node.
   */
  public const TYPE_UNIT_INCLUDED = 'UNIT_INCLUDED';
  /**
   * An excluded listing group filter leaf node.
   */
  public const TYPE_UNIT_EXCLUDED = 'UNIT_EXCLUDED';
  /**
   * Not specified.
   */
  public const VERTICAL_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const VERTICAL_UNKNOWN = 'UNKNOWN';
  /**
   * Represents the shopping vertical. The vertical is allowed only in
   * Performance Max for Retail campaigns.
   */
  public const VERTICAL_SHOPPING = 'SHOPPING';
  /**
   * Immutable. The asset group which this asset group listing group filter is
   * part of.
   *
   * @var string
   */
  public $assetGroup;
  protected $caseValueType = GoogleAdsSearchads360V0ResourcesListingGroupFilterDimension::class;
  protected $caseValueDataType = '';
  /**
   * Output only. The ID of the ListingGroupFilter.
   *
   * @var string
   */
  public $id;
  /**
   * Immutable. Resource name of the parent listing group subdivision. Null for
   * the root listing group filter node.
   *
   * @var string
   */
  public $parentListingGroupFilter;
  protected $pathType = GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionPath::class;
  protected $pathDataType = '';
  /**
   * Immutable. The resource name of the asset group listing group filter. Asset
   * group listing group filter resource name have the form: `customers/{custome
   * r_id}/assetGroupListingGroupFilters/{asset_group_id}~{listing_group_filter_
   * id}`
   *
   * @var string
   */
  public $resourceName;
  /**
   * Immutable. Type of a listing group filter node.
   *
   * @var string
   */
  public $type;
  /**
   * Immutable. The vertical the current node tree represents. All nodes in the
   * same tree must belong to the same vertical.
   *
   * @var string
   */
  public $vertical;

  /**
   * Immutable. The asset group which this asset group listing group filter is
   * part of.
   *
   * @param string $assetGroup
   */
  public function setAssetGroup($assetGroup)
  {
    $this->assetGroup = $assetGroup;
  }
  /**
   * @return string
   */
  public function getAssetGroup()
  {
    return $this->assetGroup;
  }
  /**
   * Dimension value with which this listing group is refining its parent.
   * Undefined for the root group.
   *
   * @param GoogleAdsSearchads360V0ResourcesListingGroupFilterDimension $caseValue
   */
  public function setCaseValue(GoogleAdsSearchads360V0ResourcesListingGroupFilterDimension $caseValue)
  {
    $this->caseValue = $caseValue;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesListingGroupFilterDimension
   */
  public function getCaseValue()
  {
    return $this->caseValue;
  }
  /**
   * Output only. The ID of the ListingGroupFilter.
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
   * Immutable. Resource name of the parent listing group subdivision. Null for
   * the root listing group filter node.
   *
   * @param string $parentListingGroupFilter
   */
  public function setParentListingGroupFilter($parentListingGroupFilter)
  {
    $this->parentListingGroupFilter = $parentListingGroupFilter;
  }
  /**
   * @return string
   */
  public function getParentListingGroupFilter()
  {
    return $this->parentListingGroupFilter;
  }
  /**
   * Output only. The path of dimensions defining this listing group filter.
   *
   * @param GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionPath $path
   */
  public function setPath(GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionPath $path)
  {
    $this->path = $path;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionPath
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Immutable. The resource name of the asset group listing group filter. Asset
   * group listing group filter resource name have the form: `customers/{custome
   * r_id}/assetGroupListingGroupFilters/{asset_group_id}~{listing_group_filter_
   * id}`
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
   * Immutable. Type of a listing group filter node.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, SUBDIVISION, UNIT_INCLUDED,
   * UNIT_EXCLUDED
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Immutable. The vertical the current node tree represents. All nodes in the
   * same tree must belong to the same vertical.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, SHOPPING
   *
   * @param self::VERTICAL_* $vertical
   */
  public function setVertical($vertical)
  {
    $this->vertical = $vertical;
  }
  /**
   * @return self::VERTICAL_*
   */
  public function getVertical()
  {
    return $this->vertical;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesAssetGroupListingGroupFilter::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesAssetGroupListingGroupFilter');
