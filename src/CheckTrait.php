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

    protected function check_importExist($import): string
    {
        $tpl = str_replace('.',DIRECTORY_SEPARATOR,
                substr(
                    $import,
                    strpos($import,'\'')+1,
                    strlen($import)-11
                )
            );

        if(!file_exists($this->path . DIRECTORY_SEPARATOR . $tpl . '.tpl.php')){
            throw new Exception('Import \''.str_replace(['@import(\'','\')'],'',$import).'\' não encontrado.');
        }
        return $tpl;
    }

}