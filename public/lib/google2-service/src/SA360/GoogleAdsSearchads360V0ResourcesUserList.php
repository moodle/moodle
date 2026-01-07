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

class GoogleAdsSearchads360V0ResourcesUserList extends \Google\Model
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
   * UserList represented as a collection of conversion types.
   */
  public const TYPE_REMARKETING = 'REMARKETING';
  /**
   * UserList represented as a combination of other user lists/interests.
   */
  public const TYPE_LOGICAL = 'LOGICAL';
  /**
   * UserList created in the Google Ad Manager platform.
   */
  public const TYPE_EXTERNAL_REMARKETING = 'EXTERNAL_REMARKETING';
  /**
   * UserList associated with a rule.
   */
  public const TYPE_RULE_BASED = 'RULE_BASED';
  /**
   * UserList with users similar to users of another UserList.
   */
  public const TYPE_SIMILAR = 'SIMILAR';
  /**
   * UserList of first-party CRM data provided by advertiser in the form of
   * emails or other formats.
   */
  public const TYPE_CRM_BASED = 'CRM_BASED';
  /**
   * Output only. Id of the user list.
   *
   * @var string
   */
  public $id;
  /**
   * Name of this user list. Depending on its access_reason, the user list name
   * may not be unique (for example, if access_reason=SHARED)
   *
   * @var string
   */
  public $name;
  /**
   * Immutable. The resource name of the user list. User list resource names
   * have the form: `customers/{customer_id}/userLists/{user_list_id}`
   *
   * @var string
   */
  public $resourceName;
  /**
   * Output only. Type of this list. This field is read-only.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. Id of the user list.
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
   * Name of this user list. Depending on its access_reason, the user list name
   * may not be unique (for example, if access_reason=SHARED)
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
   * Immutable. The resource name of the user list. User list resource names
   * have the form: `customers/{customer_id}/userLists/{user_list_id}`
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
   * Output only. Type of this list. This field is read-only.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, REMARKETING, LOGICAL,
   * EXTERNAL_REMARKETING, RULE_BASED, SIMILAR, CRM_BASED
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesUserList::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesUserList');
