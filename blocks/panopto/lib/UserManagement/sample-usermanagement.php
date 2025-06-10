<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Test with UserManagement for 'https://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/UserManagement.svc?singlewsdl'
 * @package UserManagement
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
ini_set('memory_limit','512M');
ini_set('display_errors',true);
error_reporting(-1);
/**
 * Load autoload
 */
require_once dirname(__FILE__) . '/UserManagementAutoload.php';
/**
 * Wsdl instanciation infos. By default, nothing has to be set.
 * If you wish to override the SoapClient's options, please refer to the sample below.
 *
 * This is an associative array as:
 * - the key must be a UserManagementWsdlClass constant beginning with WSDL_
 * - the value must be the corresponding key value
 * Each option matches the {@link http://www.php.net/manual/en/soapclient.soapclient.php} options
 *
 * Here is below an example of how you can set the array:
 * $wsdl = array();
 * $wsdl[UserManagementWsdlClass::WSDL_URL] = 'https://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/UserManagement.svc?singlewsdl';
 * $wsdl[UserManagementWsdlClass::WSDL_CACHE_WSDL] = WSDL_CACHE_NONE;
 * $wsdl[UserManagementWsdlClass::WSDL_TRACE] = true;
 * $wsdl[UserManagementWsdlClass::WSDL_LOGIN] = 'myLogin';
 * $wsdl[UserManagementWsdlClass::WSDL_PASSWD] = '**********';
 * etc....
 * Then instantiate the Service class as:
 * - $wsdlObject = new UserManagementWsdlClass($wsdl);
 */
/**
 * Examples
 */


/*****************************************
 * Example for UserManagementServiceCreate
 */
$userManagementServiceCreate = new UserManagementServiceCreate();
// sample call for UserManagementServiceCreate::CreateUser()
if($userManagementServiceCreate->CreateUser(new UserManagementStructCreateUser(/*** update parameters list ***/)))
    print_r($userManagementServiceCreate->getResult());
else
    print_r($userManagementServiceCreate->getLastError());
// sample call for UserManagementServiceCreate::CreateUsers()
if($userManagementServiceCreate->CreateUsers(new UserManagementStructCreateUsers(/*** update parameters list ***/)))
    print_r($userManagementServiceCreate->getResult());
else
    print_r($userManagementServiceCreate->getLastError());
// sample call for UserManagementServiceCreate::CreateInternalGroup()
if($userManagementServiceCreate->CreateInternalGroup(new UserManagementStructCreateInternalGroup(/*** update parameters list ***/)))
    print_r($userManagementServiceCreate->getResult());
else
    print_r($userManagementServiceCreate->getLastError());
// sample call for UserManagementServiceCreate::CreateExternalGroup()
if($userManagementServiceCreate->CreateExternalGroup(new UserManagementStructCreateExternalGroup(/*** update parameters list ***/)))
    print_r($userManagementServiceCreate->getResult());
else
    print_r($userManagementServiceCreate->getLastError());

/**************************************
 * Example for UserManagementServiceGet
 */
$userManagementServiceGet = new UserManagementServiceGet();
// sample call for UserManagementServiceGet::GetUserByKey()
if($userManagementServiceGet->GetUserByKey(new UserManagementStructGetUserByKey(/*** update parameters list ***/)))
    print_r($userManagementServiceGet->getResult());
else
    print_r($userManagementServiceGet->getLastError());
// sample call for UserManagementServiceGet::GetUsers()
if($userManagementServiceGet->GetUsers(new UserManagementStructGetUsers(/*** update parameters list ***/)))
    print_r($userManagementServiceGet->getResult());
else
    print_r($userManagementServiceGet->getLastError());
// sample call for UserManagementServiceGet::GetGroupIsPublic()
if($userManagementServiceGet->GetGroupIsPublic(new UserManagementStructGetGroupIsPublic(/*** update parameters list ***/)))
    print_r($userManagementServiceGet->getResult());
else
    print_r($userManagementServiceGet->getLastError());
// sample call for UserManagementServiceGet::GetGroup()
if($userManagementServiceGet->GetGroup(new UserManagementStructGetGroup(/*** update parameters list ***/)))
    print_r($userManagementServiceGet->getResult());
else
    print_r($userManagementServiceGet->getLastError());
// sample call for UserManagementServiceGet::GetGroupsByName()
if($userManagementServiceGet->GetGroupsByName(new UserManagementStructGetGroupsByName(/*** update parameters list ***/)))
    print_r($userManagementServiceGet->getResult());
else
    print_r($userManagementServiceGet->getLastError());
// sample call for UserManagementServiceGet::GetUsersInGroup()
if($userManagementServiceGet->GetUsersInGroup(new UserManagementStructGetUsersInGroup(/*** update parameters list ***/)))
    print_r($userManagementServiceGet->getResult());
else
    print_r($userManagementServiceGet->getLastError());

/***************************************
 * Example for UserManagementServiceList
 */
$userManagementServiceList = new UserManagementServiceList();
// sample call for UserManagementServiceList::ListUsers()
if($userManagementServiceList->ListUsers(new UserManagementStructListUsers(/*** update parameters list ***/)))
    print_r($userManagementServiceList->getResult());
else
    print_r($userManagementServiceList->getLastError());
// sample call for UserManagementServiceList::ListGroups()
if($userManagementServiceList->ListGroups(new UserManagementStructListGroups(/*** update parameters list ***/)))
    print_r($userManagementServiceList->getResult());
else
    print_r($userManagementServiceList->getLastError());

/*****************************************
 * Example for UserManagementServiceUpdate
 */
$userManagementServiceUpdate = new UserManagementServiceUpdate();
// sample call for UserManagementServiceUpdate::UpdateContactInfo()
if($userManagementServiceUpdate->UpdateContactInfo(new UserManagementStructUpdateContactInfo(/*** update parameters list ***/)))
    print_r($userManagementServiceUpdate->getResult());
else
    print_r($userManagementServiceUpdate->getLastError());
// sample call for UserManagementServiceUpdate::UpdateUserBio()
if($userManagementServiceUpdate->UpdateUserBio(new UserManagementStructUpdateUserBio(/*** update parameters list ***/)))
    print_r($userManagementServiceUpdate->getResult());
else
    print_r($userManagementServiceUpdate->getLastError());
// sample call for UserManagementServiceUpdate::UpdatePassword()
if($userManagementServiceUpdate->UpdatePassword(new UserManagementStructUpdatePassword(/*** update parameters list ***/)))
    print_r($userManagementServiceUpdate->getResult());
else
    print_r($userManagementServiceUpdate->getLastError());

/****************************************
 * Example for UserManagementServiceReset
 */
$userManagementServiceReset = new UserManagementServiceReset();
// sample call for UserManagementServiceReset::ResetPassword()
if($userManagementServiceReset->ResetPassword(new UserManagementStructResetPassword(/*** update parameters list ***/)))
    print_r($userManagementServiceReset->getResult());
else
    print_r($userManagementServiceReset->getLastError());

/*****************************************
 * Example for UserManagementServiceUnlock
 */
$userManagementServiceUnlock = new UserManagementServiceUnlock();
// sample call for UserManagementServiceUnlock::UnlockAccount()
if($userManagementServiceUnlock->UnlockAccount(new UserManagementStructUnlockAccount(/*** update parameters list ***/)))
    print_r($userManagementServiceUnlock->getResult());
else
    print_r($userManagementServiceUnlock->getLastError());

/**************************************
 * Example for UserManagementServiceSet
 */
$userManagementServiceSet = new UserManagementServiceSet();
// sample call for UserManagementServiceSet::SetSystemRole()
if($userManagementServiceSet->SetSystemRole(new UserManagementStructSetSystemRole(/*** update parameters list ***/)))
    print_r($userManagementServiceSet->getResult());
else
    print_r($userManagementServiceSet->getLastError());
// sample call for UserManagementServiceSet::SetGroupIsPublic()
if($userManagementServiceSet->SetGroupIsPublic(new UserManagementStructSetGroupIsPublic(/*** update parameters list ***/)))
    print_r($userManagementServiceSet->getResult());
else
    print_r($userManagementServiceSet->getLastError());

/*****************************************
 * Example for UserManagementServiceDelete
 */
$userManagementServiceDelete = new UserManagementServiceDelete();
// sample call for UserManagementServiceDelete::DeleteUsers()
if($userManagementServiceDelete->DeleteUsers(new UserManagementStructDeleteUsers(/*** update parameters list ***/)))
    print_r($userManagementServiceDelete->getResult());
else
    print_r($userManagementServiceDelete->getLastError());
// sample call for UserManagementServiceDelete::DeleteGroup()
if($userManagementServiceDelete->DeleteGroup(new UserManagementStructDeleteGroup(/*** update parameters list ***/)))
    print_r($userManagementServiceDelete->getResult());
else
    print_r($userManagementServiceDelete->getLastError());

/**************************************
 * Example for UserManagementServiceAdd
 */
$userManagementServiceAdd = new UserManagementServiceAdd();
// sample call for UserManagementServiceAdd::AddMembersToInternalGroup()
if($userManagementServiceAdd->AddMembersToInternalGroup(new UserManagementStructAddMembersToInternalGroup(/*** update parameters list ***/)))
    print_r($userManagementServiceAdd->getResult());
else
    print_r($userManagementServiceAdd->getLastError());
// sample call for UserManagementServiceAdd::AddMembersToExternalGroup()
if($userManagementServiceAdd->AddMembersToExternalGroup(new UserManagementStructAddMembersToExternalGroup(/*** update parameters list ***/)))
    print_r($userManagementServiceAdd->getResult());
else
    print_r($userManagementServiceAdd->getLastError());

/*****************************************
 * Example for UserManagementServiceRemove
 */
$userManagementServiceRemove = new UserManagementServiceRemove();
// sample call for UserManagementServiceRemove::RemoveMembersFromInternalGroup()
if($userManagementServiceRemove->RemoveMembersFromInternalGroup(new UserManagementStructRemoveMembersFromInternalGroup(/*** update parameters list ***/)))
    print_r($userManagementServiceRemove->getResult());
else
    print_r($userManagementServiceRemove->getLastError());
// sample call for UserManagementServiceRemove::RemoveMembersFromExternalGroup()
if($userManagementServiceRemove->RemoveMembersFromExternalGroup(new UserManagementStructRemoveMembersFromExternalGroup(/*** update parameters list ***/)))
    print_r($userManagementServiceRemove->getResult());
else
    print_r($userManagementServiceRemove->getLastError());

/***************************************
 * Example for UserManagementServiceSync
 */
$userManagementServiceSync = new UserManagementServiceSync();
// sample call for UserManagementServiceSync::SyncExternalUser()
if($userManagementServiceSync->SyncExternalUser(new UserManagementStructSyncExternalUser(/*** update parameters list ***/)))
    print_r($userManagementServiceSync->getResult());
else
    print_r($userManagementServiceSync->getLastError());
