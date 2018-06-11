<?php
namespace MasterZero\Nextcloud;

use MasterZero\Nextcloud\Exceptions\XMLParseException;


class Response
{

    protected $code;

    protected $answer;

    protected $status;

    protected $message;

    protected $xml;


    //MasterZero\Nextcloud\Exceptions\XMLParseException
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


    public function getCode()
    {
        return $this->code;
    }

    public function getAnswer()
    {
        return $this->answer;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getMessage()
    {
        return $this->message;
    }

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

    protected function stringToXmlArray(string $str)
    {
        return json_decode(json_encode(simplexml_load_string($str)),1);
    }
}

?>