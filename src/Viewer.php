<?php

namespace HnrAzevedo\Viewer;

class Viewer{
    use HelperTrait, CheckTrait;

    private static $instance = null;
    private string $path = '';

    public function __construct(string $path){
        $this->path = $path;
        return $this;
    }

    public static function getInstance(string $path){
        if(is_null(self::$instance)){
            self::$instance = new self($path);
        }
        return self::$instance;
    }

    public static function create(string $path){
        return self::getInstance($path);
    }

    public function render(string $file, array $data = [], bool $return = false): string
    {
        header('Content-Type: text/html; charset=utf-8');
        
        $this->check_viewExist($file);

        $buffer = $this->getOB($this->path . DIRECTORY_SEPARATOR . $file . '.view.php', $data);
        
        $buffer = $this->getVars($buffer);
        
        $buffer = $this->getImport($buffer, $data);
        
        $buffer = $this->removeComments($buffer);

        $this->saveData();

        if(!$return){
            echo $buffer;
            return '';
        }
        
        return $buffer;
    }

}
