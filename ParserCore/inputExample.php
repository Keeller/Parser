<?php

$b=['name'=>'template_list',
    'pattern'=>['div[class="content_article"]','header'=>'p[class="green_text title_2"]'],
    'url_list_id'=>4,
];

$c=['name'=>'template_detail',
    'pattern'=>['div[class="content_article"]','header'=>'p[class="green_text title_2"]','date'=>''],
    'url_list_id'=>3,
    'id'=>3
];
$d=[

    'url'=>"http://yandex.ru/"
];
$hy=[
    'kwords'=>['бизнес'],
    'id'=>1
];
$url=[
    'url'=>'http://yandex.ru/',
    'id'=>5

];

file_put_contents('createConfig.json',json_encode($url));