<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 3/13/17
 * Time: 4:12 PM
 */

namespace CreativeDelta\User\Core\Domain\Entity;


class SessionLog
{
    protected $id;
    protected $datetime;
    protected $previousHash;
    protected $returnUrl;
    protected $dataJson;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @param mixed $datetime
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    }

    /**
     * @return mixed
     */
    public function getPreviousHash()
    {
        return $this->previousHash;
    }

    /**
     * @param mixed $previousHash
     */
    public function setPreviousHash($previousHash)
    {
        $this->previousHash = $previousHash;
    }

    /**
     * @return mixed
     */
    public function getReturnUrl()
    {
        return $this->returnUrl;
    }

    /**
     * @param mixed $returnUrl
     */
    public function setReturnUrl($returnUrl)
    {
        $this->returnUrl = $returnUrl;
    }

    /**
     * @return mixed
     */
    public function getDataJson()
    {
        return $this->dataJson;
    }

    /**
     * @param mixed $dataJson
     */
    public function setDataJson($dataJson)
    {
        $this->dataJson = $dataJson;
    }


}