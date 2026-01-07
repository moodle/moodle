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

namespace Google\Service\DisplayVideo;

class EditCustomerMatchMembersRequest extends \Google\Model
{
  protected $addedContactInfoListType = ContactInfoList::class;
  protected $addedContactInfoListDataType = '';
  protected $addedMobileDeviceIdListType = MobileDeviceIdList::class;
  protected $addedMobileDeviceIdListDataType = '';
  /**
   * Required. The ID of the owner advertiser of the updated Customer Match
   * FirstAndThirdPartyAudience.
   *
   * @var string
   */
  public $advertiserId;
  protected $removedContactInfoListType = ContactInfoList::class;
  protected $removedContactInfoListDataType = '';
  protected $removedMobileDeviceIdListType = MobileDeviceIdList::class;
  protected $removedMobileDeviceIdListDataType = '';

  /**
   * Input only. A list of contact information to define the members to be
   * added.
   *
   * @param ContactInfoList $addedContactInfoList
   */
  public function setAddedContactInfoList(ContactInfoList $addedContactInfoList)
  {
    $this->addedContactInfoList = $addedContactInfoList;
  }
  /**
   * @return ContactInfoList
   */
  public function getAddedContactInfoList()
  {
    return $this->addedContactInfoList;
  }
  /**
   * Input only. A list of mobile device IDs to define the members to be added.
   *
   * @param MobileDeviceIdList $addedMobileDeviceIdList
   */
  public function setAddedMobileDeviceIdList(MobileDeviceIdList $addedMobileDeviceIdList)
  {
    $this->addedMobileDeviceIdList = $addedMobileDeviceIdList;
  }
  /**
   * @return MobileDeviceIdList
   */
  public function getAddedMobileDeviceIdList()
  {
    return $this->addedMobileDeviceIdList;
  }
  /**
   * Required. The ID of the owner advertiser of the updated Customer Match
   * FirstAndThirdPartyAudience.
   *
   * @param string $advertiserId
   */
  public function setAdvertiserId($advertiserId)
  {
    $this->advertiserId = $advertiserId;
  }
  /**
   * @return string
   */
  public function getAdvertiserId()
  {
    return $this->advertiserId;
  }
  /**
   * Input only. A list of contact information to define the members to be
   * removed.
   *
   * @param ContactInfoList $removedContactInfoList
   */
  public function setRemovedContactInfoList(ContactInfoList $removedContactInfoList)
  {
    $this->removedContactInfoList = $removedContactInfoList;
  }
  /**
   * @return ContactInfoList
   */
  public function getRemovedContactInfoList()
  {
    return $this->removedContactInfoList;
  }
  /**
   * Input only. A list of mobile device IDs to define the members to be
   * removed.
   *
   * @param MobileDeviceIdList $removedMobileDeviceIdList
   */
  public function setRemovedMobileDeviceIdList(MobileDeviceIdList $removedMobileDeviceIdList)
  {
    $this->removedMobileDeviceIdList = $removedMobileDeviceIdList;
  }
  /**
   * @return MobileDeviceIdList
   */
  public function getRemovedMobileDeviceIdList()
  {
    return $this->removedMobileDeviceIdList;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EditCustomerMatchMembersRequest::class, 'Google_Service_DisplayVideo_EditCustomerMatchMembersRequest');
