<?php

namespace HnrAzevedo\Viewer;

use Exception;

trait HelperTrait{
    use CheckTrait, JScriptTrait;

    protected function getOB(string $require, array $data = []): string
    {
        foreach($data as $variable => $_){
            $$variable = $_;
        }
        
        $_ = (array_key_exists('_',$data)) ? $data['_'] : null;

        if(!file_exists($require)){
            $require = basename($require);
            throw new Exception("Importation file does not exist: {$require} .");
        }
        
        $this->initData();

        $_SESSION['data'] = (!empty($data)) ? array_merge($data,$_SESSION['data']) : $_SESSION['data'];

        ob_start();
        require($require);
        $response = ob_get_contents();
        ob_end_clean();

        
        
        return $this->treatHTML($response);
    }

    private function treatHTML(string $html): string
    {
        $arrayHtml = explode(PHP_EOL,$html);
        $html = [];

        $inScript = false;
        $inComment = false;
            
        foreach($arrayHtml as $index => $value){
            $inScript = $this->checkInScript($inScript, $value);
            

            if($inScript){
                $inComment = $this->checkCommentInScript($inComment, $value);

                if(!$this->checkScriptNeed($inComment, $value)){
                    continue;
                }else{
                    $value = $this->treatScript($value);
                }
            }
                
            $html[$index] = ltrim($value);
        }
        
        return implode('',$html);
    }

    protected function initData()
    {
        $_SESSION['data'] = (empty($_SESSION['data'])) ? null : $_SESSION['data'];
    }

    protected function getVars(string $buffer,string $prefix = null, ?array $values = null): string
    {
        $this->initData();

        $vars = (is_null($values)) ? $_SESSION['data'] : $values;

        return (is_null($vars)) ? $buffer : $this->replace_vars($buffer, $vars, $prefix);
    }

    protected function replace_vars($buffer, $vars, $prefix): string
    {
        foreach ($vars as $key => $value) {
            switch(gettype($value)){
                case 'array':
                    $buffer = $this->replace_Array($buffer, $value, $prefix, $key);
                    break;
                case 'object':
                    $buffer = $this->replace_Object($buffer, $value, $prefix, $key);
                    break;
                default:
                    $buffer = $this->replace_value($buffer, $value, $prefix, $key);
                    break;
            }
        }

        return $buffer;
    }

    protected function replace_value(string $buffer, $value, ?string $prefix, string $key): string
    {
        if(gettype($value)!=='array' && gettype($value)!=='object'){
            while(strstr($buffer,'{{ $'.$prefix.$key.' }}')){
                $buffer = str_replace('{{ $'.$prefix.$key.' }}', htmlspecialchars($value) ,$buffer);
            }
        }
        return $buffer;
    }

    protected function replace_Object(string $buffer, object $obj, string $prefix, string $key): string
    {
        foreach($obj->get_object_vars() as $field => $val){
            
            $buffer = $this->replace_value($buffer, $val, $key.'.'.$field.'.' , $field);

            while(strstr($buffer,'{{ $'.$prefix.$key.'.'.$field.' }}')){
                $buffer = str_replace('{{ $'.$prefix.$key.'.'.$field.' }}', htmlspecialchars($val) ,$buffer);
            }
        }
        return $buffer;
    }

    protected function replace_Array(string $buffer, array $array, ?string $prefix = '', ?string $key = ''): string
    {
        foreach($array as $field => $val){
            $buffer = $this->replace_value($buffer, $val, $key.'.'.$field.'.' , $field);

            while(strstr($buffer,'{{ $'.$prefix.$key.'.'.$field.' }}')){
                $buffer = str_replace('{{ $'.$prefix.$key.'.'.$field.' }}', htmlspecialchars($val) ,$buffer);
            }
        }
        return $buffer;
    }

    protected function removeComments(string $buffer): string
    {
        while(strstr($buffer,'<!--')){
            $comment = substr(
                $buffer,
                strpos($buffer,'<!--'),
                strpos(strstr($buffer,'<!--'),'-->')+3
            );
            $buffer = str_replace($comment,'',$buffer);
        }
        return $buffer;
    }

    protected function saveData(): bool
    {   
        if(session_status() !== PHP_SESSION_ACTIVE){
            return false;
        }
        unset($_SESSION['data']);

        if(!empty($_SESSION['save'])){
            foreach ($_SESSION['save'] as $key => $value) {
                $_SESSION['data'][$key] = $value;
            }
        }
        return true;
    }

}