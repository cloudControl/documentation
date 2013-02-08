<?php
/**
 * require Pear::HTTP_Request2, Pear::Net_URL2 packages
 */
require_once 'HTTP/Request2.php';
require_once 'Net/URL2.php';

/*
 phpcclib

 library for accessing the cloudControl API using PHP

 Copyright 2010 cloudControl UG (haftungsbeschraenkt)

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

class API {
    /**
     * predefined API URL
     * @const string
     */
    const URL = 'https://api.cloudcontrol.com';

    /**
     * set certificate validation (if https protocol used)
     * @const boolean
     */
    const SSL_VERIFY_PEER = false;

    /**
     * api version
     * @var string
     */
    const VERSION = '0.1.3.6';

    /**
     * token length - to check the token
     * @var int
     */
    const TOKEN_STRLEN = 30;

    private $_url = null;
    private $_token = null;

    /**
     * constructor
     *
     * @param string $url
     *
     * @return API
     */
    public function __construct($url=Api::URL) {
        $this->setUrl($url);
    }

    /**
     * set url
     * @todo check if it is a valid url
     * @param string $url
     *
     * @return void
     */
    public function setUrl($url) {
        $this->_url = $url;
    }

    /**
     * get url
     *
     * @return string
     */
    public function getUrl() {
        return $this->_url;
    }

    /**
     * requiresToken checks that methods that require
     * a token can't be called without a token.
     *
     * If checkToken doesn't return true; a TokenRequiredError exception is
     * raised telling the caller to use the create_token method to get a
     * valid token.
     *
     * @throws TokenRequiredError
     *
     * @return void
     */
    public function requiresToken() {
        $token = $this->getToken();
        if (!$this->checkToken($token)) {
            throw new TokenRequiredError();
        }
    }

    /**
     * Queries the API for a new Token and saves it as self._token.
     *
     * @param string $email users email-address
     * @param string $password users password
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws ConflictDuplicateError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return boolean
     */
    public function auth($email, $password) {
        $request = new Request($this->_url);
        $request->setAuth($email, $password);
        $content = $request->post('/token/', array());
        $tokenObject = $this->_jsonDecode($content);
        return $this->setToken($tokenObject->token);
    }

    /**
     * This method checks if there's a token.
     *
     * @todo implement a stronger check
     * @param string $token token to check
     *
     * @return boolean
     */
    public function checkToken($token) {
        if (strlen($token) == Api::TOKEN_STRLEN) {
            return true;
        }
        return false;
    }

    /**
     * We use to set the token.
     *
     * @param string $token token to set
     *
     * @return void
     */
    public function setToken($token) {
        if ($this->checkToken($token)) {
            $this->_token = $token;
        }
        return $this;
    }

    /**
     * We use getToken to get the token.
     *
     * @return string
     */
    public function getToken() {
        return $this->_token;
    }

    /**
     * Create a new application and return it.
     *
     * @param string $applicationName applications name
     * @param string $type applications type [default:"php"]
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return stdObj contains app data
     */
    public function application_create($applicationName, $type="php") {
        $resource = '/app/';
        $data = array(
            'name' => $applicationName,
            'type' => $type
        );
        return $this->_executePost($resource, $data);
    }

    /**
     * Returns a list of applications.
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return array<stdObj> contains app list
     */
    public function application_getList() {
        return $this->_executeGet('/app/');
    }

    /**
     * Returns all application details.
     *
     * @param string $applicationName applications name
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return stdObj contains app details
     */
    public function application_getDetails($applicationName) {
        $resource = sprintf('/app/%s/', $applicationName);
        return $this->_executeGet($resource);
    }

    /**
     * Delete a application.
     *
     * @param string $applicationName applications name
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return true
     */
    public function application_delete($applicationName) {
        $resource = sprintf('/app/%s/', $applicationName);
        $this->_executeDelete($resource);
        return true;
    }

    /**
     * Add a user to an application.
     *
     * @param string $applicationName applications name
     * @param string $email users email
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws ConflictDuplicateError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return stdObj contains app and user data
     */
    public function application_addUser($applicationName, $email) {
        $resource = sprintf('/app/%s/user/', $applicationName);
        $data = array('email' => email);
        return $this->_executePost($resource, $data);
    }

    /**
     * Remove a user from an application.
     *
     * @param string $applicationName applications name
     * @param string $userName users name
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return boolean
     */
    public function application_removeUser($applicationName, $userName) {
        $resource = sprintf('/app/%s/user/%s/', $applicationName, $userName);
        $this->_executeDelete($resource);
        return true;
    }

    /**
     * Create a new deployment.
     * deploymentName is optional
     * Attention!, (at current state), the first deployment of an application
     * will ever be named "default". But if you give your first deployment a certain name
     * an additional alias with this name will be created
     *
     * @param string $applicationName applications name
     * @param string $deploymentName deployments name [default="default"]
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return stdObj contains deployment data
     */
    public function deployment_create($applicationName, $deploymentName='default') {
        $resource = sprintf('/app/%s/deployment/', $applicationName);
        $data = array();
        if ($deploymentName) {
            $data['name'] = $deploymentName;
        }
        return $this->_executePost($resource, $data);
    }

    /**
     * return applications deployment.list
     *
     * @param string $applicationName applications name
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws ConflictDuplicateError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return array<stdObj> contains deployment info data
     */
    public function deployment_getList($applicationName) {
        $resource = sprintf('/app/%s/deployment/', $applicationName);
        return $this->_executeGet($resource);
    }

    /**
     * Returns deployment details.
     *
     * @param string $applicationName applications name
     * @param string $deploymentName deployments name
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return stdObj contains deployment details
     */
    public function deployment_getDetails($applicationName, $deploymentName) {
        $resource = sprintf('/app/%s/deployment/%s/', $applicationName, $deploymentName);
        return $this->_executeGet($resource);
    }

    /**
     * Returns deployments boxeslist for one month backwards.
     *
     * @param string $applicationName applications name
     * @param string $deploymentName deployments name
     * @param int $from start timestamp
     * @param int $until end timestamp
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return array<stdObj> contains boxes list
     */
    public function deployment_getBoxeslist($applicationName, $deploymentName, $from, $until) {
        $resource = sprintf('/app/%s/deployment/%s/boxes/', $applicationName, $deploymentName);
        $data = array('start' => $from, 'end' => $until);
        return $this->_executeGet($resource, $requiresToken=true, $data=$data);
    }

    /**
     * Updates a deployment.
     * Use this to deploy new versions. If no version is provided the
     * last version is deployed.
     *
     * @param string $applicationName applications name
     * @param string $deploymentName deployments name
     * @param array $data deployments data in an associative array
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return stdObj contains deployment data
     */
    public function deployment_update($applicationName, $deploymentName, $data) {
        $resource = sprintf('/app/%s/deployment/%s/', $applicationName, $deploymentName);
        return $this->_executePut($resource, $data);
    }

    /**
     * Delete a deployment.
     *
     * @param string $applicationName applications name
     * @param string $deploymentName deployments name
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return boolean
     */
    public function deployment_delete($applicationName, $deploymentName) {
        $resource = sprintf('/app/%s/deployment/%s/', $applicationName, $deploymentName);
        $this->_executeDelete($resource);
        return true;
    }

    /**
     * Get a deployment's log by log_type.
     * log_type choices are 'access' or 'error'
     * last_time is optional - any English textual datetime description '2010-8-30 17:04:22' Y-m-d H:i:s
     *
     * @param string $applicationName applications name
     * @param string $deploymentName deployments name
     * @param string $logType logs type [default:"error"]
     * @param string $lastTime logs start time [default:null]
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return stdObj contains log data
     */
    public function deployment_getLog($applicationName, $deploymentName, $logType="error", $lastTime=null) {
        $url = sprintf(
            '/app/%s/deployment/%s/log/%s/',
            $applicationName,
            $deploymentName,
            $logType
        );
        $resource = $url;
        if (!is_null($lastTime)) {
            $timestamp = strtotime($lastTime);
            $resource = sprintf('%s?timestamp=%s', $url, $timestamp);
        }
        return $this->_executeGet($resource);
    }

    /**
     * add an addon to a deployment
     *
     * @param string $applicationName applications name
     * @param string $deploymentName deployments name
     * @param string $addonName addons name
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return array<stdObj> contains addon list
     */
    public function deployment_addAddon($applicationName, $deploymentName, $addonName) {
        $resource = sprintf('/app/%s/deployment/%s/addon/', $applicationName, $deploymentName);
        $data = array('addon' => $addonName);
        return $this->_executePost($resource, $data);
    }
    

    /**
     * get sso login data
     *
     * @param string $applicationName applications name
     * @param string $deploymentName deployments name
     * @param string $addonName addons name
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return stdObj sso login data
     */
    public function deployment_getAddonSSO($applicationName, $deploymentName, $addonName) {
        $resource = sprintf('/app/%s/deployment/%s/addon/%s/login', $applicationName, $deploymentName, $addonName);
        return $this->_executeGet($resource);
    }

    /**
     * creates an alias for a deployment.
     *
     * @param string $applicationName applications name
     * @param string $deploymentName deployments name
     * @param string $aliasName new alias name
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return stdObj contains alias details
     */
    public function alias_create($applicationName, $deploymentName, $aliasName) {
        $resource = sprintf('/app/%s/deployment/%s/alias/', $applicationName, $deploymentName);
        $data = array('name' => $aliasName);
        return $this->_executePost($resource, $data);
    }

    /**
     * Get all alias details.
     *
     * @param string $applicationName applications name
     * @param string $deploymentName deployments name
     * @param string $aliasName alias' name
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return stdObj contains app details
     */
    public function alias_getDetails($applicationName, $deploymentName, $aliasName) {
        $resource = sprintf('/app/%s/deployment/%s/alias/%s/', $applicationName, $deploymentName, $aliasName);
        return $this->_executeGet($resource);
    }

    /**
     * Remove an alias from a deployment.
     *
     * @param string $applicationName applications name
     * @param string $deploymentName deployments name
     * @param string $aliasName alias' name
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return boolean
     */
    public function alias_delete($applicationName, $deploymentName, $aliasName) {
        $resource = sprintf('/app/%s/deployment/%s/alias/%s/', $applicationName, $deploymentName, $aliasName);
        $this->_executeDelete($resource);
        return true;
    }

    /**
     * Get a list of users. Usually just your own.
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return array<stdObj> contains user list
     */
    public function user_getList() {
        return $this->_executeGet('/user/');
    }

    /**
     * Create a new user.
     *
     * @param string $userName users name
     * @param string $email users email
     * @param string $password users password
     *
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws ConflictDuplicateError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return stdObj contains user details
     */
    public function user_create($userName, $email, $password) {
        $resource = '/user/';
        $data = array(
            'username' => $userName,
            'email' => $email,
            'password' => $password
        );
        return $this->_executePost($resource, $data, $requiresToken=false);
    }

    /**
     * Get user by name.
     *
     * @param string $userName users name
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return stdObj contains user details
     */
    public function user_getDetails($userName) {
        $resource = sprintf('/user/%s/', $userName);
        return $this->_executeGet($resource);
    }

    /**
     * activate user by name.
     * Use this for activation after registration.
     *
     * @param string $userName users name
     * @param string $activationCode users activation code
     *
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return boolean
     */
    public function user_activate($userName, $activationCode) {
        $resource = sprintf('/user/%s/', $userName);
        $data = array(
            'activation_code' => $activationCode
        );
        $this->_executePut($resource, $data, $requiresToken=false);
        return true;
    }

    /**
     * Delete user by $userName.
     * .
     * @param string $userName users name
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return boolean
     */
    public function user_delete($userName) {
        $resource = sprintf('/user/%s/', $userName);
        $this->_executeDelete($resource);
        return true;
    }

    /**
     * Get a list of keys belonging to user selected by $userName.
     *
     * @param string $userName users name
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return array<stdObj> contains key list
     */
    public function user_getKeyList($userName) {
        $resource = sprintf('/user/%s/key/', $userName);
        return $this->_executeGet($resource);
    }

    /**
     * Add a key to user by $userName.
     *
     * @param string $userName users name
     * @param string $publicKey users public key as string
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws ConflictDuplicateError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return stdObj contains key data
     */
    public function user_addKey($userName, $publicKey) {
        $resource = sprintf('/user/%s/key/', $userName);
        $data = array('key' => $publicKey);
        return $this->_executePost($resource, $data);
    }

    /**
     * Remove a key from user by $userName.
     * Requires key_id that can be requested using read_user_keys()
     *
     * @param string $userName users name
     * @param string $keyId users public key id
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return boolean
     */
    public function user_removeKey($userName, $keyId) {
        $resource = sprintf('/user/%s/key/%s/', $userName, $keyId);
        $this->_executeDelete($resource);
        return true;
    }

    /**
     * creates a billing account.
     *
     * @param string $userName users name
     * @param string $billingName billing address' name
     * @param array $data billing account data as associative array
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws ConflictDuplicateError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return stdObj contains billing account data
     */
    public function billingAccount_create($userName, $data) {
        $resource = sprintf('/user/%s/billing/', $userName);
        return $this->_executePost($resource, $data);
    }

    /**
     * get a billing account details.
     *
     * @param string $userName users name
     * @param string $billingName billing address' name
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws ConflictDuplicateError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return stdObj contains billing account data
     */
    public function billingAccount_getDetails($userName, $billingName) {
        $resource = sprintf('/user/%s/billing/%s/', $userName, $billingName);
        return $this->_executeGet($resource);
    }

    /**
     * updates a billing account.
     *
     * @param string $userName users name
     * @param string $billingName billing address' name
     * @param array $data billing account data as associative array
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return stdObj contains billing account data
     */
    public function billingAccount_update($userName, $billingName, $data) {
        $resource = sprintf('/user/%s/billing/%s/', $userName, $billingName);
        return $this->_executePut($resource, $data);
    }

    /**
     * return all users billling accounts
     *
     * @param string $userName users name
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return array<stdObj> contains billing account data
     */
    public function billingAccount_getList($userName) {
        $resource = sprintf('/user/%s/billing/', $userName);
        return $this->_executeGet($resource);
    }

    /**
     * create a billing account voucher.
     *
     * @param string $userName users name
     * @param string $billingName billing address' name
     * @param array $data billing account vouchers data as associative array
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws ConflictDuplicateError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return stdObj contains billing voucher data
     */
    public function billingAccount_addVoucher($userName, $billingName, $data) {
        $resource = sprintf('/user/%s/billing/%s/voucher/', $userName, $billingName);
        return $this->_executePost($resource, $data);
    }

    /**
     * get a list o billing account vouchers.
     *
     * @param string $userName users name
     * @param string $billingName billing address' name
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return array<stdObj> contains billing voucher data
     */
    public function billingAccount_getVoucherList($userName, $billingName)
    {
        $resource = sprintf('/user/%s/billing/%s/voucher/', $userName, $billingName);
        return $this->_executeGet($resource);
    }

    /**
     * return list of all addons
     *
     * @param string $userName users name
     *
     * @throws BadRequestError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return array<stdObj> contains all addons
     */
    public function addon_getList() {
        return $this->_executeGet('/addon/', $requiresToken=false);
    }
    
    /**
     * return list of all support plans
     *
     * @throws BadRequestError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return array<stdObj> contains all available support plans
     */
    public function support_getList() {
        return $this->_executeGet('/support/', $requiresToken=false);
    }
    
    /**
     * return a single support plan
     *
     * @param string $planName name of the support plan
     * 
     * @throws BadRequestError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return array<stdObj> contains all available support plans
     */
    public function support_getDetails($planName) {
        $resource = sprintf('/support/%s/', $planName);
        return $this->_executeGet($resource, $requiresToken=false);
    }

    /**
     * Create a new deployment worker.
     *
     * @param string $applicationName applications name
     * @param string $deploymentName deployments name
     * @param string $command at current state, the path to php-worker-file, relative from deployment base dir (f.e. '/mystuff/worker.php')
     * @param string $parameter [default:''] optional start parameter (read by worker file)
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return stdObj contains worker data
     */
    public function worker_create($applicationName, $deploymentName, $command, $parameter='', $size=1) {
        $resource = sprintf('/app/%s/deployment/%s/worker/', $applicationName, $deploymentName);
        $data = array();
        $data['command'] = $command;
        if (strlen($parameter) > 0) {
            $data['params'] = $parameter;
        }
        $data['size'] = $size;
        return $this->_executePost($resource, $data);
    }

    /**
     * get deployment worker details.
     *
     * @param string $applicationName applications name
     * @param string $deploymentName deployments name
     * @param string $workerId deployment worker id
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return stdObj contains worker data
     */
    public function worker_getDetails($applicationName, $deploymentName, $workerId) {
        $resource = sprintf('/app/%s/deployment/%s/worker/%s/', $applicationName, $deploymentName, $workerId);
        return $this->_executeGet($resource);
    }

    /**
     * get deployment worker list.
     *
     * @param string $applicationName applications name
     * @param string $deploymentName deployments name
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return array<stdObj> contains worker data
     */
    public function worker_getList($applicationName, $deploymentName) {
        $resource = sprintf('/app/%s/deployment/%s/worker/', $applicationName, $deploymentName);
        return $this->_executeGet($resource);
    }

    /**
     * delete deployment worker (not the worker-file itself, but the supervisor-daemon entry).
     *
     * @param string $applicationName applications name
     * @param string $deploymentName deployments name
     * @param string $workerId deployment worker id
     *
     * @throws TokenRequiredError
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return boolean
     */
    public function worker_delete($applicationName, $deploymentName, $workerId) {
        $resource = sprintf('/app/%s/deployment/%s/worker/%s/', $applicationName, $deploymentName, $workerId);
        $this->_executeDelete($resource);
        return true;
    }

    private function _executeGet($resource, $requiresToken=true, $data=array()) {
        if($requiresToken) {
            $this->requiresToken();
        }
        $request = new Request($this->_url, $this->getToken());
        $content = $request->get($resource, $data);
        return $this->_jsonDecode($content);
    }

    private function _executePost($resource, $data, $requiresToken=true) {
        if($requiresToken) {
            $this->requiresToken();
        }
        $request = new Request($this->_url, $this->getToken());
        $content = $request->post($resource, $data);
        return $this->_jsonDecode($content);
    }

    private function _executePut($resource, $data, $requiresToken=true) {
        if($requiresToken) {
            $this->requiresToken();
        }
        $request = new Request($this->_url, $this->getToken());
        $content = $request->put($resource, $data);
        return true;
    }

    private function _executeDelete($resource, $requiresToken=true) {
        if($requiresToken) {
            $this->requiresToken();
        }
        $request = new Request($this->_url, $this->getToken());
        $content = $request->delete($resource);
        return $this->_jsonDecode($content);
    }

    /**
     * try to decode servers response
     *
     * @param string $content json encoded servers data
     *
     * @throws CCException
     *
     * @return mixed array (itemlist) or stdObj (single item)
     */
    private function _jsonDecode($content) {
        $data = json_decode($content);
        if (strlen($content) > 0 && json_last_error() !== JSON_ERROR_NONE) {
            throw new CCException("no valid json: " . $content, 500);
        }
        return $data;
    }
}

###
#
# EXCEPTIONS
#
###

/*
 * base cloudControl api exception
 */

class CCException extends Exception {
}

/*
 We raise this exception if the API was unreachable.
 */

class ConnectionException extends CCException {
}

/*
 We raise this exception if a method requires a token but self._token
 is none.

 Use the create_token() method to get a new token.
 */

class TokenRequiredError extends CCException {
    public function __toString()
    {
        return 'No valid token. Use create_token(email, password) to get a new one';
    }
}

/*
 We raise this exception whenever the API answers with HTTP STATUS 400
 BAD REQUEST.
 */

class BadRequestError extends CCException {
    private $_msgs = array();

    public function __construct($message)
    {
        $this->message = 'BadRequest';
        /*
         * You will get a string like this
         * Bad Request {"lastname": "This field is required.", "firstname": "This field is required."}
         * therefore we cut the first 12 chars from errorMessage
         */
        $obj = json_decode(substr($message, 12));

        if (json_last_error() === JSON_ERROR_NONE && !empty($obj)) {
            $this->message = '';
            foreach ($obj as $k => $v) {
                $this->message .= sprintf("%s: %s\n", $k, $v);
            }
        }
    }
}

/*
 We raise this exception whenever the API answers with HTTP STATUS 401
 UNAUTHORIZED.
 */

class UnauthorizedError extends CCException {
}

/*
 We raise this exception whenever the API answers with HTTP STATUS 403
 FORBIDDEN.
 */

class ForbiddenError extends CCException {
}

/*
 We raise this exception whenever the API answers with HTTP STATUS 409
 DUPLICATE ENTRY.
 */

class ConflictDuplicateError extends CCException {
}

/*
 We raise this exception whenever the API answers with HTTP STATUS 410
 GONE.
 */

class GoneError extends CCException {
}

/*
 We raise this exception whenever the API answers with HTTP STATUS 500
 INTERNAL SERVER ERROR.
 */

class InternalServerError extends CCException {
}

/*
 We raise this exception whenever the API answers with HTTP STATUS 501
 NOT IMPLEMENTED.
 */

class NotImplementedError extends CCException {
}

/*
 We raise this exception whenever the API answers with HTTP STATUS 503
 THROTTLED.
 */

class ThrottledError extends CCException {
}

###
#
# Request Class using httplib2 to fire HTTP requests
#
###
/*
 Request is used internally to actually fire API requests. It has some
 handy shortcut methods for POST, GET, PUT and DELETE, sets correct
 headers for each method, takes care of encoding data and handles all API
 errors by throwing exceptions.
 */

class Request {
    private $_email = null;
    private $_password = null;
    private $_token = null;
    private $_version = null;
    private $_url = null;

    /**
     * Request constructor
     *
     * @param string $url api-url
     * @param string $token auth token [default:null]
     * @param string $version client-version [default:Api::VERSION]
     */
    public function __construct($url, $token=null, $version=Api::VERSION) {
        $this->_url = $url;
        $this->_token = $token;
        $this->_version = $version;
    }

    /**
     * set auth data
     *
     * @param string $email users email
     * @param string $password users password
     */
    public function setAuth($email, $password) {
        $this->_email = $email;
        $this->_password = $password;
    }

    /**
     * post request (create)
     *
     * @param string $resource api-resource
     * @param array $data request data as associative array
     *
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws ConflictDuplicateError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return string json encoded servers response
     */
    public function post($resource, $data) {
        return $this->_request($resource, HTTP_Request2::METHOD_POST, $data);
    }

    /**
     * get request
     *
     * @param string $resource api-resource
     *
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return string json encoded servers response
     */
    public function get($resource, $data=array()) {
        return $this->_request($resource, HTTP_Request2::METHOD_GET, $data);
    }

    /**
     * put request (update)
     *
     * @param string $resource api-resource
     * @param string $data request data as associative array
     *
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return string json encoded servers response
     */
    public function put($resource, $data) {
        return $this->_request($resource, HTTP_Request2::METHOD_PUT, $data);
    }

    /**
     * delete request
     *
     * @param string $resource api-resource
     *
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return string json encoded servers response
     */
    public function delete($resource) {
        return $this->_request($resource, HTTP_Request2::METHOD_DELETE);
    }

    /**
     * we use the Pear::HTTP_Request2 for all the heavy HTTP protocol lifting.
     *
     * @param string $resource api-resource
     * @param string $method request method [default:"GET"]
     * @param array $data request data as associative array [default:array()]
     * @param array $headers optional request header [default:array()]
     *
     * @throws ConnectionException
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws ConflictDuplicateError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return string json encoded servers response
     */
    private function _request($resource, $method=HTTP_Request2::METHOD_GET, $data=array(), $headers=array()) {
        $url = $this->_url . $resource;
        $request = new HTTP_Request2($url);
        $request->setConfig('ssl_verify_peer', API::SSL_VERIFY_PEER);

        $methods = array(
            'options' => HTTP_Request2::METHOD_OPTIONS,
            'get' => HTTP_Request2::METHOD_GET,
            'head' => HTTP_Request2::METHOD_HEAD,
            'post' => HTTP_Request2::METHOD_POST,
            'put' => HTTP_Request2::METHOD_PUT,
            'delete' => HTTP_Request2::METHOD_DELETE,
            'trace' => HTTP_Request2::METHOD_TRACE,
            'connect' => HTTP_Request2::METHOD_CONNECT
        );
        $request->setMethod($methods[strtolower($method)]);

        #
        # If the current API instance has a valid token we add the Authorization
        # header with the correct token.
        #
        # In case we do not have a valid token but email and password are
        # provided we automatically use them to add a HTTP Basic Authenticaion
        # header to the request to create a new token.
        #

        if ($this->_token) {
            $headers['Authorization'] = sprintf('cc_auth_token="%s"', $this->_token);
        } else if ($this->_email && $this->_password) {
            $request->setAuth($this->_email, $this->_password, HTTP_Request2::AUTH_BASIC);
        }

        #
        # The API expects the body to be urlencoded. If data was passed to
        # the request method we therefore use urlencode from urllib.
        #
        if (!empty($data)) {
            if ($request->getMethod() == HTTP_Request2::METHOD_GET) {
                $url = $request->getUrl();
                $url->setQueryVariables($data);
            } else {
                // works with post and put
                $request->addPostParameter($data);
                $request->setBody(http_build_query($data));
            }
        }

        #
        # We set the User-Agent Header to pycclib and the local version.
        # This enables basic statistics about still used pycclib versions in
        # the wild.
        #
        $headers['User-Agent'] = sprintf('phpcclib/%s', $this->_version);
        #
        # The API expects PUT or POST data to be x-www-form-urlencoded so we
        # also set the correct Content-Type header.
        #
        if (strtoupper($method) == 'PUT' || strtoupper($method) == 'POST') {
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        }
        #
        # We also set the Content-Length and Accept-Encoding headers.
        #
        //$headers['Content-Length'] = strlen($body);
        $headers['Accept-Encoding'] = 'compress, gzip';
        #
        # Finally we fire the actual request.
        #
        foreach ($headers as $k => $v) {
            $request->setHeader(sprintf('%s: %s', $k, $v));
        }
        for ($i=1; $i<6; $i++) {
            try {
                $response = $request->send();
                return $this->_return($response);
            }
            catch (HTTP_Request2_Exception $e) {
                # if we could not reach the API we wait 1s and try again
                sleep(1);
                # if we tried for the fifth time we give up - and cry a little
                if ($i == 5) {
                    throw new ConnectionException('Could not connect to API...');
                }
            }
        }
    }

    /**
     * evaluate response object
     *
     * @param HTTP_Request2_Response $resp
     *
     * @throws BadRequestError
     * @throws UnauthorizedError
     * @throws ForbiddenError
     * @throws ConflictDuplicateError
     * @throws GoneError
     * @throws InternalServerError
     * @throws NotImplementedError
     * @throws ThrottledError
     * @throws CCException
     *
     * @return string json encoded servers response
     */
    private function _return($resp) {
        #
        # And handle the possible responses according to their HTTP STATUS
        # CODES.
        #
        # 200 OK, 201 CREATED and 204 DELETED result in returning the actual
        # response.
        #
        # All non success STATUS CODES raise an exception containing
        # the API error message.
        #
        if (in_array($resp->getStatus(), array(200, 201, 204)) !== false) {
            return $resp->getBody();
        }
        else if ($resp->getStatus() == 400) {
            throw new BadRequestError($resp->getBody(), $resp->getStatus());
        }
        else if ($resp->getStatus() == 401) {
            throw new UnauthorizedError($resp->getBody(), $resp->getStatus());
        }
        else if ($resp->getStatus() == 403) {
            throw new ForbiddenError($resp->getBody(), $resp->getStatus());
        }
        else if ($resp->getStatus() == 409) {
            throw new ConflictDuplicateError($resp->getBody(), $resp->getStatus());
        }
        else if ($resp->getStatus() == 410) {
            throw new GoneError($resp->getBody(), $resp->getStatus());
        }
        #
        # 500 INTERNAL SERVER ERRORs normally shouldn't happen...
        #
        else if ($resp->getStatus() == 500) {
            throw new InternalServerError($resp->getBody(), $resp->getStatus());
        }
        else if ($resp->getStatus() == 501) {
            throw new NotImplementedError($resp->getBody(), $resp->getStatus());
        }
        else if ($resp->getStatus() == 503) {
            throw new ThrottledError($resp->getBody(), $resp->getStatus());
        }
        #
        # throw CCException anyway
        #
        else {
            throw new CCException ($resp->getBody(), $resp->getStatus());
        }
    }
}
