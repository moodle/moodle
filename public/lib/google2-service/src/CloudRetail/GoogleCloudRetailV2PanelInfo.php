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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2PanelInfo extends \Google\Collection
{
  protected $collection_key = 'productDetails';
  /**
   * Optional. The attribution token of the panel.
   *
   * @var string
   */
  public $attributionToken;
  /**
   * Optional. The display name of the panel.
   *
   * @var string
   */
  public $displayName;
  /**
   * Required. The panel ID.
   *
   * @var string
   */
  public $panelId;
  /**
   * Optional. The ordered position of the panel, if shown to the user with
   * other panels. If set, then total_panels must also be set.
   *
   * @var int
   */
  public $panelPosition;
  protected $productDetailsType = GoogleCloudRetailV2ProductDetail::class;
  protected $productDetailsDataType = 'array';
  /**
   * Optional. The total number of panels, including this one, shown to the
   * user. Must be set if panel_position is set.
   *
   * @var int
   */
  public $totalPanels;

  /**
   * Optional. The attribution token of the panel.
   *
   * @param string $attributionToken
   */
  public function setAttributionToken($attributionToken)
  {
    $this->attributionToken = $attributionToken;
  }
  /**
   * @return string
   */
  public function getAttributionToken()
  {
    return $this->attributionToken;
  }
  /**
   * Optional. The display name of the panel.
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
   * Required. The panel ID.
   *
   * @param string $panelId
   */
  public function setPanelId($panelId)
  {
    $this->panelId = $panelId;
  }
  /**
   * @return string
   */
  public function getPanelId()
  {
    return $this->panelId;
  }
  /**
   * Optional. The ordered position of the panel, if shown to the user with
   * other panels. If set, then total_panels must also be set.
   *
   * @param int $panelPosition
   */
  public function setPanelPosition($panelPosition)
  {
    $this->panelPosition = $panelPosition;
  }
  /**
   * @return int
   */
  public function getPanelPosition()
  {
    return $this->panelPosition;
  }
  /**
   * Optional. The product details associated with the panel.
   *
   * @param GoogleCloudRetailV2ProductDetail[] $productDetails
   */
  public function setProductDetails($productDetails)
  {
    $this->productDetails = $productDetails;
  }
  /**
   * @return GoogleCloudRetailV2ProductDetail[]
   */
  public function getProductDetails()
  {
    return $this->productDetails;
  }
  /**
   * Optional. The total number of panels, including this one, shown to the
   * user. Must be set if panel_position is set.
   *
   * @param int $totalPanels
   */
  public function setTotalPanels($totalPanels)
  {
    $this->totalPanels = $totalPanels;
  }
  /**
   * @return int
   */
  public function getTotalPanels()
  {
    return $this->totalPanels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2PanelInfo::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2PanelInfo');
