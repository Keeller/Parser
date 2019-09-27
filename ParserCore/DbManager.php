<?php
/**
 * Created by PhpStorm.
 * User: kolya
 * Date: 17.08.2019
 * Time: 15:57
 */

namespace ParserCore;
use \RedBeanPHP\R;

class DbManager
{


    private function __construct(){

    }


    public static function connect(){

        R::setup( 'mysql:host=127.0.0.1;dbname=parser_db','parser_user', 'zug7019', false);
        if(!R::testConnection())
            die('No db Connection');
        try {
            R::ext('xdispense', function ($type) {
                return R::getRedBean()->dispense($type);
            });
        }
        catch (\Exception $ex)
        {
            die($ex->getMessage());
        }



    }




}