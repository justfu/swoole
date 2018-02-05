<?php
class Server
{
    private $serv;
    public function __construct() {
        $this->serv = new swoole_server("127.0.0.1", 9501);
        $this->serv->set(array(
            'worker_num' => 1,   //一般设置为服务器CPU数的1-4倍
            'daemonize' => 1,  //以守护进程执行
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'task_worker_num' => 1,  //task进程的数量
            "task_ipc_mode " => 3 ,  //使用消息队列通信，并设置为争抢模式
            "log_file" => "aa.log" ,//日志
        ));
        $this->serv->on('Receive', array($this, 'onReceive'));
        // bind callback
        $this->serv->on('Task', array($this, 'onTask'));
        $this->serv->on('Finish', array($this, 'onFinish'));
        $this->serv->start();
    }
    public function onReceive( swoole_server $serv, $fd, $from_id, $data ) {
        $serv->task( $data );
    }
    public function onTask($serv,$task_id,$from_id, $data) {
        shell_exec($data);
    }
    public function onFinish($serv,$task_id, $data) {
    }
}
// 调用
$server = new Server();