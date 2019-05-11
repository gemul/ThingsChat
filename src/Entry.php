<?php
namespace ThingsChat;

/**
 * Entry point from socket connections
 * 
 * @author Gema Ulama Putra <gemul.putra@email.com>
 */
class Entry
{
    /**
     * Transmit mode
     *
     * @var String
     */
    public $transmitMode="single";
    /**
     * Receiver Point
     *
     * @param String $message
     * @return String json response
     */
    public function Receiver($message){
        $this->transmitMode="single";
        // placeholder return
        return json_encode(['status'=>1,'message'=>"OK"]);
    }
}
