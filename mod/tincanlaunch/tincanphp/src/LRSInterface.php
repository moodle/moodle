<?php
/*
    Copyright 2014 Rustici Software

    Licensed under the Apache License, Version 2.0 (the "License");
    you may not use this file except in compliance with the License.
    You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.
*/

namespace TinCan;

interface LRSInterface
{
    public function about();

    public function saveStatement($statement);
    public function saveStatements($statements);
    public function retrieveStatement($id);
    public function retrieveVoidedStatement($id);
    public function queryStatements($query);
    public function moreStatements($moreUrl);

    // TODO: should the document APIs be able to just provide a Document object?
    public function retrieveStateIds($activity, $agent);
    public function retrieveState($activity, $agent, $id);
    public function saveState($activity, $agent, $id, $content);
    public function deleteState($activity, $agent, $id);
    public function clearState($activity, $agent);

    public function retrieveActivityProfileIds($activity);
    public function retrieveActivityProfile($activity, $id);
    public function retrieveActivity($activityid);
    public function saveActivityProfile($activity, $id, $content);
    public function deleteActivityProfile($activity, $id);

    public function retrieveAgentProfileIds($agent);
    public function retrieveAgentProfile($agent, $id);
    public function retrievePerson($agent);
    public function saveAgentProfile($agent, $id, $content);
    public function deleteAgentProfile($agent, $id);
}
