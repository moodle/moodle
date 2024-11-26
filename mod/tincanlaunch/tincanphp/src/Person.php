<?php
/*
    Copyright 2015 Rustici Software

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

class Person implements VersionableInterface
{
    use ArraySetterTrait, FromJSONTrait;
    protected $objectType = 'Person';

    protected $name;
    protected $mbox;
    protected $mbox_sha1sum;
    protected $openid;
    protected $account;

    public function __construct() {
        if (func_num_args() == 1) {
            $arg = func_get_arg(0);

            $this->_fromArray($arg);
        }
    }

    public function asVersion($version) {
        $result = array(
            'objectType' => $this->objectType
        );
        if (isset($this->name)) {
            $result['name'] = $this->name;
        }
        if (isset($this->account)) {
            $result['account'] = array();
            foreach ($this->account as $account) {
                if (! $account instanceof AgentAccount && is_array($account)) {
                    $account = new AgentAccount($account);
                }
                array_push($result['account'], $account->asVersion($version));
            }
        }
        if (isset($this->mbox_sha1sum)) {
            $result['mbox_sha1sum'] = $this->mbox_sha1sum;
        }
        if (isset($this->mbox)) {
            $result['mbox'] = $this->mbox;
        }
        if (isset($this->openid)) {
            $result['openid'] = $this->openid;
        }

        return $result;
    }

    public function getObjectType() { return $this->objectType; }

    public function setName($value) { $this->name = $value; return $this; }
    public function getName() { return $this->name; }

    public function setMbox($value) { $this->mbox = $value; return $this; }
    public function getMbox() { return $this->mbox; }

    public function setMbox_sha1sum($value) { $this->mbox_sha1sum = $value; return $this; }
    public function getMbox_sha1sum() {return $this->mbox_sha1sum;}

    public function setOpenid($value) { $this->openid = $value; return $this; }
    public function getOpenid() { return $this->openid; }

    public function setAccount($value) { $this->account = $value; return $this; }
    public function getAccount() { return $this->account; }
}
