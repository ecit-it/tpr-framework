<?php
// +----------------------------------------------------------------------
// | TPR [ Design For Api Develop ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2017 http://hanxv.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: axios <axioscros@aliyun.com>
// +----------------------------------------------------------------------

namespace tpr\framework\behavior;

use tpr\framework\Request;

/**
 * Class LogWriteDone
 * @package axios\tpr\behavior
 * need library/think/Log.php 161
 *  ->   Hook::listen('log_write_done', $log);
 */
class LogWriteDone
{
    public $param;
    public $request;

    function __construct()
    {
        $this->request = Request::instance();
        $this->param   = $this->request->param();
    }

    public function run()
    {

    }
}