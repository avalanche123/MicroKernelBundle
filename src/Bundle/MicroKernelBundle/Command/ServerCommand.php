<?php

namespace Bundle\MicroKernelBundle\Command;

use Symfony\Components\Console\Command\Command;
use Symfony\Components\Console\Input\InputArgument;
use Symfony\Components\Console\Input\InputInterface;
use Symfony\Components\Console\Output\OutputInterface;

/* 
 * This file is part of The OpenSky Project
 */

/**
 * Description of ServerCommand
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 */
class ServerCommand extends Command
{
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputArgument('name', InputArgument::REQUIRED, 'command to run - start, stop or restart'),
                new InputArgument('ip', InputArgument::OPTIONAL, 'ip address, to listen on', '127.0.0.1'),
                new InputArgument('port', InputArgument::OPTIONAL, 'port, to listen on, random is used if none specified'),
            ))
            ->setName('micro:server')
            ->setDescription('Allows control the micro-kernel server.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        set_time_limit (0);

        // Set the ip and port we will listen on
        $address = $input->getArgument('ip');
        $port = $input->getArgument('port') ?: rand(4000, 4500);
        $maxClients = 10;

        // Array that will hold client information
        $clients = Array();

        // Create a TCP Stream socket
        $sock = socket_create(AF_INET, SOCK_STREAM, 0);
        // Bind the socket to an address/port
        if (!socket_bind($sock, $address, $port)) {
            $output->writeln('Could not bind to address ' . $input->getArgument('ip') . ':' . $port);
            return;
        }
        // Start listening for connections
        socket_listen($sock);
        $output->writeln('Server started at ' . $input->getArgument('ip') . ':' . $port);
        // Loop continuously
        while (true) {
            // Setup clients listen socket for reading
            $read[0] = $sock;
            for ($i = 0; $i < $maxClients; $i++) {
                if (isset ($clients[$i]['sock'])) {
                    $read[$i + 1] = $clients[$i]['sock'];
                }
            }
            // Set up a blocking call to socket_select()
            $write = null;
            $except = array();
            $tv_spec = null;
            $ready = socket_select($read, $write, $except, $tv_spec);
            /* if a new connection is being made add it to the client array */
            if (in_array($sock, $read)) {
                for ($i = 0; $i < $maxClients; $i++) {
                    if (isset ($clients[$i]['sock'])) {
                        $clients[$i]['sock'] = socket_accept($sock);
                        break;
                    }
                    elseif ($i == $maxClients - 1) {
                        print ("too many clients");
                    }
                }
                if (--$ready <= 0) {
                    continue;
                }
            } // end if in_array

            // If a client is trying to write - handle it now
            for ($i = 0; $i < $maxClients; $i++) // for each client
            {
                if (in_array($clients[$i]['sock'] , $read)) {
                    $input = socket_read($clients[$i]['sock'] , 1024);
                    if ($input == null) {
                        // Zero length string meaning disconnected
                        unset($clients[$i]);
                    }
                    $n = trim($input);
                    if ($input == 'exit') {
                        // requested disconnect
                        socket_close($clients[$i]['sock']);
                    } elseif ($input) {
                        // strip white spaces and write back to user
                        $output = ereg_replace("[ \t\n\r]","",$input).chr(0);
                        socket_write($clients[$i]['sock'],$output);
                    }
                } else {
                    // Close the socket
                    socket_close($clients[$i]['sock']);
                    unset($clients[$i]);
                }
            }
        } // end while
        // Close the master sockets
        socket_close($sock);
    }

}
