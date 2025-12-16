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

namespace Google\Service\CloudSupport;

class CaseClassification extends \Google\Model
{
  /**
   * A display name for the classification. The display name is not static and
   * can change. To uniquely and consistently identify classifications, use the
   * `CaseClassification.id` field.
   *
   * @var string
   */
  public $displayName;
  /**
   * The unique ID for a classification. Must be specified for case creation. To
   * retrieve valid classification IDs for case creation, use
   * `caseClassifications.search`. Classification IDs returned by
   * `caseClassifications.search` are guaranteed to be valid for at least 6
   * months. If a given classification is deactiveated, it will immediately stop
   * being returned. After 6 months, `case.create` requests using the
   * classification ID will fail.
   *
   * @var string
   */
  public $id;

  /**
   * A display name for the classification. The display name is not static and
   * can change. To uniquely and consistently identify classifications, use the
   * `CaseClassification.id` field.
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
   * The unique ID for a classification. Must be specified for case creation. To
   * retrieve valid classification IDs for case creation, use
   * `caseClassifications.search`. Classification IDs returned by
   * `caseClassifications.search` are guaranteed to be valid for at least 6
   * months. If a given classification is deactiveated, it will immediately stop
   * being returned. After 6 months, `case.create` requests using the
   * classification ID will fail.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CaseClassification::class, 'Google_Service_CloudSupport_CaseClassification');
