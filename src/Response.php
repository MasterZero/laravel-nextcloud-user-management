<?php
namespace MasterZero\Nextcloud;

use MasterZero\Nextcloud\Exceptions\XMLParseException;

/**
* class MasterZero\Nextcloud\Response
*/
class Response
{

    /**
     * http code
     */
    protected $code;

    /**
     * http content
     */
    protected $answer;

    /**
     * nextcloud answer status
     */
    protected $status;

    /**
     * nextcloud answer message
     */
    protected $message;

    /**
     * decoded xml answer to array
     */
    protected $xml;


    /**
     * @param $answer | string: http content
     * @param $code | int: http code of answer
     * @throws MasterZero\Nextcloud\Exceptions\XMLParseException
     */
    public function __construct(string $answer = '', int $code = 0)
    {
        $this->answer = $answer;
        $this->code = $code;
        $this->xml = $this->stringToXmlArray($answer);


        if(!isset($this->xml['meta'])
            || !isset($this->xml['meta']['statuscode'])
            || !isset($this->xml['meta']['message'])
        ) {
            throw new XMLParseException("Error on parsing xml response from nextcloud.", 1);
        }

        $this->status = (int)$this->xml['meta']['statuscode'];
        $this->message = $this->xml['meta']['message'];
    }

    /**
     * get http code
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * get http content
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * get nextcloud status
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * get nextcloud message
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * get custom parameter of xml answer
     * @param $offset | string|null
     * @param $ret_on_unset | any
     * @return any
     */
    public function getData($offset = null, $ret_on_unset = '')
    {
        if(is_null($offset)) {
            return $this->xml;
        }

        if(!isset($this->xml['data'][$offset])) {
            return $ret_on_unset;
        } else {
            return $this->xml['data'][$offset];
        }
    }

    /**
     * xml => array convertation method
     * @param $str | string
     * @return array
     */
    protected function stringToXmlArray(string $str)
    {
        return json_decode(json_encode(simplexml_load_string($str)),1);
    }
}

