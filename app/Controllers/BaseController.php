<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;
    protected $excel_header_color;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // E.g.: $this->session = \Config\Services::session();
        $this->session = \Config\Services::session();
        
        helper('auth'); // Load auth_helper
        
        // Fetch global settings
        $db = \Config\Database::connect();
        $settings = $db->table('settings')->where('id', 1)->get()->getRowArray();
        
        $custom_menus = $db->table('custom_menus')->get()->getResultArray();
        
        // Share data to all views
        $viewData = [
            'top_color' => $settings['top_color'] ?? '#0077b6',
            'side_color' => $settings['side_color'] ?? '#f0f2f5',
            'excel_header_color' => $settings['excel_header_color'] ?? '#4e73df',
            'custom_menus' => $custom_menus,
            'current_user' => $this->session->get('user') ?? 'Guest',
            'current_role' => strtoupper($this->session->get('role') ?? '')
        ];
        
        $this->excel_header_color = $settings['excel_header_color'] ?? '#4e73df';
        
        // Push to View
        foreach ($viewData as $key => $value) {
            // Kita bisa set variabel global untuk views di sini, tapi di CI4 lebih aman return dari controller
            // Namun, untuk memudahkan karena ada banyak view, kita gunakan service renderer
        }
        $renderer = \Config\Services::renderer();
        $renderer->setData($viewData);
    }
}
