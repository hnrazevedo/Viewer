<?php

namespace HnrAzevedo\Viewer;

use Exception;

trait HelperTrait{
    use CheckTrait, JScriptTrait;

    public array $data = [];

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

        $this->data = array_merge($this->data,$data);

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

    protected function getVars(string $buffer): string
    {
        return $this->replaceVars($buffer, $this->data);
    }

    protected function replaceVars(string $buffer, array $vars, ?string $prefix = ''): string
    {
        foreach ($vars as $key => $value) {
            switch(gettype($value)){
                case 'array':
                    $buffer = $this->replaceArray($buffer, $value, $prefix, $key);
                    break;
                case 'object':
                    $buffer = $this->replaceObject($buffer, $value, $prefix, $key);
                    break;
                default:
                    $buffer = $this->replaceValue($buffer, $value, $prefix, $key);
                    break;
            }
        }

        return $buffer;
    }

    protected function replaceValue(string $buffer, $value, ?string $prefix, string $key): string
    {
        if(gettype($value)!=='array' && gettype($value)!=='object'){
            while(strstr($buffer,'{{ $'.$prefix.$key.' }}')){
                $buffer = str_replace('{{ $'.$prefix.$key.' }}', htmlspecialchars($value) ,$buffer);
            }
        }
        return $buffer;
    }

    protected function replaceObject(string $buffer, object $obj, string $prefix, string $key): string
    {
        foreach(get_object_vars($obj) as $field => $val){
            
            $buffer = $this->replaceValue($buffer, $val, $key.'.'.$field.'.' , $field);

            while(strstr($buffer,'{{ $'.$prefix.$key.'.'.$field.' }}')){
                $buffer = str_replace('{{ $'.$prefix.$key.'.'.$field.' }}', htmlspecialchars($obj->$field) ,$buffer);
            }
        }
        return $buffer;
    }

    protected function replaceArray(string $buffer, array $array, ?string $prefix = '', ?string $key = ''): string
    {
        foreach($array as $field => $val){
            $buffer = $this->replaceValue($buffer, $val, $key.'.'.$field.'.' , $field);

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

}