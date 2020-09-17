<?php

namespace HnrAzevedo\Viewer;

trait EspecialHelperTrait{
    use HelperTrait;

    protected function getEspecialVars(string $buffer): string
    {
        return $this->replaceEspecialVars($buffer, $this->data);
    }

    protected function replaceEspecialVars(string $buffer, array $vars, ?string $prefix = ''): string
    {
        foreach ($vars as $key => $value) {
            switch(gettype($value)){
                case 'array':
                    $buffer = $this->replaceEspecialArray($buffer, $value, $prefix, $key);
                    break;
                case 'object':
                    $buffer = $this->replaceEspecialObject($buffer, $value, $prefix, $key);
                    break;
                default:
                    $buffer = $this->replaceEspecialvalue($buffer, $value, $prefix, $key);
                    break;
            }
        }

        return $buffer;
    }

    protected function replaceEspecialValue(string $buffer, $value, ?string $prefix, string $key): string
    {
        if(gettype($value)!=='array' && gettype($value)!=='object'){
            while(strstr($buffer,'{{!! $'.$prefix.$key.' !!}}')){
                $buffer = str_replace('{{!! $'.$prefix.$key.' !!}}', $value ,$buffer);
            }
        }
        return $buffer;
    }

    protected function replaceEspecialObject(string $buffer, object $obj, ?string $prefix, string $key): string
    {
        $vars = method_exists($obj,'getVars') ? $obj->getVars() : [];
        $vars = array_merge($vars, get_object_vars($obj));
        foreach($vars as $field => $val){
            
            $buffer = $this->replaceEspecialValue($buffer, $val, $key.'.'.$field.'.' , $field);

            while(strstr($buffer,'{{!! $'.$prefix.$key.'.'.$field.' !!}}')){
                $buffer = str_replace('{{!! $'.$prefix.$key.'.'.$field.' !!}}', $obj->$field ,$buffer);
            }
        }
        return $buffer;
    }

    protected function replaceEspecialArray(string $buffer, array $array, ?string $prefix = '', ?string $key = ''): string
    {
        foreach($array as $field => $val){
            $buffer = $this->replaceEspecialValue($buffer, $val, $key.'.'.$field.'.' , $field);

            while(strstr($buffer,'{{!! $'.$prefix.$key.'.'.$field.' !!}}')){
                $buffer = str_replace('{{!! $'.$prefix.$key.'.'.$field.' !!}}', $val ,$buffer);
            }
        }
        return $buffer;
    }

}