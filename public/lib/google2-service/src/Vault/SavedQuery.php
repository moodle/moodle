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

class SavedQuery extends \Google\Model
{
  /**
   * Output only. The server-generated timestamp when the saved query was
   * created.
   *
   * @var string
   */
  public $createTime;
  /**
   * The name of the saved query.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The matter ID of the matter the saved query is saved in. The
   * server does not use this field during create and always uses matter ID in
   * the URL.
   *
   * @var string
   */
  public $matterId;
  protected $queryType = Query::class;
  protected $queryDataType = '';
  /**
   * A unique identifier for the saved query.
   *
   * @var string
   */
  public $savedQueryId;

  /**
   * Output only. The server-generated timestamp when the saved query was
   * created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The name of the saved query.
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
   * Output only. The matter ID of the matter the saved query is saved in. The
   * server does not use this field during create and always uses matter ID in
   * the URL.
   *
   * @param string $matterId
   */
  public function setMatterId($matterId)
  {
    $this->matterId = $matterId;
  }
  /**
   * @return string
   */
  public function getMatterId()
  {
    return $this->matterId;
  }
  /**
   * The search parameters of the saved query.
   *
   * @param Query $query
   */
  public function setQuery(Query $query)
  {
    $this->query = $query;
  }
  /**
   * @return Query
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * A unique identifier for the saved query.
   *
   * @param string $savedQueryId
   */
  public function setSavedQueryId($savedQueryId)
  {
    $this->savedQueryId = $savedQueryId;
  }
  /**
   * @return string
   */
  public function getSavedQueryId()
  {
    return $this->savedQueryId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SavedQuery::class, 'Google_Service_Vault_SavedQuery');
