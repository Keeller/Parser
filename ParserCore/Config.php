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


    public function __construct(){

        if(!R::testConnection()) {
            try {
                DbManager::connect();
            } catch (\Exception $ex) {
                die($ex->getMessage());
            }
        }
    }

    public function createUrlList(array $params){

        if(!isset($params['url'])||!isset($params['id']))
            return false;

        $site=R::findOne('url_list',$params['id']);
        if(!empty($site)) {
            $newUrlList = R::xdispense('url_list');
            $newUrlList->setAttr('url', $params['url']);
            $newUrlList->setAttr('site_id', $params['id']);
            try {
                $res = R::store($newUrlList);
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

    public function createSite(array $params){

        if(!isset($params['url']))
            return false;

        $baseUrl=parse_url($params['url'], PHP_URL_HOST);
        $newSite=R::dispense('site');
        $newSite->setAttr('base_url',$baseUrl);
        try {
            $res = R::store($newSite);
            return is_integer($res)?true:false;
        }
        catch (\Exception $ex){
            die($ex->getMessage());
        }


    }

    public function createTemplate(array $params){

        if(!isset($params['name'])||!isset($params['pattern'])||!isset($params['url_list_id']))
            return false;

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

    public function updateUrlList(array $params){

        if(!isset($params['urll_id'])||!isset($params['id'])||!isset($params['id']))
            return false;


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

    public function updateSite(array $params){

        if(!isset($params['url'])||!isset($params['id']))
            return false;


        $baseUrl=parse_url($params['url'], PHP_URL_HOST);
        $newSite=R::load('site',$params['id']);

        try {
            $newSite->setAttr('base_url',$baseUrl);
            $res = R::store($newSite);
            return is_integer($res)?true:false;
        }
        catch (\Exception $ex){
            die($ex->getMessage());
        }


    }

    public function updateTemplate(array $params){

        //var_dump($params);die();
        if(!isset($params['name'])||!isset($params['pattern'])||!isset($params['id'])||!isset($params['url_list_id']))
            die('update error');



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

    public function createAll(array $params){

        if(!isset($params['site'])||!isset($params['list'])||!isset($params['view'])||!isset($params['detail']))
            return false;
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

    public function updateAll(array $params){

        if(!isset($params['site'])||!isset($params['list'])||!isset($params['view'])||!isset($params['detail']))
            return false;
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

    public function createFromJson(){

        if(file_exists('createConfig.json')) {
            $configArray = file_get_contents('createConfig.json');
            $parsedArray=json_decode($configArray,true);
            return $parsedArray;
        }
    }

    public function updateFromJson(){

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
        $this->$action($this->createFromJson());
    }



}