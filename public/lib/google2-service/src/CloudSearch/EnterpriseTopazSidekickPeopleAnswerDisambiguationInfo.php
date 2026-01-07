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

class EnterpriseTopazSidekickPeopleAnswerDisambiguationInfo extends \Google\Collection
{
  protected $collection_key = 'disambiguation';
  protected $disambiguationType = EnterpriseTopazSidekickPeopleAnswerDisambiguationInfoDisambiguationPerson::class;
  protected $disambiguationDataType = 'array';
  /**
   * The name that was extracted from the query. This may be in the form of the
   * given name, last name, full name, LDAP, or email address. This name can be
   * considered suitable for displaying to the user and can largely be
   * considered to be normalized (e.g. "Bob's" -> "Bob").
   *
   * @var string
   */
  public $name;

  /**
   * A list of people that also matched the query. This list is not complete.
   *
   * @param EnterpriseTopazSidekickPeopleAnswerDisambiguationInfoDisambiguationPerson[] $disambiguation
   */
  public function setDisambiguation($disambiguation)
  {
    $this->disambiguation = $disambiguation;
  }
  /**
   * @return EnterpriseTopazSidekickPeopleAnswerDisambiguationInfoDisambiguationPerson[]
   */
  public function getDisambiguation()
  {
    return $this->disambiguation;
  }
  /**
   * The name that was extracted from the query. This may be in the form of the
   * given name, last name, full name, LDAP, or email address. This name can be
   * considered suitable for displaying to the user and can largely be
   * considered to be normalized (e.g. "Bob's" -> "Bob").
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickPeopleAnswerDisambiguationInfo::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickPeopleAnswerDisambiguationInfo');
