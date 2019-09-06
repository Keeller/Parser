<?php
/**
 * Created by PhpStorm.
 * User: kolya
 * Date: 23.08.2019
 * Time: 19:29
 */

namespace ParserCore;

use RedBeanPHP\R;

class Config
{

    protected $createdSiteId;
    protected $createdUrlListId;


    public function __construct(){

        if(!R::testConnection()) {
            try {
                DbManager::connect();
            } catch (\Exception $ex) {
                die($ex->getMessage());
            }
        }
    }

    protected function createUrlList(array $params){

        if(!isset($params['url'])||!isset($params['id']))
            die('params not stated');

        $site=R::findOne('site',$params['id']);
        if(!empty($site)) {
            $newUrlList = R::xdispense('url_list');
            $newUrlList->setAttr('url', $params['url']);
            $newUrlList->setAttr('site_id', $params['id']);
            try {
                $res = R::store($newUrlList);
                $this->createdUrlListId=$res;
                return is_integer($res)?true:false;
            }
            catch (\Exception $ex) {

                die($ex->getMessage());

            }

        }
        else{
            die('site not found');

        }
    }

    protected function createSite(array $params){

        if(!isset($params['url'])||!isset($params['user_id']))
            die('params not stated');

        $baseUrl=parse_url($params['url'], PHP_URL_HOST);
        $newSite=R::dispense('site');
        $newSite->setAttr('base_url',$baseUrl);
        $newSite->setAttr('user_id',$params['user_id']);
        try {
            $res = R::store($newSite);
            $this->createdSiteId=$res;
            return is_integer($res)?true:false;
        }
        catch (\Exception $ex){
            die($ex->getMessage());
        }


    }

    protected function createTemplate(array $params){

        if(!isset($params['name'])||!isset($params['pattern'])||!isset($params['url_list_id']))
            die('params not stated');

        $urlList=R::findOne('url_list',$params['url_list_id']);
        if(!empty($urlList)) {
            $tlist = R::xdispense($params['name']);
            $tlist->setAttr('pattern', json_encode($params['pattern']));
            $tlist->setAttr('url_list_id', $params['url_list_id']);
            try {
                $arg = R::store($tlist);
                return is_integer($arg)?true:false;
            }
            catch (\Exception $ex) {
                die($ex->getMessage());
            }
        }
        else{
            echo 'url list not found';
            return false;
        }

    }

    protected function updateUrlList(array $params){

        if(!isset($params['urll_id'])||!isset($params['id'])||!isset($params['id']))
            die('params not stated');


            $newUrlList = R::load('url_list',$params['id']);

            try {
                $newUrlList->setAttr('url', $params['url']);
                $newUrlList->setAttr('site_id', $params['id']);
                $res = R::store($newUrlList);
                return is_integer($res)?true:false;
            }
            catch (\Exception $ex) {

                die($ex->getMessage());

            }



    }

    protected function deleteBySiteId($siteId){

        $site=R::load('site',$siteId);
        if(!empty($site))
            R::trash($site);
        else
            die('id not found');

    }

    protected function deleteContentByDate($mindate,$maxdate){

        $content=R::find('content','DATE(date_insert)>:mindate AND DATE(date_insert)<:maxdate',[':mindate'=>$mindate,':maxdate'=>$maxdate]);

        if(!empty($content))
            R::trashAll($content);
        else
            die('content not found');
    }

    protected function updateSite(array $params){

        if(!isset($params['url'])||!isset($params['id'])||!isset($params['user_id']))
            die('params not stated');


        $baseUrl=parse_url($params['url'], PHP_URL_HOST);
        $newSite=R::load('site',$params['id']);

        try {
            $newSite->setAttr('base_url',$baseUrl);
            $newSite->setAttr('user_id',$params['user_id']);
            $res = R::store($newSite);
            return is_integer($res)?true:false;
        }
        catch (\Exception $ex){
            die($ex->getMessage());
        }


    }

    protected function updateTemplate(array $params){

        //var_dump($params);die();
        if(!isset($params['name'])||!isset($params['pattern'])||!isset($params['id'])||!isset($params['url_list_id']))
            die('params not stated');



            $tlist = R::load($params['name'],$params['id']);
            if(!empty($tlist)) {
                $tlist->setAttr('pattern', json_encode($params['pattern']));
                $tlist->setAttr('url_list_id', $params['url_list_id']);
                try {
                    $arg = R::store($tlist);
                    return is_integer($arg) ? true : false;
                } catch (\Exception $ex) {
                    die($ex->getMessage());
                }
            }
            else{
                echo 'updated el not found';
                return false;
            }

    }

    protected function createKeys(array $params){

        if(!isset($params['kwords'])||!isset($params['site_id']))
            die('params not stated');
        $keys=R::dispense('keywords');
        $keys->setAttr('keywords',json_encode($params['kwords']));
        $keys->setAttr('site_id',$params['site_id']);
        $arg=R::store($keys);
        return (is_integer($arg))?true:false;

    }

    protected function updateKeys(array $params){

        if(!isset($params['kwords'])||!isset($params['site_id'])||!isset($params['id']))
            die('params not stated');
        $keys=R::load('keywords',$params['id']);
        $keys->setAttr('keywords',json_encode($params['kwords']));
        $keys->setAttr('site_id',$params['site_id']);
        $arg=R::store($keys);
        (is_integer($arg))?true:false;

    }


    protected function createAll(array $params){



        if(!isset($params['site'])||!isset($params['list'])||!isset($params['view'])||!isset($params['detail'])||!isset($params['keys']))
            die('params not stated');
        $site=R::findOne('site','base_url=?',[parse_url($params['site']['url'],PHP_URL_HOST)]);

        if(!empty($site))
            R::trash($site);
        if($this->createSite($params['site'])) {
            $params['list']['id']=$params['keys']['site_id']=$this->createdSiteId;
            if ($this->createUrlList($params['list'])) {
                $params['view']['url_list_id']=$params['detail']['url_list_id']=$this->createdUrlListId;
                if ($this->createTemplate($params['view'])) {
                    if ($this->createTemplate($params['detail'])) {
                        if ($this->createKeys($params['keys']))
                            return true;
                        else
                            die('keys create error');
                    }
                    else
                        die('detail create error');
                }
                else
                    die('view create error');
            }
            else
                die('url list create error');


        }
        else
            die('Site Create error');

    }

    /*
    protected function updateAll(array $params){

        if(!isset($params['site'])||!isset($params['list'])||!isset($params['view'])||!isset($params['detail']))
            die('params not stated');
        if($this->createSite($params['site'])) {
            if ($this->createUrlList($params['list'])) {
                if ($this->createTemplate($params['view'])) {
                    if ($this->createTemplate($params['detail']))
                        return true;
                    else
                        die('detail create error');
                }
                else
                    die('view create error');
            }
            else
                die('url list create error');


        }
        else
            die('Site Create error');
    }
    */


    protected function createFromJson(){

        if(file_exists('createConfig.json')) {
            $configArray = file_get_contents('createConfig.json');
            $parsedArray=json_decode($configArray,true);
            return $parsedArray;
        }
    }

    protected function updateFromJson(){

        if(file_exists('updateConfig.json')){
            $configArray = file_get_contents('updateConfig.json');
            $parsedArray=json_decode($configArray,true);
            return $parsedArray;
        }
    }

    public function updateRun($action){
        $this->$action($this->updateFromJson());
    }

    public function createRun($action){

        if($action!='createAll')
            $this->$action($this->createFromJson());
        elseif($action=='createAll'){
            $array=$this->createFromJson();
            foreach ($array as $value)
                $this->createAll($value);
        }
    }

    public function deleteRun($action,$param1,$param2=null){
        if(!empty($param2))
            $this->$action($param1,$param2);
        else
            $this->$action($param1);

    }



}