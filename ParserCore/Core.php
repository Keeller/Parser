<?php
/**
 * Created by PhpStorm.
 * User: kolya
 * Date: 13.08.2019
 * Time: 19:22
 */

namespace ParserCore;


use RedBeanPHP\R;

class Core
{
    protected $htmlListString;
    /*
    protected $baseUrl;
    protected $currentConfig;
    */
    protected $parsedUrls;
    protected $currentList;
    protected $currentAnons;
    protected $currentSite;
    protected $currentDetail;
    protected $currentView;
    protected $currentPublishDate;

    public function __construct(){

        DbManager::connect();
    }

    protected function getList($url)
    {


        $result=R::findOne('url_list','url=?',[$url]);
        if(empty($result))
            die("url not found");

        $this->currentList=$result;
        $this->htmlListString=static::query($url);

        return (empty($this->htmlString)||empty($this->currentSite))?static::formAnswer(false,'cant get content'):static::formAnswer(true,"OK");
    }


    protected static function query($url){

        $curl=curl_init();
        curl_setopt($curl, CURLOPT_URL,$url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)');
        $result=curl_exec($curl);

        if ($result === false) {
            echo "Ошибка CURL: " . curl_error($curl);
            return false;
        } else {
            return $result;
        }

    }


    protected function parseAnons($parsePattern){

        if(!empty($this->htmlListString)) {
            $pq = \phpQuery::newDocument($this->htmlListString);
            if(!empty($parsePattern))
            {
                $elem = $pq->find($parsePattern);
                $this->currentAnons=$elem->htmlOuter();
                return $this->currentAnons;
            }
            die('empty parse pattern');
        }
        die('empty html string');
    }

    public function parseUrls( $parsePattern)
    {
        if(!empty($this->htmlListString)) {
            $pq = \phpQuery::newDocument($this->htmlListString);
            if(!empty($parsePattern))
            {
                $array=[];
                    $elem = $pq->find($parsePattern);
                    foreach ($elem as $value) {
                        $result = pq($value)->find('a')->attrs('href');
                        $result=array_unique($result);
                        array_push($array, $result);
                    }


                $this->parsedUrls=$array;
                return (empty($this->parsedText))?static::formAnswer(false,"result empty"):static::formAnswer(true,"OK");
            }
        }
    }

    protected function parseDetail($url){

        if(empty($url))
            die('Empty Url');

            if(!empty($this->currentDetail)){

                $result=static::query($url);
                $pq=\phpQuery::newDocument($result);
                $parseResult=[];

                foreach ($this->currentDetail as $value) {
                    foreach (json_decode($value->getProperties()['pattern']) as $v) {
                        $el = $pq->find($v)->htmlOuter();
                        array_push($parseResult, $el);
                    }

                }

                return $parseResult;

            }
            die('Empty Detail');


    }

    public function getView(){

        $View=R::load('url_list',$this->currentList['id']);
        $this->currentView=$View->ownTemplateListlList;


    }

    public function getDetail(){

        $urlList=R::load('url_list',$this->currentList['id']);
        $this->currentDetail=$urlList->ownTemplateDetailList;
        //var_dump($this->currentDetail[1]->getProperties());die();

    }

    protected function checkKeys($fragments){

        if(!empty($fragments)) {

            if(!empty($this->currentSite)) {

                $site=R::load('site',$this->currentSite['id']);
                $kwords=$site->ownKeywordsList;
                $result = [];
                foreach ($fragments as $fragment) {

                    foreach (json_decode($kwords->getProperties()['keywords']) as $kword) {

                        if(stripos($fragment,$kword)!==false)
                           return true;


                    }

                }

                return false;

            }
            die('empty current site');
        }
        die('empty fragment');

    }

    protected function parseSite($siteUrl){



    }

    public function run()
    {



    }

    protected static function formAnswer($status,$message)
    {
        return [
            "status"=>$status,
            "message"=>$message
        ];

    }

    /**
     * @return mixed
     */
    public function getHtmlString()
    {
        return $this->htmlListString;
    }

    /**
     * @param mixed $htmlString
     */
    public function setHtmlString($htmlString)
    {
        $this->htmlListString = $htmlString;
    }










}