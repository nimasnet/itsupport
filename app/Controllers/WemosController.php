<?php

namespace App\Controllers;

use App\Models\WemosLogModel;

class WemosController extends BaseController
{
    protected $wemosLogModel;

    public function __construct()
    {
        $this->wemosLogModel = new WemosLogModel();
    }

    public function index()
    {
        // View for the dashboard
        $data = [
            'title' => 'Monitoring Wemos',
            'wemos_ip' => '10.3.39.178'
        ];
        return view('wemos/index', $data);
    }

    // Endpoint to receive log data POSTed by Wemos
    public function apiReceiveLog()
    {
        // Disable CORS restrictions for Wemos requests if needed
        header('Access-Control-Allow-Origin: *');
        
        $json = $this->request->getJSON();
        
        if ($json && isset($json->event_type) && isset($json->message)) {
            $this->wemosLogModel->insert([
                'event_type' => $json->event_type,
                'message'    => $json->message
            ]);
            
            return $this->response->setJSON(['status' => 'success']);
        }
        
        return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid data format'])->setStatusCode(400);
    }

    // Fetch logs for the dashboard from the local database
    public function apiGetLogs()
    {
        $logs = $this->wemosLogModel->orderBy('created_at', 'DESC')->findAll(15);
        $formattedLogs = [];
        foreach ($logs as $log) {
            $formattedLogs[] = $log['created_at'] . ' - ' . $log['event_type'] . ': ' . $log['message'];
        }
        
        // Count today's door open events
        $today = date('Y-m-d');
        $dailyCount = $this->wemosLogModel->where('event_type', 'door_open')
                                          ->like('created_at', $today, 'after')
                                          ->countAllResults();
                                          
        return $this->response->setJSON([
            'daily' => $dailyCount,
            'weekly' => 0, // Simplified for now
            'monthly' => 0, // Simplified for now
            'logs' => $formattedLogs
        ]);
    }
}
