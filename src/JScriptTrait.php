<?php

namespace HnrAzevedo\Viewer;


trait JScriptTrait{

    protected function checkInScript(bool $inScript, string $value): bool
    {
        if((substr(ltrim($value),0,8) === '<script>' && !strpos($value,'src'))){
            $inScript = true;
        }
        if((substr(ltrim($value),0,9) === '</script>')){
            $inScript = false;
        }
        return $inScript;
    }

    protected function checkCommentInScript(bool $inComment, string $value): bool
    {
        if(strpos($value,'/*') && !strpos($value,'*/')){
            $inComment = true;
        }else{
            $inComment = (strpos($value,'*/')) ? false : $inComment;
        }
        return $inComment;
    }

    protected function checkScriptNeed(bool $inComment, string $value): bool
    {
        if(($inComment || strpos($value,'//'))){
            return false;
        } 
        return true;
    }

    protected function treatScript(string $value): string
    {
        while(  strpos($value,'/*') && strpos($value,'*/')  ){
            $replace = substr($value,strripos ($value,'/*'),strripos ($value,'*/')+2);
            $value = str_replace($replace,'',$value);
        }
        if(strpos($value,'*/')){
            $value = substr($value,strpos($value,'*/')+2);
        }
        return $value;
    }

}