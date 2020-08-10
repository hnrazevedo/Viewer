<?php

namespace HnrAzevedo\Viewer;

trait HelperTrait{
    use CheckTrait;

    protected function getOB(string $require): string
    {
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
                case 'string':
                case 'int':
                case 'integer':
                case 'float':
                case 'boolean':
                case 'NULL':
                    $buffer = $this->replace_value($buffer, $value, $prefix, $key);
                    break;
                case 'array':
                    $buffer = $this->replace_Array($buffer, $value, $prefix, $key);
                    break;
                case 'object':
                    $buffer = $this->replace_Object($buffer, $value, $prefix, $key);
                    break;
                default:
                    break;
            }
        }

        return $buffer;
    }

    protected function replace_value(string $buffer, $value, string $prefix, string $key): string
    {
        if(gettype($value)!=='array' && gettype($value)!=='object'){
            while(strstr($buffer,'{{ '.$prefix.$key.' }}')){
                $buffer = str_replace('{{ '.$prefix.$key.' }}', $value ,$buffer);
            }
        }
        return $buffer;
    }

    protected function replace_Object(string $buffer, object $obj, string $prefix, string $key): string
    {
        foreach($obj->getFields() as $field => $val){
            
            $buffer = $this->replace_value($buffer, $val, $key.'.'.$field.'.' , $field);

            while(strstr($buffer,'{{ '.$prefix.$key.'.'.$field.' }}')){
                $buffer = str_replace('{{ '.$prefix.$key.'.'.$field.' }}',$val[$field] ,$buffer);
            }
        }
        return $buffer;
    }

    protected function replace_Array(string $buffer, array $array, string $prefix, string $key): string
    {
        foreach($array as $field => $val){
            $buffer = $this->replace_value($buffer, $val, $key.'.'.$field.'.' , $field);

            while(strstr($buffer,'{{ '.$prefix.$key.'.'.$field.' }}')){
                $buffer = str_replace('{{ '.$prefix.$key.'.'.$field.' }}',$val,$buffer);
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

    protected function getImport(string $buffer): string
    {
        while(strstr($buffer,'@import')){
            $buffer = $this->getVars($buffer);

            $import = substr(
                $buffer,
                strpos($buffer,'@import(\''),
                strpos(strstr($buffer,'@import'),'\')')+2
            );

            $tpl = $this->check_importExist($import);

            try{
                $buffer_tpl = $this->getOB($this->path . DIRECTORY_SEPARATOR . $tpl . '.tpl.php');
            }catch(\Exception $er){
                var_dump($er);
                die();
            }
            
            $buffer_tpl = $this->getVars($buffer_tpl);
            $buffer = str_replace($import,$buffer_tpl,$buffer);
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