<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Storage;
use App\Models\UploadModel;

class ProcessImportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $path;

    // Giving a timeout of 20 minutes to the Job to process the file
    public $timeout = 1200;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function handle(): void
    {
        $filePath = storage_path('app/'.$this->path);
        $fileContents = file_get_contents($filePath);
        $rows = explode("\n", $fileContents);
        $csvData = array_map('str_getcsv', $rows);
        $headers = array_shift($csvData);
        $result = array_map(function($name) {
            return str_replace(' ', '_', $name);
        }, $headers);
    
        $chunks = array_chunk($csvData, 2);
        
        foreach ($chunks as $key => $chunk) {
            $tempData = [];
            foreach($chunk as $ch){
                if(!empty($ch[0])) {
                    $data = array_combine($result, $ch);
                    array_push($tempData, $data);
                }
            }
            UploadModel::insert($tempData);
        }

    }
}
