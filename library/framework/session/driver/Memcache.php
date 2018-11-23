<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2017 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace tpr\framework\session\driver;

use SessionHandler;
use tpr\framework\Exception;

class Memcache extends SessionHandler
{
    /**
     * @var \Memcache
     */
    protected $handler = null;
    protected $config  = [
        'host'         => '127.0.0.1', // memcache主机
        'port'         => 11211, // memcache端口
        'expire'       => 3600, // session有效期
        'timeout'      => 0, // 连接超时时间（单位：毫秒）
        'persistent'   => true, // 长连接
        'session_name' => '', // memcache key前缀
    ];

    public function __construct($config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 打开Session
     * @access public
     *
     * @param string $savePath
     * @param string $sessionName
     *
     * @return bool
     * @throws Exception
     */
    public function open($savePath, $sessionName)
    {
        // 检测php环境
        if (!extension_loaded('memcache')) {
            throw new Exception('not support:memcache');
        }
        $this->handler = new \Memcache;
        // 支持集群
        $hosts = explode(',', $this->config['host']);
        $ports = explode(',', $this->config['port']);
        if (empty($ports[0])) {
            $ports[0] = 11211;
        }
        // 建立连接
        foreach ((array)$hosts as $i => $host) {
            $port = isset($ports[$i]) ? $ports[$i] : $ports[0];
            $this->config['timeout'] > 0 ?
                $this->handler->addServer($host, $port, $this->config['persistent'], 1, $this->config['timeout']) :
                $this->handler->addServer($host, $port, $this->config['persistent'], 1);
        }
        return true;
    }

    /**
     * 关闭Session
     * @access public
     */
    public function close()
    {
        $this->gc(ini_get('session.gc_maxlifetime'));
        $this->handler->close();
        $this->handler = null;
        return true;
    }

    /**
     * 读取Session
     * @access public
     *
     * @param string $sessID
     *
     * @return string
     */
    public function read($sessID)
    {
        return (string)$this->handler->get($this->config['session_name'] . $sessID);
    }

    /**
     * 写入Session
     * @access public
     *
     * @param string $sessionID
     * @param String $sessionData
     *
     * @return bool
     */
    public function write($sessionID, $sessionData)
    {
        return $this->handler->set($this->config['session_name'] . $sessionID, $sessionData, 0, $this->config['expire']);
    }

    /**
     * 删除Session
     * @access public
     *
     * @param string $sessID
     *
     * @return bool
     */
    public function destroy($sessID)
    {
        return $this->handler->delete($this->config['session_name'] . $sessID);
    }

    /**
     * Session 垃圾回收
     * @access public
     *
     * @param string $sessMaxLifeTime
     *
     * @return true
     */
    public function gc($sessMaxLifeTime)
    {
        return true;
    }
}
