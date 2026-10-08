<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\AlarmPanelModel;
use App\Models\AlarmEventModel;

class AlarmServer extends BaseCommand
{
    protected $group       = 'Custom';
    protected $name        = 'alarm:server';
    protected $description = 'Run TCP Socket Server to listen for Alarm Panel CMS events.';
    protected $usage       = 'alarm:server [port]';
    protected $arguments   = [
        'port' => 'Port to listen on (default: 5000)'
    ];

    public function run(array $params)
    {
        $port = $params[0] ?? 5000;
        $host = "0.0.0.0";

        $server = stream_socket_server("tcp://$host:$port", $errno, $errorMessage);

        if ($server === false) {
            CLI::error("Could not bind to socket: $errorMessage");
            return;
        }

        CLI::write("Security Alarm TCP Receiver started on $host:$port", 'green');
        CLI::write("Waiting for incoming CMS events...", 'yellow');

        $alarmPanelModel = new AlarmPanelModel();
        $alarmEventModel = new AlarmEventModel();

        while (true) {
            // Check for incoming connections
            $client = @stream_socket_accept($server, -1);
            if ($client) {
                $clientName = stream_socket_get_name($client, true);
                $ip = explode(':', $clientName)[0];
                
                $data = fread($client, 1024);
                if (!empty($data)) {
                    $rawData = trim($data);
                    CLI::write("Received from $ip: " . $rawData);

                    // Find panel by IP
                    $panel = $alarmPanelModel->where('ip_address', $ip)->first();
                    $panelId = $panel ? $panel['id'] : null;

                    if ($panel) {
                        $alarmPanelModel->update($panelId, ['last_online' => date('Y-m-d H:i:s'), 'status' => 'Online']);
                    }

                    // Basic parsing for SIA or Contact ID (can be expanded later)
                    $eventType = 'Unknown';
                    $zone = '';
                    $desc = 'Raw Alarm Event';

                    // Very basic Contact ID (CID) format detection: 
                    // usually ends with something like: ACCT MT EVENT GG ZZZ
                    // or SIA format contains SIA-DCS
                    if (stripos($rawData, 'SIA-DCS') !== false) {
                        $eventType = 'SIA DC-09';
                        if (preg_match('/\[.*?(BA|BR|WA|WR|FA|FR|PA|PR).*?\]/', $rawData, $matches)) {
                            $code = $matches[1];
                            $map = [
                                'BA' => 'Burglar Alarm', 'BR' => 'Burglar Restore',
                                'FA' => 'Fire Alarm', 'FR' => 'Fire Restore',
                                'PA' => 'Panic Alarm', 'PR' => 'Panic Restore',
                                'WA' => 'Water Alarm', 'WR' => 'Water Restore'
                            ];
                            $desc = $map[$code] ?? "SIA Event Code: $code";
                        }
                    } else if (preg_match('/[0-9]{4} 18 ([0-9]{4}) ([0-9]{2}) ([0-9]{3})/', $rawData, $matches)) {
                        $eventType = 'Contact ID';
                        $eventCode = $matches[1];
                        $zone = ltrim($matches[3], '0');
                        $desc = "Event Code: $eventCode, Zone: $zone";
                    }

                    // Save to DB
                    $alarmEventModel->insert([
                        'panel_id' => $panelId,
                        'event_time' => date('Y-m-d H:i:s'),
                        'raw_data' => $rawData,
                        'event_type' => $eventType,
                        'description' => $desc,
                        'zone' => $zone
                    ]);

                    // Send ACK back to panel so it knows we received it
                    // SIA requires a specific ACK format, but a simple ACK is often enough for generic panels
                    if (stripos($rawData, 'SIA-DCS') !== false) {
                        // Very simplified SIA ACK (some panels require strict sequence numbering, we send a generic ACK)
                        $ack = "\n\r";
                        fwrite($client, $ack);
                    } else {
                        fwrite($client, "\x06"); // ACK character for standard CID
                    }
                }
                
                fclose($client);
            }
        }
    }
}
