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

namespace Google\Service\CloudSearch;

class EnterpriseTopazSidekickDocumentGroup extends \Google\Collection
{
  /**
   * Unknown type.
   */
  public const GROUP_TYPE_UNKNOWN_TYPE = 'UNKNOWN_TYPE';
  /**
   * A mix of all the document types.
   */
  public const GROUP_TYPE_ALL = 'ALL';
  protected $collection_key = 'personalizedDocument';
  /**
   * Document group type
   *
   * @deprecated
   * @var string
   */
  public $groupType;
  protected $personalizedDocumentType = EnterpriseTopazSidekickCommonDocument::class;
  protected $personalizedDocumentDataType = 'array';

  /**
   * Document group type
   *
   * Accepted values: UNKNOWN_TYPE, ALL
   *
   * @deprecated
   * @param self::GROUP_TYPE_* $groupType
   */
  public function setGroupType($groupType)
  {
    $this->groupType = $groupType;
  }
  /**
   * @deprecated
   * @return self::GROUP_TYPE_*
   */
  public function getGroupType()
  {
    return $this->groupType;
  }
  /**
   * The list of corresponding documents.
   *
   * @param EnterpriseTopazSidekickCommonDocument[] $personalizedDocument
   */
  public function setPersonalizedDocument($personalizedDocument)
  {
    $this->personalizedDocument = $personalizedDocument;
  }
  /**
   * @return EnterpriseTopazSidekickCommonDocument[]
   */
  public function getPersonalizedDocument()
  {
    return $this->personalizedDocument;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickDocumentGroup::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickDocumentGroup');
