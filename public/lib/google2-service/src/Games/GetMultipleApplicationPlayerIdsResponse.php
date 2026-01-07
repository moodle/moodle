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

namespace Google\Service\Games;

class GetMultipleApplicationPlayerIdsResponse extends \Google\Collection
{
  protected $collection_key = 'playerIds';
  protected $playerIdsType = ApplicationPlayerId::class;
  protected $playerIdsDataType = 'array';

  /**
   * Output only. The requested applications along with the scoped ids for tha
   * player, if that player has an id for the application. If not, the
   * application is not included in the response.
   *
   * @param ApplicationPlayerId[] $playerIds
   */
  public function setPlayerIds($playerIds)
  {
    $this->playerIds = $playerIds;
  }
  /**
   * @return ApplicationPlayerId[]
   */
  public function getPlayerIds()
  {
    return $this->playerIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GetMultipleApplicationPlayerIdsResponse::class, 'Google_Service_Games_GetMultipleApplicationPlayerIdsResponse');
