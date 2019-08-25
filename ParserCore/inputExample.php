<?php

$b=['name'=>'template_list',
    'pattern'=>['div[class="content_article"]','header'=>'p[class="green_text title_2"]'],
    'url_list_id'=>4,
];

$c=['name'=>'template_detail',
    'pattern'=>['div[class="content_article"]','header'=>'p[class="green_text title_2"]'],
    'url_list_id'=>3,
    'id'=>3
];
$d=[

    'url'=>"https://yandex.ru/"
];
$hy=[
    'kwords'=>['бизнес'],
    'id'=>1
];
$url=[
    'url'=>'https://yandex.ru/',
    'id'=>3

];

file_put_contents('updateConfig.json',json_encode($c));