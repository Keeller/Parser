<?php
/**
 * Created by PhpStorm.
 * User: kolya
 * Date: 13.08.2019
 * Time: 18:04
 */

namespace ParserCore;


class Parcelable implements \JsonSerializable
{


    protected  $baseURL;
    protected  $templates=[


    ];
    protected static $saveDir='C:\\ops\\OSPanel\\domains\\Parser\\ParserCore\\Config\\';
    protected $keyWords=[];

    public function jsonSerialize()
    {
        return json_encode( [
            "baseURL"=>$this->baseURL,
            "templates"=>$this->templates,
            "keyWords"=>$this->keyWords

            ]);
    }


    /**
     * @return mixed
     */
    public function getBaseURL()
    {
        return $this->baseURL;
    }

    /**
     * @param mixed $baseURL
     */
    public function setBaseURL($baseURL)
    {
        $this->baseURL = $baseURL;
    }

    /**
     * @return array
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * @param array $templates
     */
    public function setTemplates($templates)
    {
        $this->templates = $templates;
    }

    /**
     * @return string
     */
    public static function getSaveDir()
    {
        return self::$saveDir;
    }

    /**
     * @param string $saveDir
     */
    public static function setSaveDir($saveDir)
    {
        self::$saveDir = $saveDir;
    }

    /**
     * @return array
     */
    public function getKeyWords()
    {
        return $this->keyWords;
    }

    /**
     * @param array $keyWords
     */
    public function setKeyWords($keyWords)
    {
        $this->keyWords = $keyWords;
    }

    public function jsonPutFile()
    {
        //var_dump($this->jsonSerialize());die();
        return file_put_contents(static::$saveDir.md5($this->baseURL).'.json',$this->jsonSerialize());
    }


}