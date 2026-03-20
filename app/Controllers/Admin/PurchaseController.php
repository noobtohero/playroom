<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PurchaseModel;
use App\Models\EnrollmentModel;

class PurchaseController extends BaseController
{
    public function index()
    {
        $purchaseModel = new PurchaseModel();
        
        $db = \Config\Database::connect();
        $builder = $db->table('purchases');
        $builder->select('purchases.*, users.name as user_name, courses.title as course_title');
        $builder->join('users', 'users.id = purchases.user_id');
        $builder->join('courses', 'courses.id = purchases.course_id');
        $builder->orderBy('purchases.created_at', 'DESC');
        
        $data['purchases'] = $builder->get()->getResultArray();
        
        return view('admin/purchases/index', $data);
    }

    public function approve($id)
    {
        $purchaseModel = new PurchaseModel();
        $enrollmentModel = new EnrollmentModel();
        
        $purchase = $purchaseModel->find($id);
        
        if (!$purchase || $purchase['status'] !== 'pending') {
            return redirect()->back()->with('error', 'Purchase not found or already processed.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Update purchase status
        $purchaseModel->update($id, [
            'status'      => 'approved',
            'approved_by' => session()->get('id'),
            'updated_at'  => date('Y-m-d H:i:s')
        ]);

        // 2. Create Enrollment
        $enrollmentModel->insert([
            'user_id'    => $purchase['user_id'],
            'course_id'  => $purchase['course_id'],
            'source'     => 'purchase',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
             return redirect()->back()->with('error', 'Failed to approve purchase.');
        }

        return redirect()->to('admin/purchases')->with('success', 'Purchase approved and user enrolled.');
    }

    public function reject($id)
    {
        $purchaseModel = new PurchaseModel();
        
        $purchase = $purchaseModel->find($id);
        if (!$purchase || $purchase['status'] !== 'pending') {
            return redirect()->back()->with('error', 'Purchase not found or already processed.');
        }

        $purchaseModel->update($id, [
            'status' => 'rejected',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('admin/purchases')->with('success', 'Purchase rejected.');
    }
}
