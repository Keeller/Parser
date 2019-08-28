<?php

$b=['name'=>'template_list',
    'pattern'=>['div[class="content_article"]','header'=>'p[class="green_text title_2"]'],

];

$c=['name'=>'template_detail',
    'pattern'=>['div[class="content_article"]','header'=>'p[class="green_text title_2"]','date'=>''],

];
$d=[

    'url'=>"http://www.rambler.ru/"
];
$hy=[
    'kwords'=>['бизнес'],
];
$url=[
    'url'=>'http://rambler.ru/',


];

$hv=['kwords'=>['новость']];
$d1=[

    'url'=>"http://yandex.ru/"
];
$url1=[
    'url'=>'http://yandex.ru./'
];
$c1=['name'=>'template_detail',
    'pattern'=>['div[class="content_article"]','header'=>'p[class="green_text title_2"]','date'=>''],

];
$b1=['name'=>'template_list',
    'pattern'=>['div[class="content_article"]','header'=>'p[class="green_text title_2"]'],

];

$res1=[
    [
        'site'=>$d,
        'keys'=>$hy,
        'list'=>$url,
        'view'=>$b,
        'detail'=>$c],
    [
        'site'=>$d1,
        'keys'=>$hv,
        'list'=>$url1,
        'view'=>$b1,
        'detail'=>$c1],

];


file_put_contents('createConfig.json',json_encode($res1));