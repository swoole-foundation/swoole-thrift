<?php
/**
 * @author xialeistudio
 */

namespace swoole\foundation\thrift\client;

use Swoole\Client;
use Thrift\Transport\TTransport;
use Thrift\Exception\TTransportException;

/**
 * Swoole同步阻塞客户端
 * Class SwooleTransport
 * @package thrift\transport
 */
class Transport extends TTransport
{
    /**
     * @var string 连接地址
     */
    protected $host;
    /**
     * @var int 连接端口
     */
    protected $port;

    /**
     * @var Client
     */
    protected $client;

    /**
     * ClientTransport constructor.
     * @param string $host
     * @param int $port
     */
    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
        $this->client = new Client(SWOOLE_SOCK_TCP);
    }


    /**
     * Whether this transport is open.
     *
     * @return boolean true if open
     */
    public function isOpen()
    {
        return $this->client->sock > 0;
    }

    /**
     * Open the transport for reading/writing
     *
     * @throws TTransportException if cannot open
     */
    public function open()
    {
        if ($this->isOpen()) {
            throw new TTransportException('ClientTransport already open.', TTransportException::ALREADY_OPEN);
        }
        if (!$this->client->connect($this->host, $this->port)) {
            throw new TTransportException('ClientTransport could not open:' . $this->client->errCode,
                TTransportException::UNKNOWN);
        }
    }

    /**
     * Close the transport.
     * @throws TTransportException
     */
    public function close()
    {
        if (!$this->isOpen()) {
            throw new TTransportException('ClientTransport not open.', TTransportException::NOT_OPEN);
        }
        $this->client->close();
    }

    /**
     * Read some data into the array.
     *
     * @param int $len How much to read
     * @return string The data that has been read
     * @throws TTransportException if cannot read any more data
     */
    public function read($len)
    {
        if (!$this->isOpen()) {
            throw new TTransportException('ClientTransport not open.', TTransportException::NOT_OPEN);
        }
        return $this->client->recv($len, true);
    }

    /**
     * Writes the given data out.
     *
     * @param string $buf The data to write
     * @throws TTransportException if writing fails
     */
    public function write($buf)
    {
        if (!$this->isOpen()) {
            throw new TTransportException('ClientTransport not open.', TTransportException::NOT_OPEN);
        }
        $this->client->send($buf);
    }
}