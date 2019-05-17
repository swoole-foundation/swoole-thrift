<?php
/**
 * thrift服务端
 * @author xialeistudio
 * @date 2019-05-16
 */

use swoole\foundation\thrift\factory\TFramedTransportFactory;
use swoole\foundation\thrift\server\SwooleServer;
use swoole\foundation\thrift\server\SwooleServerTransport;
use Swoole\Server;
use tests\handler\SumServiceImpl;
use tests\services\SumService\SumServiceProcessor;
use Thrift\Exception\TTransportException;
use Thrift\Factory\TBinaryProtocolFactory;

require __DIR__ . '/../vendor/autoload.php';

$processor = new SumServiceProcessor(new SumServiceImpl());

$serverTransport = new SwooleServerTransport('localhost');
$transportFactory = new TFramedTransportFactory();
$protocolFactory = new TBinaryProtocolFactory();
$server = new SwooleServer(
    $processor,
    $serverTransport,
    $transportFactory,
    $transportFactory,
    $protocolFactory,
    $protocolFactory
);

try {
    $server->on('start', function (Server $server) {
        printf("Server::serve on %s:%d\n", $server->host, $server->port);
    });
    $server->serve();
} catch (TTransportException $e) {
    printf("Server::serve error: {$e->getMessage()}\n");
}