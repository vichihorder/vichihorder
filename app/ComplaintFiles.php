<?php
/**
 * Created by PhpStorm.
 * User: goerge
 * Date: 15/04/2017
 * Time: 12:05
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class ComplaintFiles extends Model
{
    protected $table = 'complaints_files';

    /**
     * ham tao luu duong dan anh khieu nai
     * @param array $complaint_file
     * @return bool
     */
    public function createComplaintFile($complaint_file = array()){
        $result = $this->insert([
            'name' => $complaint_file['name'],
            'path' => $complaint_file['path'],
            'complaint_id' => $complaint_file['complaint_id'],
            'file_type' => '',
            'create_time' =>  date('Y-m-d H:i:s',time())

        ]);
        if(!$result){
            return false;
        }
        return true;
        
    }

}