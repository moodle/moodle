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

class EnterpriseTopazSidekickPeopleAnswerDisambiguationInfoDisambiguationPerson extends \Google\Model
{
  protected $personType = EnterpriseTopazSidekickCommonPerson::class;
  protected $personDataType = '';
  /**
   * The query that can be used to produce an answer card with the same
   * attribute, but for this person.
   *
   * @var string
   */
  public $query;

  /**
   * The profile of this person.
   *
   * @param EnterpriseTopazSidekickCommonPerson $person
   */
  public function setPerson(EnterpriseTopazSidekickCommonPerson $person)
  {
    $this->person = $person;
  }
  /**
   * @return EnterpriseTopazSidekickCommonPerson
   */
  public function getPerson()
  {
    return $this->person;
  }
  /**
   * The query that can be used to produce an answer card with the same
   * attribute, but for this person.
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickPeopleAnswerDisambiguationInfoDisambiguationPerson::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickPeopleAnswerDisambiguationInfoDisambiguationPerson');
