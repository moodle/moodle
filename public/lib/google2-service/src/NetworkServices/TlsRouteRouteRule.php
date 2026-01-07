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

class TlsRouteRouteRule extends \Google\Collection
{
  protected $collection_key = 'matches';
  protected $actionType = TlsRouteRouteAction::class;
  protected $actionDataType = '';
  protected $matchesType = TlsRouteRouteMatch::class;
  protected $matchesDataType = 'array';

  /**
   * Required. The detailed rule defining how to route matched traffic.
   *
   * @param TlsRouteRouteAction $action
   */
  public function setAction(TlsRouteRouteAction $action)
  {
    $this->action = $action;
  }
  /**
   * @return TlsRouteRouteAction
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Required. RouteMatch defines the predicate used to match requests to a
   * given action. Multiple match types are "OR"ed for evaluation. Atleast one
   * RouteMatch must be supplied.
   *
   * @param TlsRouteRouteMatch[] $matches
   */
  public function setMatches($matches)
  {
    $this->matches = $matches;
  }
  /**
   * @return TlsRouteRouteMatch[]
   */
  public function getMatches()
  {
    return $this->matches;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TlsRouteRouteRule::class, 'Google_Service_NetworkServices_TlsRouteRouteRule');
