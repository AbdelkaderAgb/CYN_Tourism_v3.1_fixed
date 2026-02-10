<?php
/**
 * CYN Tourism - Voucher Controller
 * 
 * Handles voucher-related business logic.
 * Separates concerns from presentation layer.
 * 
 * @package CYN_Tourism
 * @version 3.0.0
 */

require_once __DIR__ . '/BaseController.php';
require_once dirname(__DIR__) . '/Models/VoucherModel.php';

class VoucherController extends BaseController {
    
    /** @var VoucherModel Voucher model */
    private $voucherModel;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->voucherModel = new VoucherModel();
        $this->requireAuth();
    }
    
    /**
     * List all vouchers
     */
    public function index() {
        $status = $this->get('status');
        $startDate = $this->get('start_date');
        $endDate = $this->get('end_date');
        
        if ($startDate && $endDate) {
            $vouchers = $this->voucherModel->findByDateRange($startDate, $endDate);
        } elseif ($status) {
            $vouchers = $this->voucherModel->findByStatus($status);
        } else {
            $vouchers = $this->voucherModel->findAll();
        }
        
        $this->view('vouchers/list', [
            'vouchers' => $vouchers,
            'title' => 'Vouchers'
        ]);
    }
    
    /**
     * Show voucher details
     */
    public function show() {
        $id = $this->get('id');
        $voucher = $this->voucherModel->find($id);
        
        if (!$voucher) {
            $this->redirect('/404.php');
        }
        
        $this->view('vouchers/show', [
            'voucher' => $voucher,
            'title' => 'Voucher Details'
        ]);
    }
    
    /**
     * Create new voucher form
     */
    public function create() {
        $this->data['csrf_token'] = $this->generateCsrf();
        $this->data['voucher_no'] = $this->voucherModel->generateVoucherNumber();
        
        $this->view('vouchers/create', [
            'title' => 'Create Voucher'
        ]);
    }
    
    /**
     * Store new voucher
     */
    public function store() {
        if (!$this->validateCsrf()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
        }
        
        $data = [
            'voucher_no' => $this->post('voucher_no'),
            'company_name' => $this->post('company_name'),
            'hotel_name' => $this->post('hotel_name'),
            'pickup_location' => $this->post('pickup_location'),
            'dropoff_location' => $this->post('dropoff_location'),
            'pickup_date' => $this->post('pickup_date'),
            'pickup_time' => $this->post('pickup_time'),
            'transfer_type' => $this->post('transfer_type', 'one_way'),
            'total_pax' => $this->post('total_pax', 0),
            'price' => $this->post('price', 0),
            'currency' => $this->post('currency', 'USD'),
            'status' => $this->post('status', 'pending'),
            'created_by' => $this->user()['id']
        ];
        
        // Validate required fields
        $required = ['voucher_no', 'company_name', 'pickup_location', 'dropoff_location', 'pickup_date'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $this->json(['error' => "Field $field is required"], 400);
            }
        }
        
        $result = $this->voucherModel->create($data);
        
        if ($result) {
            $this->json(['success' => true, 'id' => $result]);
        } else {
            $this->json(['error' => 'Failed to create voucher'], 500);
        }
    }
    
    /**
     * Edit voucher form
     */
    public function edit() {
        $id = $this->get('id');
        $voucher = $this->voucherModel->find($id);
        
        if (!$voucher) {
            $this->redirect('/404.php');
        }
        
        $this->data['csrf_token'] = $this->generateCsrf();
        
        $this->view('vouchers/edit', [
            'voucher' => $voucher,
            'title' => 'Edit Voucher'
        ]);
    }
    
    /**
     * Update voucher
     */
    public function update() {
        if (!$this->validateCsrf()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
        }
        
        $id = $this->post('id');
        
        $data = [
            'company_name' => $this->post('company_name'),
            'hotel_name' => $this->post('hotel_name'),
            'pickup_location' => $this->post('pickup_location'),
            'dropoff_location' => $this->post('dropoff_location'),
            'pickup_date' => $this->post('pickup_date'),
            'pickup_time' => $this->post('pickup_time'),
            'status' => $this->post('status'),
            'updated_by' => $this->user()['id']
        ];
        
        $result = $this->voucherModel->update($id, $data);
        
        if ($result) {
            $this->json(['success' => true]);
        } else {
            $this->json(['error' => 'Failed to update voucher'], 500);
        }
    }
    
    /**
     * Delete voucher
     */
    public function delete() {
        if (!$this->validateCsrf()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
        }
        
        $id = $this->post('id');
        $result = $this->voucherModel->delete($id);
        
        if ($result) {
            $this->json(['success' => true]);
        } else {
            $this->json(['error' => 'Failed to delete voucher'], 500);
        }
    }
}
