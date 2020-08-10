<?php

namespace HnrAzevedo\Viewer;

use Exception;

trait CheckTrait{

    protected function check_viewExist(string $viewfile){
        if(!file_exists($this->path . DIRECTORY_SEPARATOR . $viewfile . '.view.php')){
            $v = $this->path . DIRECTORY_SEPARATOR . $viewfile;
            throw new Exception("Preview file {$v} not found");
        }
    }

}