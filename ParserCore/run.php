<?php



//require_once "C:\\ops\\OSPanel\domains\Parser\\vendor\\electrolinux\\phpquery\\phpQuery\\phpQuery.php";
require_once "C:\\ops\\OSPanel\\domains\\Parser\\vendor\\autoload.php";
use ParserCore\Core;





$core=new Core("log.txt");

$config=new \ParserCore\Config();


try {
    $callableAction = explode('/', $argv[1]);

    if(!isset($callableAction[3])) {
        switch ((string)$callableAction[0]) {
            case 'Config':
                if ($callableAction[1] == 'update')
                    $config->updateRun($callableAction[2]);
                elseif ($callableAction[1] == 'create')
                    $config->createRun($callableAction[2]);
                break;

            case 'Core':
                $str=$callableAction[1];
                if(isset($callableAction[2]))
                    $core->$str($callableAction[2]);
                else
                    $core->$str();
                break;

            default:
                die('Error in command syntax');
                break;
        }
    }
    else {

        switch ((string)$callableAction[0]) {
            case 'Config':
                if ($callableAction[1] == 'delete')
                    if(!isset($callableAction[4]))
                        $config->deleteRun($callableAction[2], $callableAction[3]);
                    else
                        $config->deleteRun($callableAction[2], $callableAction[3],$callableAction[4]);
                break;

            default:
                die('Error in command syntax');
                break;
        }

    }




}
catch (Exception $ex)
{
    echo $ex->getMessage();
}

/*
$a=['name'=>'template_list',
    'pattern'=>['div[class="col-3 elem_list_rubrika bordered_bottom"]'],
    'id'=>2
    ];
$b=['name'=>'template_detail',
    'pattern'=>['div[class="content_article"]','date'=>'some date pattern'],
    'id'=>3
];
$kw=[
'kwords'=>['банка','банк','банки','банковский'],
    'id'=>1
];


$core->run();
*/
