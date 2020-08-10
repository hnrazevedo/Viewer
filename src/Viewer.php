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

    public function render(string $file, string $return = null)
    {
        header('Content-Type: text/html; charset=utf-8');

        if(!empty($_SESSION['save'])){
            foreach ($_SESSION['save'] as $key => $value) {
                $_SESSION['data'][$key] = $value;
            }
        }
        
        $this->check_viewExist($file);

        $buffer = $this->getOB($this->path . DIRECTORY_SEPARATOR . $file . '.view.php');
        
        $buffer = $this->getVars($buffer);
        
        $buffer = $this->getImport($buffer);
        
        $buffer = $this->removeComments($buffer);

        if(is_null($return)){
            echo $buffer;
        }else{
            return $buffer;
        }

        unset($_SESSION['data']);

        if(!empty($_SESSION['save'])){
            foreach ($_SESSION['save'] as $key => $value) {
                $_SESSION['data'][$key] = $value;
            }
        }
    }

}
