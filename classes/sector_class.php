<?php
/**
 * Sector Class
 * ThriftHub - Sector Reference Data Class
 * 
 * Lightweight class for populating sector dropdowns
 */

require_once __DIR__ . '/../settings/db_class.php';

class Sector extends Database {
    
    /**
     * Get all sectors
     * 
     * @return array Returns array of sectors (sector_id, sector_description)
     */
    public function get_all_sectors() {
        $sql = "SELECT sector_id, sector_description FROM sectors ORDER BY sector_description ASC";
        return $this->fetchAll($sql);
    }
    
    /**
     * Get sector by ID
     * 
     * @param int $sectorId Sector ID
     * @return array|false Returns sector data or false if not found
     */
    public function get_sector($sectorId) {
        $sectorId = (int)$sectorId;
        $sql = "SELECT * FROM sectors WHERE sector_id = $sectorId";
        return $this->fetchOne($sql);
    }
}
