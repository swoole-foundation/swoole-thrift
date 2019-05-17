# swoole-thrift
基于基于swoole协程的thrift服务端。

基于thrift标准接口编程，使用方法与原生thrift一致。

## version

+ swoole 4.x
+ thrift 0.12

## 使用方法

```bash
composer require swoole-foundation/swoole-thrift
```

## 示例代码

+ Server
    
    ```php
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
    
    require __DIR__ . '/vendor/autoload.php';
    
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
    ```
 
 + Client
    
    ```php
    <?php
    /**
     * thrift客户端，共用一个连接
     * @author xialeistudio
     * @date 2019-05-16
     */
    
    use swoole\foundation\thrift\client\Transport;
    use tests\services\SumService\SumServiceClient;
    use Thrift\Protocol\TBinaryProtocol;
    use Thrift\Transport\TFramedTransport;
    
    require __DIR__ . '/../vendor/autoload.php';
    
    $transport = new Transport('localhost', 9501);
    $transport = new TFramedTransport($transport);
    $protocol = new TBinaryProtocol($transport);
    
    $client = new SumServiceClient($protocol);
    
    $transport->open();
    
    $max = $min = $count = $total = 0;
    
    for ($i = 0; $i < 10000; $i++) {
        $start = microtime(true);
        $client->sum(1, 1);
        $duration = microtime(true) - $start;
        $max = max($duration, $max);
        $min = $min === 0 ? $duration : min($duration, $min);
        $count++;
        $total += $duration;
    }
    
    printf("max: %fs\nmin: %fs\navg: %fs\ncall count: %d\ntotal time: %fs\n", $max, $min, $total / $count, $count, $total);
    ```
    
## 压力测试
 
+ 4核i5
+ 8G内存

 ```text
max: 0.001053s
min: 0.000084s
avg: 0.000094s
call count: 10000
total time: 0.936655s
```