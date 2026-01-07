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

namespace Google\Service\Vault;

class Hold extends \Google\Collection
{
  /**
   * No service specified.
   */
  public const CORPUS_CORPUS_TYPE_UNSPECIFIED = 'CORPUS_TYPE_UNSPECIFIED';
  /**
   * Drive, including Meet and Sites.
   */
  public const CORPUS_DRIVE = 'DRIVE';
  /**
   * For search, Gmail and classic Hangouts. For holds, Gmail only.
   */
  public const CORPUS_MAIL = 'MAIL';
  /**
   * Groups.
   */
  public const CORPUS_GROUPS = 'GROUPS';
  /**
   * For export, Google Chat only. For holds, Google Chat and classic Hangouts.
   */
  public const CORPUS_HANGOUTS_CHAT = 'HANGOUTS_CHAT';
  /**
   * Google Voice.
   */
  public const CORPUS_VOICE = 'VOICE';
  /**
   * Calendar.
   */
  public const CORPUS_CALENDAR = 'CALENDAR';
  /**
   * Gemini.
   */
  public const CORPUS_GEMINI = 'GEMINI';
  protected $collection_key = 'accounts';
  protected $accountsType = HeldAccount::class;
  protected $accountsDataType = 'array';
  /**
   * The service to be searched.
   *
   * @var string
   */
  public $corpus;
  /**
   * The unique immutable ID of the hold. Assigned during creation.
   *
   * @var string
   */
  public $holdId;
  /**
   * The name of the hold.
   *
   * @var string
   */
  public $name;
  protected $orgUnitType = HeldOrgUnit::class;
  protected $orgUnitDataType = '';
  protected $queryType = CorpusQuery::class;
  protected $queryDataType = '';
  /**
   * The last time this hold was modified.
   *
   * @var string
   */
  public $updateTime;

  /**
   * If set, the hold applies to the specified accounts and **orgUnit** must be
   * empty.
   *
   * @param HeldAccount[] $accounts
   */
  public function setAccounts($accounts)
  {
    $this->accounts = $accounts;
  }
  /**
   * @return HeldAccount[]
   */
  public function getAccounts()
  {
    return $this->accounts;
  }
  /**
   * The service to be searched.
   *
   * Accepted values: CORPUS_TYPE_UNSPECIFIED, DRIVE, MAIL, GROUPS,
   * HANGOUTS_CHAT, VOICE, CALENDAR, GEMINI
   *
   * @param self::CORPUS_* $corpus
   */
  public function setCorpus($corpus)
  {
    $this->corpus = $corpus;
  }
  /**
   * @return self::CORPUS_*
   */
  public function getCorpus()
  {
    return $this->corpus;
  }
  /**
   * The unique immutable ID of the hold. Assigned during creation.
   *
   * @param string $holdId
   */
  public function setHoldId($holdId)
  {
    $this->holdId = $holdId;
  }
  /**
   * @return string
   */
  public function getHoldId()
  {
    return $this->holdId;
  }
  /**
   * The name of the hold.
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
   * If set, the hold applies to all members of the organizational unit and
   * **accounts** must be empty. This property is mutable. For Groups holds, set
   * **accounts**.
   *
   * @param HeldOrgUnit $orgUnit
   */
  public function setOrgUnit(HeldOrgUnit $orgUnit)
  {
    $this->orgUnit = $orgUnit;
  }
  /**
   * @return HeldOrgUnit
   */
  public function getOrgUnit()
  {
    return $this->orgUnit;
  }
  /**
   * Service-specific options. If set, **CorpusQuery** must match
   * **CorpusType**.
   *
   * @param CorpusQuery $query
   */
  public function setQuery(CorpusQuery $query)
  {
    $this->query = $query;
  }
  /**
   * @return CorpusQuery
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * The last time this hold was modified.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Hold::class, 'Google_Service_Vault_Hold');
