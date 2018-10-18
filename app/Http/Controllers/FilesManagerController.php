<?php

namespace App\Http\Controllers;

use Flow\Config;
use Flow\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class FilesManagerController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getFile()
    {

        $file = $this->initFlowFile();

        if ($file->checkChunk()) {
            return response("Getting File");
        } else {
            return response("No Content", 204);
        }
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function postFile()
    {
        $file = $this->initFlowFile();
        if ($file->validateChunk()) {
            $file->saveChunk();
            return $this->saveFile($file);
        } else {
            return response("Bad Request", 400);
        }
    }

    /**
     * @return File
     */
    protected function initFlowFile()
    {
        $config = new Config();
        if (!is_dir(storage_path() . '/uploads/temp/')) {
            mkdir(storage_path() . '/uploads/temp/', 0775, true);
        }

        $config->setTempDir(storage_path() . '/uploads/temp/');

        return new File($config);
    }

    /**
     * @param $file
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    private function saveFile($file)
    {
        $fileName =Input::get('flowRelativePath');
        $tmp = uniqid('xls', false);

        $path = storage_path() . '/uploads/temp/';
        if ($file->validateFile() && $file->save($path. $fileName)) {
            return response("Complete", 200);
        } else {
            return response("Ok", 200);
        }

    }

}
