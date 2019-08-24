<?php
/**
 * Created by PhpStorm.
 * User: kolya
 * Date: 23.08.2019
 * Time: 14:25
 */

namespace ParserCore;


class ErrorLoger
{
    protected $LogFName;

    /**
     * @return mixed
     */
    public function getLogFName()
    {
        return $this->LogFName;
    }

    /**
     * @param mixed $LogFName
     */
    public function setLogFName($LogFName)
    {
        $this->LogFName = $LogFName;
    }

    public function __construct($fName){
        $this->LogFName=$fName;
    }

    public function logError($errMsg,$Method,$currentSite=''){

        file_put_contents($this->LogFName,'Error: '.$errMsg.' in method '.$Method.'. For site '.$currentSite.PHP_EOL,FILE_APPEND);

    }

    public function logWarning($warMsg,$Method,$currentSite=''){

        file_put_contents($this->LogFName,'Warning: '.$warMsg.' in method '.$Method.'. For site '.$currentSite.PHP_EOL,FILE_APPEND);

    }


}