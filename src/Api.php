<?php
namespace MasterZero\Nextcloud;

use MasterZero\Nextcloud\Exceptions\CurlException;


/**
* @todo add normal descriptions
*/
class Api
{

    protected $login;

    protected $password;

    //http://localhost
    protected $baseUrl;

    protected $apiPath = 'ocs/v1.php';

    protected $sslVerify = true;

    protected $userListPath = 'cloud/users';

    protected $userCreatePath = 'cloud/users';

    protected $userUpdatePath = 'cloud/users';


    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';


    public function __construct(array $params = [])
    {

        $this->login = $params['login'] ?? config("nextcloud.login");
        $this->password = $params['password'] ?? config("nextcloud.password");
        $this->baseUrl = $params['baseUrl'] ?? config("nextcloud.baseUrl");


        $initialParam = [
            'apiPath',
            'sslVerify',

            'userListPath',
            'userCreatePath',
            'userUpdatePath',
        ];

        foreach ($initialParam as $param) {

            if (isset($params[$param])) {
                $this->$param = $params[$param];
            }

        }

    }

    //throws
    //MasterZero\Nextcloud\Exceptions\XMLParseException
    //MasterZero\Nextcloud\Exceptions\CurlException
    public function getUserList(string $search = '', int $limit = 0, int $offset = 0) : array
    {

        $url = $this->baseUrl . '/' . $this->apiPath .  '/' . $this->userListPath;
        $method = static::METHOD_GET;

        $params = [];

        if(strlen($search)) {
            $params['search'] = $search;
        }

        if($limit) {
            $params['limit'] = $limit;
        }

        if($offset) {
            $params['offset'] = $offset;
        }


        if(!empty($params)) {
            $url .= '?' . $this->serializeParams($params);
        }

        $response = $this->request($url, $method);

        $userData = $response->getData('users');

        $ret = [
            'users' => $userData['element'],
            'response' => $response,
            'success' => $response->getStatus() === Status::USERLIST_OK,
        ];

        return $ret;
    }


    //throws
    //MasterZero\Nextcloud\Exceptions\XMLParseException
    //MasterZero\Nextcloud\Exceptions\CurlException
    public function createUser(string $username, string $password) : array
    {

        $url = $this->baseUrl . '/' . $this->apiPath .  '/' . $this->userCreatePath;
        $method = static::METHOD_POST;

        $params = $this->serializeParams([
            'userid' => $username,
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

    protected function defaultHeaders(): array
    {

        return [
            'Content-Type: application/x-www-form-urlencoded',
            'OCS-APIRequest: true'
        ];
    }

    protected function serializeParams(array $params): string
    {

        if(!count($params)) {
            return '';
        }

        $expressions = [];

        foreach ($params as $key => $value) {
            $expressions[] = urlencode($key) . '=' . urlencode($value);
        }

        return implode('&', $expressions);
    }



    protected function request(string $url, string $method = 'GET', $data = '', array $headers = []) : Response
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if($method == static::METHOD_POST) {

            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        } elseif ($method == static::METHOD_PUT) {

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
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
            throw new CurlException(curl_error($ch), 1);
        }

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return new Response($responseData, $httpcode);
    }
}

?>