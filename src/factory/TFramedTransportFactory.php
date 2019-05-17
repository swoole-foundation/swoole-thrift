<?php
/**
 * @author xialeistudio
 */

namespace swoole\foundation\thrift\factory;


use Thrift\Factory\TTransportFactory;
use Thrift\Transport\TFramedTransport;
use Thrift\Transport\TTransport;

class TFramedTransportFactory extends TTransportFactory
{
    public static function getTransport(TTransport $transport)
    {
        return new TFramedTransport($transport);
    }
}