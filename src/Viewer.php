<?php

namespace HnrAzevedo\Viewer;

class Viewer{
    use EspecialHelperTrait, CheckTrait;

    private static $instance = null;
    private string $path = '';

    public function __construct()
    {
        return $this;
    }

    public static function getInstance(string $path): Viewer
    {
        if(is_null(self::$instance)){
            self::$instance = new self();
        }
        self::$instance->path = $path;
        return self::$instance;
    }

    public static function create(string $path): Viewer
    {
        return self::getInstance($path);
    }

    public function render(string $file, array $data = [], bool $return = false): string
    {
        if(!headers_sent()){
            header('Content-Type: text/html; charset=utf-8');
        }
        
        $this->check_viewExist($file);

        $buffer = $this->getOB($this->path . DIRECTORY_SEPARATOR . $file . '.view.php', $data);
        
        $buffer = $this->getVars($buffer);
        $buffer = $this->getEspecialVars($buffer);
        
        $buffer = $this->removeComments($buffer);

        if(!$return){
            echo $buffer;
            return '';
        }
        
        return $buffer;
    }

    public function include(string $file): void
    {
        $buffer = '';
        try{
            $buffer = $this->getOB($this->path.$file.'.inc.php');
            $buffer = $this->getVars($buffer);
            $buffer = $this->getEspecialVars($buffer);
        }catch(\Exception $er){
            $buffer = "<div class='view error'>Component error: {$er->getMessage()}</div>";
        }
        echo $buffer;
    }

}
