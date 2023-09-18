<?php

namespace IMSGlobal\LTI\ToolProvider\Service;

use IMSGlobal\LTI\ToolProvider;

/**
 * Class to implement the Membership service
 *
 * @author  Stephen P Vickers <svickers@imsglobal.org>
 * @copyright  IMS Global Learning Consortium Inc
 * @date  2016
 * @version 3.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
#[\AllowDynamicProperties]
class Membership extends Service
{

/**
 * The object to which the settings apply (ResourceLink, Context or ToolConsumer).
 *
 * @var object  $source
 */
    private $source;

/**
 * Class constructor.
 *
 * @param object       $source     The object to which the memberships apply (ResourceLink or Context)
 * @param string       $endpoint   Service endpoint
 */
    public function __construct($source, $endpoint)
    {

        $consumer = $source->getConsumer();
        parent::__construct($consumer, $endpoint, 'application/vnd.ims.lis.v2.membershipcontainer+json');
        $this->source = $source;

    }

/**
 * Get the memberships.
 *
 * @param string    $role   Role for which memberships are to be requested (optional, default is all roles)
 * @param int       $limit  Limit on the number of memberships to be returned (optional, default is all)
 *
 * @return mixed The array of User objects if successful, otherwise false
 */
    public function get($role = null, $limit = 0) {

        $isLink = is_a($this->source, 'IMSGlobal\LTI\ToolProvider\ResourceLink');
        $parameters = array();
        if (!empty($role)) {
            $parameters['role'] = $role;
        }
        if ($limit > 0) {
            $parameters['limit'] = strval($limit);
        }
        if ($isLink) {
            $parameters['rlid'] = $this->source->getId();
        }
        $http = $this->send('GET', $parameters);
        if (!$http->ok) {
            $users = false;
        } else {
            $users = array();
            if ($isLink) {
                $oldUsers = $this->source->getUserResultSourcedIDs(true, ToolProvider\ToolProvider::ID_SCOPE_RESOURCE);
            }
            foreach ($http->responseJson->pageOf->membershipSubject->membership as $membership) {
                $member = $membership->member;
                if ($isLink) {
                    $user = ToolProvider\User::fromResourceLink($this->source, $member->userId);
                } else {
                    $user = new ToolProvider\User();
                    $user->ltiUserId = $member->userId;
                }

// Set the user name
                $firstname = (isset($member->givenName)) ? $member->givenName : '';
                $lastname = (isset($member->familyName)) ? $member->familyName : '';
                $fullname = (isset($member->name)) ? $member->name : '';
                $user->setNames($firstname, $lastname, $fullname);

// Set the user email
                $email = (isset($member->email)) ? $member->email : '';
                $user->setEmail($email, $this->source->getConsumer()->defaultEmail);

// Set the user roles
                if (isset($membership->role)) {
                    $user->roles = ToolProvider\ToolProvider::parseRoles($membership->role);
                }

// If a result sourcedid is provided save the user
                if ($isLink) {
                    if (isset($member->message)) {
                        foreach ($member->message as $message) {
                            if (isset($message->message_type) && ($message->message_type === 'basic-lti-launch-request')) {
                                if (isset($message->lis_result_sourcedid)) {
                                    $user->ltiResultSourcedId = $message->lis_result_sourcedid;
                                    $user->save();
                                }
                                break;                                
                            }
                        }
                    }
                }
                $users[] = $user;

// Remove old user (if it exists)
                if ($isLink) {
                    unset($oldUsers[$user->getId(ToolProvider\ToolProvider::ID_SCOPE_RESOURCE)]);
                }
            }

// Delete any old users which were not in the latest list from the tool consumer
            if ($isLink) {
                foreach ($oldUsers as $id => $user) {
                    $user->delete();
                }
            }
        }

        return $users;

    }

}
