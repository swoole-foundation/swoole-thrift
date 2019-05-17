<?php
/**
 * @author xialeistudio
 */

namespace swoole\foundation\thrift\server;

use Swoole\Server;
use Thrift\Exception\TTransportException;
use Thrift\Server\TServer;

/**
 * 服务器
 * Class SwooleServer
 * @package app\swoole\server
 * @property SwooleServerTransport $transport_
 */
class  SwooleServer extends TServer
{
    /**
     * 监听事件(receive为保留事件，不可以监听)
     * @param string $event
     * @param callable $callable
     */
    public function on($event, callable $callable)
    {
        if ($event == 'receive') {
            return;
        }
        $this->transport_->server->on($event, $callable);
    }

    /**
     * Serves the server. This should never return
     * unless a problem permits it to do so or it
     * is interrupted intentionally
     *
     * @return void
     * @throws TTransportException
     */
    public function serve()
    {
        $this->transport_->server->on('receive', [$this, 'handleReceive']);
        $this->transport_->listen();
    }

    /**
     * Stops the server serving
     *
     * @return void
     */
    public function stop()
    {
        $this->transport_->close();
    }

    /**
     * 处理RPC请求
     * @param Server $server
     * @param int $fd
     * @param int $fromId
     * @param string $data
     */
    public function handleReceive(Server $server, $fd, $fromId, $data)
    {
        $transport = new SwooleTransport($server, $fd, $data);
        $inputTransport = $this->inputTransportFactory_->getTransport($transport);
        $outputTransport = $this->outputTransportFactory_->getTransport($transport);
        $inputProtocol = $this->inputProtocolFactory_->getProtocol($inputTransport);
        $outputProtocol = $this->outputProtocolFactory_->getProtocol($outputTransport);
        $this->processor_->process($inputProtocol, $outputProtocol);
    }
}