<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Trait for Dosya controller - extracts common API logic
 * Reduces Dosya.php controller size by moving shared methods
 */
trait Dosya_api_trait
{
    /**
     * Datatable column mapping for list sorting
     */
    protected function getDosyaTableColumnName($data = 0)
    {
        $columns = [
            0 => 'd_id',
            1 => 'dm_acilistarihi',
            2 => 'd_kurumdosyano',
            3 => 'd_davaci',
            4 => 'd_davali',
            5 => 'd_davakonuaciklama',
            6 => 'dm_mahkeme',
            7 => 'dm_esasno',
            8 => 'dm_kararno',
            9 => 'd_mevkiplaka',
            10 => 'd_tags',
        ];
        return $columns[$data] ?? 'd_id';
    }

    /**
     * Mahkeme list column mapping
     */
    protected function getDosyaMahkemeTableColumnName($data = 0)
    {
        $columns = [
            0 => 'dm_mahkeme',
            1 => 'dm_acilistarihi',
            2 => 'dm_esasno',
            3 => 'dm_kararno',
            4 => 'dm_karartarihi',
            5 => 'dm_aciklama',
        ];
        return $columns[$data] ?? 'dm_mahkeme';
    }

    /**
     * Generic Tagify search - fetches distinct values for autocomplete
     */
    protected function dosyaTagifySearch($model, $table, $column, $resultColumn = null)
    {
        $searchText = safe_like_escape(trim($this->JSON_DATA->searchText ?? ''));
        $resultCol = $resultColumn ?? $column;
        $items = $model->ek_get_all_like($table, $column, $searchText, $column);
        $itemText = "";
        if ($items) {
            foreach ($items as $item) {
                $val = $item->$resultCol ?? $item->$column ?? '';
                $parts = explode("@", $val);
                foreach ($parts as $p) {
                    if (strlen(trim($p)) > 0 && stripos($itemText, trim($p)) === false) {
                        $itemText .= trim($p) . ",";
                    }
                }
            }
        }
        return ['items' => $items, 'itemText' => $itemText];
    }
}
