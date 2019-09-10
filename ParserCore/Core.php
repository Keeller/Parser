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
    protected $parsedUrls;
    protected $currentList;
    protected $currentAnons;
    protected $currentSite;
    protected $currentDetail;
    protected $currentView;
    protected $currentPublishDate;
    protected $currentHeader;
    private static $timefLiveDate=25;
    protected $errLoger;
    protected $countOfReconect=0;
    protected $durationTime=500;

    public function __construct($errLogFileName){

        $this->errLoger=new ErrorLoger($errLogFileName);
        try {
            DbManager::connect();
        }
        catch (\Exception $ex){
            $this->errLoger->logError($ex->getMessage(),__METHOD__);
            die('Fatal Error database connect not found');
        }

    }

    protected function getList($url)
    {


        $result=R::findOne('url_list','url=?',[$url]);
        if(empty($result))
            $this->errLoger->logError('url of List Not Found',__METHOD__,$this->currentSite->getProperties()['base_url']);


        $this->currentList=$result;
        $this->htmlListString=$this->query($url);
    }

    protected function validateUrl($url){
            $content=parse_url($url);
            if(!empty($content['host'])){
                if(filter_var($url,FILTER_VALIDATE_URL))
                    return $url;
                else{
                    $this->errLoger->logError('cant validate url'.$url,__METHOD__);
                    return false;
                }


            }
            else
                return $this->formFullPath($url);


    }


    protected  function query($url){

        $url=$this->validateUrl($url);
        $curl=curl_init();
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_URL,$url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)');
        $result=curl_exec($curl);




        if ($result === false) {

            if($this->countOfReconect<3){
                $this->countOfReconect++;
                usleep($this->durationTime);
                static::query($url);
            }
            else {
                $this->errLoger->logError("Ошибка CURL: " . curl_error($curl), __METHOD__, $this->currentSite->getProperties()['base_url'] . $url);
                $this->countOfReconect=0;
                return false;
            }
        } else {
            $this->countOfReconect=0;
            usleep($this->durationTime);
            return $result;
        }

    }


    protected static function dateDiff($q){

        if(empty($q))
            return false;
        $d=new \DateTime();

        return (($d->diff(new \DateTime($q->getProperties()['date_insert'])))->format("%d")>static::$timefLiveDate)?
                    true:
                    false;

    }


    protected function parseUrls($parsePattern)
    {

        if(!empty($this->htmlListString)) {
            $pq = \phpQuery::newDocument($this->htmlListString);

            if(!empty($parsePattern))
            {
                $array=[];
                    $elem = $pq->find($parsePattern);

                    if(pq($elem)->length==0)
                        $this->errLoger->logWarning("On View template found nothing this is suspiciously: " , __METHOD__, $this->currentList->getProperties()['url']);

                    foreach ($elem as $value) {

                        $result = pq($value)->find('a')->attrs('href');
                        $result=array_unique($result);
                        $this->currentAnons=pq($value)->htmlOuter();


                        foreach ($result as $res) {
                            $query=R::findOne('content','url_detail=?',[$this->getDetailUrl($res)]);

                            if(empty($query)) {

                                $this->parseDetail($res);

                            }

                        }
                        //array_push($array, $result);
                    }

            }
        }
    }

    protected function save($detail,$detailUrl,array $attrs=[]){

        $newContent=R::dispense('content');
        $newContent->setAttr('content',$detail);
        $newContent->setAttr('site_id',$this->currentSite['id']);
        $newContent->setAttr('url_detail',$detailUrl);
        $newContent->setAttr('anons',$this->currentAnons);

        $arg=R::store($newContent);

       if(is_integer($arg)){

           if(count($attrs)>0){
               foreach ($attrs as $name=>$attr){
                   $newAttr=R::dispense('attrs');
                   $newAttr->setAttr('name',$name);
                   $newAttr->setAttr('value',$attr);
                   $newAttr->setAttr('content_id',$arg);
                   $ans=R::store($newAttr);
                   if(!is_integer($ans)){
                       $this->errLoger->logError('cant save atrrs',__METHOD__);
                       return false;
                   }

               }
           }
           else{
               $this->errLoger->logWarning('parse pattern not found',__METHOD__);
               return false;
           }

       }

    }


    protected function getDetailUrl($url){
        $content=parse_url($url);
        $result=$content['path'];
        if(!empty($content['query']))
            $result.='?'.$content['query'];

        return $result;
    }


    protected function formFullPath($url){
        return 'http://'.$this->currentSite->getProperties()['base_url'].$url;

    }

    public function parseDetail($url){


        if(empty($url))
            die('Empty Url');
        $baseUrl=$this->getDetailUrl($url);


            if(!empty($this->currentDetail)){

                $result=$this->query($url);
                $pq=\phpQuery::newDocument($result);
                $parseResult=[];


                foreach ($this->currentDetail as $value) {



                    $patterns=json_decode($value->getProperties()['pattern'],true);
                        if(!empty($patterns['main'])) {
                            $el = $pq->find($patterns['main']);
                            $maintemp=pq($el)->htmlOuter();
                            unset($patterns['main']);


                            if (pq($el)->length == 0) {
                                $this->errLoger->logWarning("On main Detail template found nothing this is suspiciously: ", __METHOD__, $this->formFullPath($url));
                                return;
                            } else {

                                if($this->checkKeys($maintemp)) {

                                    foreach ($patterns as $name => $pattern) {
                                        $parseResult[$name] = pq($el)->find($pattern)->html();


                                    }

                                    $this->save($maintemp,$baseUrl,$parseResult);
                                }

                            }
                        }
                        else{
                            $this->errLoger->logError("Main patterns not stated", __METHOD__, $this->formFullPath($url));

                        }





                }
            }
            else
                $this->errLoger->logError('Empty detail',__METHOD__,$this->currentSite->getProperties()['base_url']);



    }

    /*
    protected function parseSpecialPatterns(&$patterns,&$document){


        if(isset($patterns->date)) {

            $this->currentPublishDate = implode($document->find($patterns->date)->getString());

            if(strlen($this->currentPublishDate)>255 || strlen($this->currentPublishDate)==0) {
                $this->currentPublishDate=null;
            }
            unset($patterns->date);
        }



        if(isset($patterns->header)) {
            $this->currentHeader = implode($document->find($patterns->header)->getString());
            unset($patterns->header);

        }

    }
    */

    protected function getView(){


        $View=R::load('url_list',$this->currentList['id']);
        $this->currentView=$View->ownTemplateListList;

        if(empty($this->currentView))
            $this->errLoger->logError('Empty View',__METHOD__,$this->currentSite->getProperties()['base_url']);


    }

    protected function getDetail(){

        $urlList=R::load('url_list',$this->currentList['id']);
        $this->currentDetail=$urlList->ownTemplateDetailList;

        if(empty($this->currentDetail)) {
            $this->errLoger->logError('Empty Detail', __METHOD__,$this->currentSite->getProperties()['base_url']);

        }

    }

    protected function checkKeys(&$fragments){

        if(!empty($fragments)) {

            if(!empty($this->currentSite)) {

                $site=R::load('site',$this->currentSite['id']);
                $kwords=$site->ownKeywordsList;


                $result = [];

                    foreach ($kwords as $k) {

                        foreach (json_decode($k->getProperties()['keywords']) as $kword) {

                            if (stripos($fragments, $kword) !== false) {

                                return true;
                            }


                        }
                    }


                $this->errLoger->logWarning('In fragment not found keywords this is suspiciously',__METHOD__,$this->currentSite->getProperties()['base_url']);

                return false;

            }
           $this->errLoger->logError('empty current site',__METHOD__,$this->currentSite->getProperties()['base_url']);
        }
        $this->errLoger->logError('empty fragment',__METHOD__,$this->currentSite->getProperties()['base_url']);
    }

    protected function parseSite($site){

        $this->currentSite=$site;
        $siteList=R::load('site',$site->getId());
        $lentaList=$siteList->ownUrlListList;


        foreach ($lentaList as $value) {


            $this->getList($value->url);
            $this->getView();
            $this->getDetail();





            foreach ($this->currentView as $v) {

                foreach (json_decode($v->getProperties()['pattern']) as $t)
                            $this->parseUrls($t);

            }
        }
    }

    public function run($id=null){


        if(!empty($id)) {
            $user=R::load('users',$id);
            $res=R::findAll('site','user_id=?',[$id]);
            $this->durationTime=$user->getProperties()['duration_time'];
            foreach ($res as $value)
                $this->parseSite($value);
        }
        else{
            $this->errLoger->logError('User id must set',__METHOD__);
        }
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

    /**
     * @return int
     */
    public static function getTimefLiveDate()
    {
        return self::$timefLiveDate;
    }

    /**
     * @param int $timefLiveDate
     */
    public static function setTimefLiveDate($timefLiveDate)
    {
        self::$timefLiveDate = $timefLiveDate;
    }



}