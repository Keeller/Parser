<?php



//require_once "C:\\ops\\OSPanel\domains\Parser\\vendor\\electrolinux\\phpquery\\phpQuery\\phpQuery.php";
require_once "C:\\ops\\OSPanel\\domains\\Parser\\vendor\\autoload.php";
use ParserCore\Core;

//$result=file_get_contents('http://yandex.ru/');
//echo $result;

//$fileEndEnd = mb_convert_encoding($result,'UTF-8');

/*
$pq=phpQuery::newDocument($result);
$elem=$pq->find('[class="rows-wrapper"]');
echo $elem->htmlOuter();
*/

//echo Core::createConfig($a);
$core=new Core();
//$core->setBaseUrl('http://yandex.ru/');
//$core->getDetail();
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

