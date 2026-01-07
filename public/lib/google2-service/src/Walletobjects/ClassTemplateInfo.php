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

namespace Google\Service\Walletobjects;

class ClassTemplateInfo extends \Google\Model
{
  protected $cardBarcodeSectionDetailsType = CardBarcodeSectionDetails::class;
  protected $cardBarcodeSectionDetailsDataType = '';
  protected $cardTemplateOverrideType = CardTemplateOverride::class;
  protected $cardTemplateOverrideDataType = '';
  protected $detailsTemplateOverrideType = DetailsTemplateOverride::class;
  protected $detailsTemplateOverrideDataType = '';
  protected $listTemplateOverrideType = ListTemplateOverride::class;
  protected $listTemplateOverrideDataType = '';

  /**
   * Specifies extra information to be displayed above and below the barcode.
   *
   * @param CardBarcodeSectionDetails $cardBarcodeSectionDetails
   */
  public function setCardBarcodeSectionDetails(CardBarcodeSectionDetails $cardBarcodeSectionDetails)
  {
    $this->cardBarcodeSectionDetails = $cardBarcodeSectionDetails;
  }
  /**
   * @return CardBarcodeSectionDetails
   */
  public function getCardBarcodeSectionDetails()
  {
    return $this->cardBarcodeSectionDetails;
  }
  /**
   * Override for the card view.
   *
   * @param CardTemplateOverride $cardTemplateOverride
   */
  public function setCardTemplateOverride(CardTemplateOverride $cardTemplateOverride)
  {
    $this->cardTemplateOverride = $cardTemplateOverride;
  }
  /**
   * @return CardTemplateOverride
   */
  public function getCardTemplateOverride()
  {
    return $this->cardTemplateOverride;
  }
  /**
   * Override for the details view (beneath the card view).
   *
   * @param DetailsTemplateOverride $detailsTemplateOverride
   */
  public function setDetailsTemplateOverride(DetailsTemplateOverride $detailsTemplateOverride)
  {
    $this->detailsTemplateOverride = $detailsTemplateOverride;
  }
  /**
   * @return DetailsTemplateOverride
   */
  public function getDetailsTemplateOverride()
  {
    return $this->detailsTemplateOverride;
  }
  /**
   * Override for the passes list view.
   *
   * @param ListTemplateOverride $listTemplateOverride
   */
  public function setListTemplateOverride(ListTemplateOverride $listTemplateOverride)
  {
    $this->listTemplateOverride = $listTemplateOverride;
  }
  /**
   * @return ListTemplateOverride
   */
  public function getListTemplateOverride()
  {
    return $this->listTemplateOverride;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClassTemplateInfo::class, 'Google_Service_Walletobjects_ClassTemplateInfo');
