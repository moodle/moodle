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

namespace Google\Service\ShoppingContent;

class AccountAutomaticImprovements extends \Google\Model
{
  protected $imageImprovementsType = AccountImageImprovements::class;
  protected $imageImprovementsDataType = '';
  protected $itemUpdatesType = AccountItemUpdates::class;
  protected $itemUpdatesDataType = '';
  protected $shippingImprovementsType = AccountShippingImprovements::class;
  protected $shippingImprovementsDataType = '';

  /**
   * This improvement will attempt to automatically correct submitted images if
   * they don't meet the [image
   * requirements](https://support.google.com/merchants/answer/6324350), for
   * example, removing overlays. If successful, the image will be replaced and
   * approved. This improvement is only applied to images of disapproved offers.
   * For more information see: [Automatic image
   * improvements](https://support.google.com/merchants/answer/9242973) This
   * field is only updated (cleared) if provided.
   *
   * @param AccountImageImprovements $imageImprovements
   */
  public function setImageImprovements(AccountImageImprovements $imageImprovements)
  {
    $this->imageImprovements = $imageImprovements;
  }
  /**
   * @return AccountImageImprovements
   */
  public function getImageImprovements()
  {
    return $this->imageImprovements;
  }
  /**
   * Turning on [item
   * updates](https://support.google.com/merchants/answer/3246284) allows Google
   * to automatically update items for you. When item updates are on, Google
   * uses the structured data markup on the website and advanced data extractors
   * to update the price and availability of the items. When the item updates
   * are off, items with mismatched data aren't shown. This field is only
   * updated (cleared) if provided.
   *
   * @param AccountItemUpdates $itemUpdates
   */
  public function setItemUpdates(AccountItemUpdates $itemUpdates)
  {
    $this->itemUpdates = $itemUpdates;
  }
  /**
   * @return AccountItemUpdates
   */
  public function getItemUpdates()
  {
    return $this->itemUpdates;
  }
  /**
   * Not available for MCAs
   * [accounts](https://support.google.com/merchants/answer/188487). By turning
   * on [automatic shipping
   * improvements](https://support.google.com/merchants/answer/10027038), you
   * are allowing Google to improve the accuracy of your delivery times shown to
   * shoppers using Google. More accurate delivery times, especially when
   * faster, typically lead to better conversion rates. Google will improve your
   * estimated delivery times based on various factors: - Delivery address of an
   * order - Current handling time and shipping time settings - Estimated
   * weekdays or business days - Parcel tracking data This field is only updated
   * (cleared) if provided.
   *
   * @param AccountShippingImprovements $shippingImprovements
   */
  public function setShippingImprovements(AccountShippingImprovements $shippingImprovements)
  {
    $this->shippingImprovements = $shippingImprovements;
  }
  /**
   * @return AccountShippingImprovements
   */
  public function getShippingImprovements()
  {
    return $this->shippingImprovements;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountAutomaticImprovements::class, 'Google_Service_ShoppingContent_AccountAutomaticImprovements');
