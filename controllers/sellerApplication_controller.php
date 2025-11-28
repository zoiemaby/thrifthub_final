<?php
/**
 * Seller Application Controller
 * ThriftHub - Seller Application Management Controller
 * 
 * Interface between views/actions and the SellerApplication class
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../classes/sellerApplication_class.php';

class SellerApplicationController {
    private $application;
    
    public function __construct() {
        $this->application = new SellerApplication();
    }
    
    /**
     * Create seller application
     */
    public function create_seller_application_ctrl($userId, $documentationPath) {
        if (empty($documentationPath)) {
            return [
                'success' => false,
                'message' => 'Documentation path is required.'
            ];
        }
        
        $applicationId = $this->application->create_application($userId, $documentationPath);
        
        if ($applicationId) {
            return [
                'success' => true,
                'message' => 'Seller application submitted successfully. We will review it soon.',
                'application_id' => $applicationId
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to submit application. You may have already applied.'
            ];
        }
    }
    
    /**
     * Get pending applications
     */
    public function get_pending_applications_ctrl() {
        $applications = $this->application->get_pending_applications();
        
        return [
            'success' => true,
            'applications' => $applications,
            'count' => count($applications)
        ];
    }
    
    /**
     * Get application by ID
     */
    public function get_application_by_id_ctrl($applicationId) {
        $application = $this->application->get_application_by_id($applicationId);
        
        if ($application) {
            return [
                'success' => true,
                'application' => $application
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Application not found.'
            ];
        }
    }
    
    /**
     * Update application status
     */
    public function update_application_status_ctrl($applicationId, $status, $reviewedBy) {
        if (!in_array($status, ['approved', 'rejected'])) {
            return [
                'success' => false,
                'message' => 'Invalid status. Must be approved or rejected.'
            ];
        }
        
        $result = $this->application->update_application_status($applicationId, $status, $reviewedBy);
        
        if ($result) {
            return [
                'success' => true,
                'message' => "Application $status successfully."
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to update application status.'
            ];
        }
    }
    
    /**
     * Get application by user
     */
    public function get_application_by_user_ctrl($userId) {
        $application = $this->application->get_application_by_user($userId);
        
        if ($application) {
            return [
                'success' => true,
                'application' => $application
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No application found for this user.'
            ];
        }
    }
}
