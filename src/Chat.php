<?php
namespace ThingsChat;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use ThingsChat\Entry;

/**
 * Chat class
 * 
 * @author Gema Ulama Putra <gemul.putra@email.com>
 * 
 */
class Chat implements MessageComponentInterface {

    /**
     * clients array
     *
     * @var Array
     */
    protected $clients;

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    /**
     * Called upon opening new connection
     *
     * @param ConnectionInterface   $conn connection interface
     * 
     * @return void
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
     * @return void
     */
    public function onMessage(ConnectionInterface $from, $msg) {
        $clientCount = count($this->clients);
        echo sprintf( 'Client %d from %d connections sending message "%s". Forwarded to Entry Point Receiver' . "\n"
            , $from->resourceId, $clientCount, $msg);

        $entryPoint = new Entry();
        $response = $entryPoint->Receiver($msg);

        if($entryPoint->transmitMode=="single"){
            //this client
            $from->send($response);
        }elseif($entryPoint->transmitMode == "broadcast") {
            //all connected client
            foreach ($this->clients as $client) {
                $client->send($response);
            }
        }elseif($entryPoint->transmitMode == "other") {
            //all except this client
            foreach ($this->clients as $client) {
                if ($from !== $client) {
                    $client->send($response);
                }
            }
        }

    }

    /**
     * Called upon closing connection
     *
     * @param ConnectionInterface   $conn connection interface
     * 
     * @return void
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
     * @return void
     */
    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}