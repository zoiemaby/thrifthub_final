<?php
/**
 * Business Type Class
 * ThriftHub - Business Type Reference Data Class
 * 
 * Lightweight class for populating business type dropdowns
 */

require_once __DIR__ . '/../settings/db_class.php';

class BusinessType extends Database {
    
    /**
     * Get all business types
     * 
     * @return array Returns array of business types (type_id, type_description)
     */
    public function get_all_business_types() {
        $sql = "SELECT type_id, type_description FROM business_types ORDER BY type_description ASC";
        return $this->fetchAll($sql);
    }
    
    /**
     * Get business type by ID
     * 
     * @param int $typeId Type ID
     * @return array|false Returns business type data or false if not found
     */
    public function get_business_type($typeId) {
        $typeId = (int)$typeId;
        $sql = "SELECT * FROM business_types WHERE type_id = $typeId";
        return $this->fetchOne($sql);
    }
}
