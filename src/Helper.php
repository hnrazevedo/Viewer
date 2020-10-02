<?php

namespace HnrAzevedo\Viewer;

trait Helper
{
    use JScriptTrait;
    
    protected string $treat = '';
    protected bool $especial = true;
    protected array $data = [];

    protected function getOB(string $require, ?array $data = []): string
    {
        $this->data = array_merge($this->data, $data);

        foreach($this->data as $variable => $_){
            $$variable = $_;
        }
        
        $_ = (isset($data['_'])) ?? $data['_'];

        if(!file_exists($require)){
            $require = basename($require);
            throw new \RuntimeException("Importation file does not exist: {$require}");
        }

        ob_start();
        require($require);
        $response = ob_get_contents();
        ob_end_clean();
       
        return $this->treatHTML($response);
    }

    private function treatHTML(string $html): string
    {
        $arrayHtml = explode(PHP_EOL, $html);
        $html = [];

        $inScript = false;
        $inComment = false;
            
        foreach($arrayHtml as $index => $value){
            $inScript = $this->checkInScript($inScript, $value);
            
            if($inScript){
                $inComment = $this->checkCommentInScript($inComment, $value);

                if(!$this->checkScriptNeed($inComment, $value)){
                    continue;
                }
                
                $value = $this->treatScript($value);
            }
                
            $html[$index] = ltrim($value);
        }
        
        return implode('',$html);
    }

    protected function getVars(string $buffer, ?bool $treat = true): string
    {
        $this->treat = ($treat) ? '' : '!!';
        $this->especial = $treat;
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
                    $buffer = (is_scalar($value)) ? $this->replaceValue($buffer, $value, $prefix, $key) : $buffer;
                    break;
            }
        }

        return $buffer;
    }

    protected function replaceValue(string $buffer, $value, ?string $prefix, string $key): string
    {
        if(is_scalar($value)){
            while(strstr($buffer, "{{{$this->treat} \${$prefix}{$key} {$this->treat}}}")){
                $buffer = str_replace("{{{$this->treat} \${$prefix}{$key} {$this->treat}}}", (($this->especial) ? htmlspecialchars($value) : $value) ,$buffer);
            }
        }

        return $buffer;
    }

    protected function replaceObject(string $buffer, object $obj, ?string $prefix, string $key): string
    {
        $vars = method_exists($obj,'getVars') ? $obj->getVars() : [];
        $vars = array_merge($vars, get_object_vars($obj));

        foreach($vars as $field => $val){
            $buffer = $this->replaceValue($buffer, $val, $key.'.' , $field);


            $buffer = $this->checkArray($buffer, $obj->$field, $key.'.'.$field.'.');
            $buffer = $this->checkObject($buffer, $obj->$field, $key.'.'.$field.'.',  $key.'.'.$field);

            while(strstr($buffer, "{{{$this->treat} \${$prefix}{$key}.{$field} {$this->treat}}}")){
                $buffer = str_replace("{{{$this->treat} \${$prefix}{$key}.{$field} {$this->treat}}}", (($this->especial) ? htmlspecialchars($obj->$field) : $obj->$field) ,$buffer);
            }
            
        }
        return $buffer;
    }

    private function checkArray(string $buffer, $obj, string $prefix): string
    {
        if(is_array($obj)){
            $buffer = $this->replaceVars($buffer, $obj, $prefix);
        }
        return $buffer;
    }

    private function checkObject(string $buffer, $obj, string $prefix, string $key): string
    {
        if(is_object($obj)){
            $buffer = $this->replaceObject($buffer, $obj, $prefix, $key);
        }
        return $buffer;
    }

    protected function replaceArray(string $buffer, array $array, ?string $prefix = '', ?string $key = ''): string
    {
        foreach($array as $field => $val){
            $buffer = $this->replaceValue($buffer, $val, $key.'.', $field);

            while(strstr($buffer, "{{{$this->treat} \${$prefix}{$key}.{$field} {$this->treat}}}")){
                $buffer = str_replace("{{{$this->treat} \${$prefix}{$key}.{$field} {$this->treat}}}", (($this->especial) ? htmlspecialchars($val) : $val) ,$buffer);
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
