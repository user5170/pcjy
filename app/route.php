<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    '__pattern__' => [
        'name' => '\w+',
        'id' => '\d+',
        'catId' => '\d+',
        'my' => '\d+',
    ],
    '[hello]'     => [
        ':id'   => ['apphome/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['apphome/hello', ['method' => 'post']],
    ],
    '' => 'apphome/Welcome/index', //首页
    'location' => 'apphome/location/index',
    'index' => 'apphome/index/index',
    'item' => 'apphome/Warehouse/index',
    //'apphome/warehouse/index' => 'apphome/Warehouse/index',
    //'item/:catId' => 'apphome/warehouse/detail',
    'claim' => 'apphome/Warehouse/claim',
    'taken' => 'apphome/Warehouse/taken',
    'login' => 'appuser/login/index',
    'reg' => 'appuser/login/reg',
    'user' => 'appuser/index/index',
    'put' => 'appuser/put/index',
    'publish' => 'appuser/put/publish',
    'withdraw' => 'appuser/money/withdraw',
    'record' => 'appuser/money/record',
    'rank' => 'apphome/rank/index',
    'clock' => 'apphome/clock/index',
    'guide' => 'apphome/guide/index',
    'setting' => 'appuser/index/setting',
    // 'vxlogin' => 'vx/index/login',
    // 'vx' => 'vx/index/index',
];
