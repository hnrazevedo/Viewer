<?php

namespace HnrAzevedo\Viewer;

use Exception;

trait HelperTrait{
    use CheckTrait;

    protected function getOB(string $require, array $data = []): string
    {
        foreach($data as $variable => $value){
            $$variable = $value;
        }

        if(!file_exists($require)){
            throw new Exception("Impotation file does not exist:{$require} .");
        }

        $_SESSION['data'] = (!empty($data)) ? $data : $_SESSION['data'];

        ob_start();
        require($require);
        $response = ob_get_contents();
        ob_end_clean();

        $response = explode(PHP_EOL,$response);
        foreach($response as $index => $value){
            $response[$index] = ltrim($value);
        }
        
        return implode('',$response);
    }

    protected function getVars(string $buffer,string $prefix = null, ?array $values = null): string
    {
        $_SESSION['data'] = (empty($_SESSION['data'])) ? null : $_SESSION['data'];

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
            while(strstr($buffer,'{{'.$prefix.$key.'}}')){
                $buffer = str_replace('{{'.$prefix.$key.'}}', $value ,$buffer);
            }
        }
        return $buffer;
    }

    protected function replace_Object(string $buffer, object $obj, string $prefix, string $key): string
    {
        foreach($obj->get_object_vars() as $field => $val){
            
            $buffer = $this->replace_value($buffer, $val, $key.'.'.$field.'.' , $field);

            while(strstr($buffer,'{{'.$prefix.$key.'.'.$field.'}}')){
                $buffer = str_replace('{{'.$prefix.$key.'.'.$field.'}}',$val ,$buffer);
            }
        }
        return $buffer;
    }

    protected function replace_Array(string $buffer, array $array, ?string $prefix = '', ?string $key = ''): string
    {
        foreach($array as $field => $val){
            $buffer = $this->replace_value($buffer, $val, $key.'.'.$field.'.' , $field);

            while(strstr($buffer,'{{'.$prefix.$key.'.'.$field.'}}')){
                $buffer = str_replace('{{'.$prefix.$key.'.'.$field.'}}',$val,$buffer);
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