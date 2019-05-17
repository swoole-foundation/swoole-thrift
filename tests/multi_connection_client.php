<?php
/**
 * thrift客户端，共用一个连接
 * @author xialeistudio
 * @date 2019-05-16
 */

use SwooleThrift\Client\Transport;
use tests\services\SumService\SumServiceClient;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TFramedTransport;

require __DIR__ . '/../vendor/autoload.php';


$max = $min = $count = $total = 0;

for ($i = 0; $i < 10000; $i++) {
    $transport = new Transport('localhost', 9501);
    $transport = new TFramedTransport($transport);
    $protocol = new TBinaryProtocol($transport);
    $client = new SumServiceClient($protocol);
    $transport->open();

    $start = microtime(true);
    $client->sum(1, 1);
    $duration = microtime(true) - $start;
    $max = max($duration, $max);
    $min = $min === 0 ? $duration : min($duration, $min);
    $count++;
    $total += $duration;
}

printf("max: %fs\nmin: %fs\navg: %fs\ncall count: %d\ntotal time: %fs\n", $max, $min, $total / $count, $count, $total);