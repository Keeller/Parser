<?php



//require_once "C:\\ops\\OSPanel\domains\Parser\\vendor\\electrolinux\\phpquery\\phpQuery\\phpQuery.php";
require_once "C:\\ops\\OSPanel\\domains\\Parser\\vendor\\autoload.php";
use ParserCore\Core;


/*
$b=['name'=>'template_detail',
    'pattern'=>['div[class="content_article"]','header'=>'p[class="green_text title_2"]'],
    'url_list_id'=>4,
    'id'=>6
    ];

$c=['name'=>'template_detail',
    'pattern'=>['div[class="content_article"]','header'=>'p[class="green_text title_2"]'],
    'url_list_id'=>3,
];
$d=[
    'id'=>2,
    'url'=>"https://yandex.ru/"
];
file_put_contents('updateConfig.json',json_encode($b));

die();
*/
$huy=[
    'kwords'=>['бизнес'],
    'id'=>1
];
$core=new Core("log.txt");
$core->createKeys($huy);
$config=new \ParserCore\Config();

$core->run();
die();



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
                $core->$str();
                break;
        }
    }
    else
        $core->$callableAction[1]($callableAction[3]);





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
