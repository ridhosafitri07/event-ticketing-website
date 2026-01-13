<?php

namespace App\Controllers;

use App\Models\BookingModel;
use App\Models\PaymentProofModel;
use App\Models\MidtransTransactionModel;

class PaymentController extends BaseController
{
    protected $bookingModel;
    protected $paymentProofModel;
    protected $midtransModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->paymentProofModel = new PaymentProofModel();
        $this->midtransModel = new MidtransTransactionModel();
    }

    /**
     * Upload Bukti Transfer (POST)
     */
    public function uploadProof($bookingId)
    {
        $userId = $this->session->get('user_id');
        $booking = $this->bookingModel->find($bookingId);

        // Validasi
        if (!$booking) {
            return redirect()->to('/user/riwayat')->with('error', 'Booking tidak ditemukan');
        }

        if ($booking['user_id'] != $userId) {
            return redirect()->to('/user/riwayat')->with('error', 'Unauthorized');
        }

        if ($booking['payment_method'] !== 'Transfer Manual') {
            return redirect()->to('/user/riwayat')->with('error', 'Metode pembayaran bukan transfer manual');
        }

        // Validasi file
        $rules = [
            'payment_proof' => [
                'uploaded[payment_proof]',
                'mime_in[payment_proof,image/jpg,image/jpeg,image/png,application/pdf]',
                'max_size[payment_proof,2048]' // 2MB
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('payment_proof');

        if ($file->isValid() && !$file->hasMoved()) {
            // Generate unique filename
            $newName = 'proof_' . $booking['booking_number'] . '_' . time() . '.' . $file->getExtension();

            // Move file to uploads folder
            $file->move(FCPATH . 'uploads/payment_proofs', $newName);

            // Save to database
            $proofData = [
                'booking_id' => $bookingId,
                'booking_number' => $booking['booking_number'],
                'file_name' => $newName,
                'file_path' => 'uploads/payment_proofs/' . $newName,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize()
            ];

            try {
                $this->paymentProofModel->insert($proofData);

                // Update booking status
                $this->bookingModel->update($bookingId, [
                    'status' => 'Waiting Approval',
                    'payment_details' => 'Bukti transfer telah diupload'
                ]);

                return redirect()->to('/user/riwayat')->with('success', 'Bukti transfer berhasil diupload. Menunggu verifikasi admin');
            } catch (\Exception $e) {
                // Delete uploaded file if database insert fails
                if (file_exists(FCPATH . 'uploads/payment_proofs/' . $newName)) {
                    unlink(FCPATH . 'uploads/payment_proofs/' . $newName);
                }
                return redirect()->back()->with('error', 'Upload gagal: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'File tidak valid');
    }

    /**
     * Midtrans Checkout (Fase 2)
     */
    public function midtransCheckout($bookingId)
    {
        $userId = $this->session->get('user_id');
        $booking = $this->bookingModel->find($bookingId);

        // Validasi
        if (!$booking) {
            return redirect()->to('/user/riwayat')->with('error', 'Booking tidak ditemukan');
        }

        if ($booking['user_id'] != $userId) {
            return redirect()->to('/user/riwayat')->with('error', 'Unauthorized');
        }

        if ($booking['payment_method'] !== 'Midtrans') {
            return redirect()->to('/user/riwayat')->with('error', 'Metode pembayaran bukan Midtrans');
        }

        // TODO: Implement Midtrans Snap di Fase 2
        $data = [
            'title' => 'Pembayaran - EventKu',
            'booking' => $booking
        ];

        return view('payment/midtrans_checkout', $data);
    }

    /**
     * Midtrans Callback/Webhook (Fase 2)
     */
    public function midtransCallback()
    {
        // TODO: Implement Midtrans callback handler di Fase 2
        // Verifikasi signature
        // Update booking status based on transaction status
        // Save transaction data

        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);

        log_message('info', 'Midtrans Callback: ' . $jsonData);

        // Temporary response
        return $this->response->setJSON([
            'status' => 'received',
            'message' => 'Callback received (not implemented yet)'
        ]);
    }

    /**
     * Payment Success Page
     */
    public function paymentSuccess()
    {
        $data = [
            'title' => 'Pembayaran Berhasil - EventKu',
            'user' => $this->getUserData()
        ];

        return view('payment/success', $data);
    }

    /**
     * Payment Failed Page
     */
    public function paymentFailed()
    {
        $data = [
            'title' => 'Pembayaran Gagal - EventKu',
            'user' => $this->getUserData()
        ];

        return view('payment/failed', $data);
    }

    /**
     * Get User Data
     */
    private function getUserData()
    {
        if ($this->session->get('logged_in')) {
            $userModel = new \App\Models\UserModel();
            return $userModel->find($this->session->get('user_id'));
        }
        return null;
    }
}
