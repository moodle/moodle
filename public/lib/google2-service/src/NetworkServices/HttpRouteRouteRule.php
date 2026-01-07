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

namespace Google\Service\NetworkServices;

class HttpRouteRouteRule extends \Google\Collection
{
  protected $collection_key = 'matches';
  protected $actionType = HttpRouteRouteAction::class;
  protected $actionDataType = '';
  protected $matchesType = HttpRouteRouteMatch::class;
  protected $matchesDataType = 'array';

  /**
   * The detailed rule defining how to route matched traffic.
   *
   * @param HttpRouteRouteAction $action
   */
  public function setAction(HttpRouteRouteAction $action)
  {
    $this->action = $action;
  }
  /**
   * @return HttpRouteRouteAction
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * A list of matches define conditions used for matching the rule against
   * incoming HTTP requests. Each match is independent, i.e. this rule will be
   * matched if ANY one of the matches is satisfied. If no matches field is
   * specified, this rule will unconditionally match traffic. If a default rule
   * is desired to be configured, add a rule with no matches specified to the
   * end of the rules list.
   *
   * @param HttpRouteRouteMatch[] $matches
   */
  public function setMatches($matches)
  {
    $this->matches = $matches;
  }
  /**
   * @return HttpRouteRouteMatch[]
   */
  public function getMatches()
  {
    return $this->matches;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpRouteRouteRule::class, 'Google_Service_NetworkServices_HttpRouteRouteRule');
