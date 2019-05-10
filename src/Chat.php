<?php
namespace Wsrt;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {

    protected $clients;
    
    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    /**
     * Called upon opening new connection
     *
     * @param ConnectionInterface   $conn connection interface
     * 
     * @author Gema Ulama Putra <gemul.putra@gmail.com>
     */
    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    /**
     * Called when receiving a message
     *
     * @param ConnectionInterface   $from connection interface
     * @param String   $msg message
     * 
     * @author Gema Ulama Putra <gemul.putra@gmail.com>
     */
    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                $client->send($msg);
            }
        }
    }

    /**
     * Called upon closing connection
     *
     * @param ConnectionInterface   $conn connection interface
     * 
     * @author Gema Ulama Putra <gemul.putra@gmail.com>
     */
    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    /**
     * Called when error occured
     *
     * @param ConnectionInterface   $conn connection interface
     * @param \Exception   $e exception
     * 
     * @author Gema Ulama Putra <gemul.putra@gmail.com>
     */
    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}