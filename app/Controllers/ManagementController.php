<?php
/**
 * CYN Tourism - Management Controller
 * 
 * Replaces consolidated-management.php with proper MVC pattern.
 * Handles: partners, drivers, vehicles, tour guides, users
 * 
 * @package CYN_Tourism
 * @version 3.0.0
 */

require_once __DIR__ . '/BaseController.php';
require_once dirname(__DIR__) . '/Models/BaseModel.php';

class ManagementController extends BaseController {
    
    /** @var array Management configurations */
    private $configs = [
        'partners' => [
            'title' => 'Partner Management',
            'table' => 'partners',
            'fields' => ['company' => 'company_name', 'email' => 'email', 'phone' => 'phone', 'status' => 'status'],
            'form' => ['company' => 'text', 'contact_name' => 'text', 'email' => 'email', 'phone' => 'text', 'address' => 'textarea', 'status' => 'select'],
            'required' => ['company']
        ],
        'drivers' => [
            'title' => 'Driver Management',
            'table' => 'drivers',
            'fields' => ['name' => 'first_name', 'phone' => 'phone', 'license_no' => 'license_number', 'status' => 'status'],
            'form' => ['name' => 'text', 'phone' => 'text', 'license_no' => 'text', 'status' => 'select'],
            'required' => ['name']
        ],
        'vehicles' => [
            'title' => 'Vehicle Management',
            'table' => 'vehicles',
            'fields' => ['plate_number' => 'plate_number', 'model' => 'model', 'capacity' => 'capacity', 'status' => 'status'],
            'form' => ['plate_number' => 'text', 'model' => 'text', 'capacity' => 'number', 'status' => 'select'],
            'required' => ['plate_number']
        ],
        'guides' => [
            'title' => 'Guide Management',
            'table' => 'tour_guides',
            'fields' => ['name' => 'first_name', 'phone' => 'phone', 'languages' => 'languages', 'status' => 'status'],
            'form' => ['name' => 'text', 'phone' => 'text', 'languages' => 'text', 'status' => 'select'],
            'required' => ['name']
        ],
        'users' => [
            'title' => 'User Management',
            'table' => 'users',
            'fields' => ['name' => 'first_name', 'email' => 'email', 'role' => 'role', 'status' => 'status'],
            'form' => ['first_name' => 'text', 'last_name' => 'text', 'email' => 'email', 'role' => 'select', 'status' => 'select'],
            'required' => ['first_name', 'email']
        ]
    ];
    
    /** @var BaseModel Model instance */
    private $model;
    
    /** @var string Current management type */
    private $type;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->requireAuth();
        $this->type = $this->get('type', 'partners');
        
        // Validate type
        if (!isset($this->configs[$this->type])) {
            $this->redirect('/404.php');
        }
        
        // Create a generic model for this type
        $this->model = $this->createModel($this->configs[$this->type]['table']);
    }
    
    /**
     * Create a model instance for a table
     * 
     * @param string $table Table name
     * @return BaseModel Model instance
     */
    private function createModel($table) {
        return new class($table) extends BaseModel {
            public function __construct($tableName) {
                parent::__construct();
                $this->table = $tableName;
            }
        };
    }
    
    /**
     * List all records
     */
    public function index() {
        $config = $this->configs[$this->type];
        $records = $this->model->findAll();
        
        $this->view('management/index', [
            'title' => $config['title'],
            'type' => $this->type,
            'config' => $config,
            'records' => $records
        ]);
    }
    
    /**
     * Create new record
     */
    public function create() {
        $config = $this->configs[$this->type];
        $this->data['csrf_token'] = $this->generateCsrf();
        
        $this->view('management/create', [
            'title' => 'Create ' . $config['title'],
            'type' => $this->type,
            'config' => $config
        ]);
    }
    
    /**
     * Store new record
     */
    public function store() {
        if (!$this->validateCsrf()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
        }
        
        $config = $this->configs[$this->type];
        $data = $this->post();
        
        // Validate required fields
        foreach ($config['required'] as $field) {
            if (empty($data[$field])) {
                $this->json(['error' => "Field $field is required"], 400);
            }
        }
        
        $result = $this->model->create($data);
        
        if ($result) {
            $this->json(['success' => true, 'id' => $result]);
        } else {
            $this->json(['error' => 'Failed to create record'], 500);
        }
    }
    
    /**
     * Update record
     */
    public function update() {
        if (!$this->validateCsrf()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
        }
        
        $id = $this->post('id');
        $data = $this->post();
        unset($data['id'], $data['csrf_token']);
        
        $result = $this->model->update($id, $data);
        
        if ($result) {
            $this->json(['success' => true]);
        } else {
            $this->json(['error' => 'Failed to update record'], 500);
        }
    }
    
    /**
     * Delete record
     */
    public function delete() {
        if (!$this->validateCsrf()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
        }
        
        $id = $this->post('id');
        $result = $this->model->delete($id);
        
        if ($result) {
            $this->json(['success' => true]);
        } else {
            $this->json(['error' => 'Failed to delete record'], 500);
        }
    }
}
