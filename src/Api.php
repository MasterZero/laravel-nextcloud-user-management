<?php
namespace MasterZero\Nextcloud;

use MasterZero\Nextcloud\Exceptions\CurlException;


/**
* class MasterZero\Nextcloud\Api
*/
class Api
{

    /**
     * login for http-auth in nextcloud api
     */
    protected $login;

    /**
     * password for http-auth in nextcloud api
     */
    protected $password;

    /**
     * url for nextcloud server. It must includes protocol. The end of url must no contains '/' character
     * examples:
     * http://localhost
     * https://production-site.com
     * http://develop.localhost:3500
     */
    protected $baseUrl;

    /**
     * path to api endpoint
     */
    protected $apiPath = 'ocs/v1.php';

    /**
     * verify ssl sertificates on nextcloud server.
     * must be 'true' in production
     */
    protected $sslVerify = true;

    /**
     * path for user actions
     */
    protected $userPath = 'cloud/users';

    /**
     * path suffix for enable
     */
    protected $enablePath = 'enable';


    /**
     * path suffix for disable
     */
    protected $disablePath = 'disable';


    /**
     * http methods
     */
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';


    /**
     * @param $params | array
     * contain custom parameters to create Api instance.
     * all  defined in $params parameters will be overwrited by them.
     */
    public function __construct(array $params = [])
    {

        $this->login = $params['login'] ?? config("nextcloud.login");
        $this->password = $params['password'] ?? config("nextcloud.password");
        $this->baseUrl = $params['baseUrl'] ?? config("nextcloud.baseUrl");


        $initialParam = [
            'apiPath',
            'sslVerify',

            'userPath',
            'enablePath',
            'disablePath',
        ];

        foreach ($initialParam as $param) {

            if (isset($params[$param])) {
                $this->$param = $params[$param];
            }

        }

    }


    /**
     * method to get nextcloud user list
     *
     * @param $search | string: string to search users by userid
     * @param $limit | int
     * @param $offset | int
     * @return array [
     *    success: is success request
     *    message: comment message from nextcloud server
     *    users: array of userid's
     *    response | MasterZero\Nextcloud\Response: response object with details of nextcloud answer
     *    ]
     * @throws MasterZero\Nextcloud\Exceptions\XMLParseException
     * @throws MasterZero\Nextcloud\Exceptions\CurlException
     */
    public function getUserList(string $search = '', int $limit = 0, int $offset = 0) : array
    {

        $url = $this->baseUrl . '/' . $this->apiPath .  '/' . $this->userPath;
        $method = static::METHOD_GET;

        $params = [];

        if (strlen($search)) {
            $params['search'] = $search;
        }

        if ($limit) {
            $params['limit'] = $limit;
        }

        if ($offset) {
            $params['offset'] = $offset;
        }


        if (!empty($params)) {
            $url .= '?' . $this->serializeParams($params);
        }

        $response = $this->request($url, $method);

        $userData = $response->getData('users');

        $ret = [
            'success' => $response->getStatus() === Status::USERLIST_OK,
            'message' => $response->getMessage(),
            'users' => $userData['element'],
            'response' => $response,
        ];

        return $ret;
    }

    /**
     * method to get nextcloud user data
     *
     * @param $user | string: userid
     * @return array [
     *    success: is success request
     *    message: comment message from nextcloud server
     *    response | MasterZero\Nextcloud\Response: response object with details of nextcloud answer
     *    ]
     * @throws MasterZero\Nextcloud\Exceptions\XMLParseException
     * @throws MasterZero\Nextcloud\Exceptions\CurlException
     */
    public function getUser(string $user=''){
        $url = parent::baseUrl . '/' . parent::apiPath .  '/' . parent::userPath . '/' . $user;
        $method = static::METHOD_GET;

        $response = parent::request($url, $method);

        $userData = $response->getData('users');

        $ret = [
            'success' => $response->getStatus() === Status::USERLIST_OK,
            'message' => $response->getMessage(),
            'response' => $response,
        ];
        return $ret;       
    }

    /**
     * method to create nextcloud user
     *
     * @param $userid | string: username for create. must be unique.
     * @param $password | string
     * @return array [
     *    success: is success request
     *    message: comment message from nextcloud server
     *    response | MasterZero\Nextcloud\Response: response object with details of nextcloud answer
     *    ]
     * @throws MasterZero\Nextcloud\Exceptions\XMLParseException
     * @throws MasterZero\Nextcloud\Exceptions\CurlException
     */
    public function createUser(string $userid, string $password) : array
    {

        $url = $this->baseUrl . '/' . $this->apiPath .  '/' . $this->userPath;
        $method = static::METHOD_POST;

        $params = $this->serializeParams([
            'userid' => $userid,
            'password' => $password,
        ]);

        $response = $this->request($url, $method, $params);


        $ret = [
            'success' => $response->getStatus() === Status::CREATEUSER_OK,
            'message' => $response->getMessage(),
            'response' => $response,
        ];


        return $ret;
    }


    /**
     * method to edit nextcloud user parameters
     *
     * @param $userid | string
     * @param $key | string: parameter to edit (email | quota | display | password)
     * @param $value | string
     * @return array [
     *    success: is success request
     *    message: comment message from nextcloud server
     *    response | MasterZero\Nextcloud\Response: response object with details of nextcloud answer
     *    ]
     * @throws MasterZero\Nextcloud\Exceptions\XMLParseException
     * @throws MasterZero\Nextcloud\Exceptions\CurlException
     */
    public function editUser(string $userid, string $key, string $value) : array
    {

        $url = $this->baseUrl . '/' . $this->apiPath .  '/' . $this->userPath . '/' . $userid;
        $method = static::METHOD_PUT;

        $params = $this->serializeParams([
            'key' => $key,
            'value' => $value,
        ]);

        $response = $this->request($url, $method, $params);


        $ret = [
            'success' => $response->getStatus() === Status::EDITUSER_OK,
            'message' => $response->getMessage(),
            'response' => $response,
        ];

        return $ret;
    }


    /**
     * method to disable nextcloud user
     *
     * @param $userid | string
     * @return array [
     *    success: is success request
     *    message: comment message from nextcloud server
     *    response | MasterZero\Nextcloud\Response: response object with details of nextcloud answer
     *    ]
     * @throws MasterZero\Nextcloud\Exceptions\XMLParseException
     * @throws MasterZero\Nextcloud\Exceptions\CurlException
     */
    public function disableUser(string $userid) : array
    {

        $url = $this->baseUrl . '/' . $this->apiPath .  '/' . $this->userPath . '/' . $userid . '/' . $this->disablePath;
        $method = static::METHOD_PUT;

        $response = $this->request($url, $method);

        $ret = [
            'success' => $response->getStatus() === Status::DISABLEUSER_OK,
            'message' => $response->getMessage(),
            'response' => $response,
        ];

        return $ret;
    }



    /**
     * method to enable nextcloud user
     *
     * @param $userid | string
     * @return array [
     *    success: is success request
     *    message: comment message from nextcloud server
     *    response | MasterZero\Nextcloud\Response: response object with details of nextcloud answer
     *    ]
     * @throws MasterZero\Nextcloud\Exceptions\XMLParseException
     * @throws MasterZero\Nextcloud\Exceptions\CurlException
     */
    public function enableUser(string $userid) : array
    {
        $url = $this->baseUrl . '/' . $this->apiPath .  '/' . $this->userPath . '/' . $userid . '/' . $this->enablePath;
        $method = static::METHOD_PUT;

        $response = $this->request($url, $method);

        $ret = [
            'success' => $response->getStatus() === Status::ENABLEUSER_OK,
            'message' => $response->getMessage(),
            'response' => $response,
        ];

        return $ret;
    }



    /**
     * method to delete nextcloud user
     *
     * @param $userid | string
     * @return array [
     *    success: is success request
     *    message: comment message from nextcloud server
     *    response | MasterZero\Nextcloud\Response: response object with details of nextcloud answer
     *    ]
     * @throws MasterZero\Nextcloud\Exceptions\XMLParseException
     * @throws MasterZero\Nextcloud\Exceptions\CurlException
     */
    public function deleteUser(string $userid) : array
    {
        $url = $this->baseUrl . '/' . $this->apiPath .  '/' . $this->userPath . '/' . $userid;
        $method = static::METHOD_DELETE;

        $response = $this->request($url, $method);

        $ret = [
            'success' => $response->getStatus() === Status::DELETEUSER_OK,
            'message' => $response->getMessage(),
            'response' => $response,
        ];

        return $ret;
    }


    /**
     * get default required headers
     *
     * @return array
     */
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type: application/x-www-form-urlencoded',
            'OCS-APIRequest: true'
        ];
    }

     /**
     * serialize array [key1 => value1, key2 => value2] to string key1=value1&key2=value2
     *
     * @return string
     */
    protected function serializeParams(array $params): string
    {

        if (!count($params)) {
            return '';
        }

        $expressions = [];

        foreach ($params as $key => $value) {
            $expressions[] = urlencode($key) . '=' . urlencode($value);
        }

        return implode('&', $expressions);
    }


    /**
     * do request
     *
     * @param $url | string
     * @param $method | string
     * @param $headers | array of strings
     * @return MasterZero\Nextcloud\Response
     * @throws MasterZero\Nextcloud\Exceptions\XMLParseException
     * @throws MasterZero\Nextcloud\Exceptions\CurlException
     */
    protected function request(string $url, string $method = 'GET', $data = '', array $headers = []) : Response
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if($method === static::METHOD_POST) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } elseif ($method === static::METHOD_PUT || $method === static::METHOD_DELETE) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $userowd = $this->login . ':' . $this->password;
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $userowd);


        $headers = array_merge($this->defaultHeaders(), $headers);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->sslVerify);// this should be set to true in production

        $responseData = curl_exec($ch);

        if(curl_errno($ch)) {
            throw new CurlException('[Nextcloud] ' . curl_error($ch), 1);
        }

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return new Response($responseData, $httpcode);
    }
}

