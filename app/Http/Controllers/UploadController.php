<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use App\Jobs\ProcessImportJob;
use Exception;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Illuminate\Support\LazyCollection;
use App\Models\UploadModel;
use Yajra\DataTables\DataTables;

class UploadController extends Controller
{
    public $headers;

    public function __construct()
    {
        $this->headers = [];   
    }

    public function list(Request $request){
        if($request->ajax()){
            $data = UploadModel::select('employee_id','name','domain','year_founded','industry','size_range','locality','country','linkedin_url','current_employee_estimate','total_employee_estimate')->limit(100000)->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
        return view('dashboard');
    }

    public function uploadForm(){
        return view('uploadForm');
    }

    public function upload(Request $request)
    {
        $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));
        if (!$receiver->isUploaded()) {
            return [
                'done' => 'File not uploaded',
                'status' => false
            ];
        }

        $fileReceived = $receiver->receive();
        if ($fileReceived->isFinished()) { 
            try {
                $file = $fileReceived->getFile();
                $extension = $file->getClientOriginalExtension();
                $fileName = str_replace('.'.$extension, '', $file->getClientOriginalName()); 
                $fileName .= '_' . md5(time()) . '.' . $extension;
                $path = Storage::disk(config('filesystems.default'))->put('public/csv', $file);
                $handle = fopen(storage_path('app/'.$path), 'r');
                ProcessImportJob::dispatch($path,$handle);
                unlink($file->getPathname());
                return [
                    'path' => asset('storage/' . $path),
                    'filename' => $fileName
                ];
            } catch(Exception $e) {
                Log::error('An error occurred: ' . $e->getMessage());
            }
        }

        $handler = $fileReceived->handler();
        return [
            'done' => $handler->getPercentageDone(),
            'status' => true
        ];
    }

}
