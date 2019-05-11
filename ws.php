<?php

try {

    $address = 0;
    $port = 8090;

    // Create WebSocket.
    echo "creating socket\n";
    $server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    echo "socket option\n";
    socket_set_option($server, SOL_SOCKET, SO_REUSEADDR, 1);
    echo "socket binding\n";
    socket_bind($server, $address, $port);
    echo "socket listen to $port\n";
    socket_listen($server);
    
    $client = socket_accept($server);
    // Send WebSocket handshake headers.
    $request = socket_read($client, 5000);
    preg_match('#Sec-WebSocket-Key: (.*)\r\n#', $request, $matches);
    $key = base64_encode(pack(
        'H*',
        sha1($matches[1] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')
    ));
    $headers = "HTTP/1.1 101 Switching Protocols\r\n";
    $headers .= "Upgrade: websocket\r\n";
    $headers .= "Connection: Upgrade\r\n";
    $headers .= "Sec-WebSocket-Version: 13\r\n";
    $headers .= "Sec-WebSocket-Accept: $key\r\n\r\n";
    socket_write($client, $headers, strlen($headers));

    // Send messages into WebSocket in a loop.
    while (true) {
        sleep(1);
        $content = 'Now: ' . time();
        $response = chr(129) . chr(strlen($content)) . $content;
        socket_write($client, $response);
    }

} catch (Exception $e) {
    echo 'Error on ws'.$e;
}

?>