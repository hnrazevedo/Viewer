<?php

namespace HnrAzevedo\Viewer;

trait HelperTrait{

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

    protected function getVars(string $buffer,string $prefix = null, $valuee = null): string
    {
        //Recebe os valores a serem substituidos pela session caso não seja passado via parametro

        //$vars = (!empty($valuee)) ? $valuee : unserialize($_SESSION['data']);
        $vars = (!empty($valuee)) ? $valuee : (empty($_SESSION['data']) ? null : $_SESSION['data']);

        if(empty($vars)){
            return $buffer;
        }
        foreach ($vars as $key => $value) {
            //Trabalha separadamente por tipo
            switch(gettype($value)){
                case 'string':
                case 'int':
                case 'integer':
                case 'float':
                case 'boolean':
                case 'NULL':

                    //Verifica se variável da session é solicitada no buffer
                    while(strstr($buffer,'{{ '.$prefix.$key.' }}')){
                        $buffer = str_replace('{{ '.$prefix.$key.' }}', $value ,$buffer);
                    }
                    break;
                case 'array':
                    //Faz um loop nos campos do array
                    foreach($value as $index => $val){
                        //Verifica se conteudo do indice no loop é do tipo objeto ou outro array, se for, chama a mesma função para trabalhar nele
                        if(gettype($val)==='array' or gettype($val)==='object'){

                            $buffer = $this->getVars($buffer,$key.'.'.$index.'.',$val);
                        }

                        //Caso não seja objeto ou array é trabalhado o conteudo a ser substituido
                        while(strstr($buffer,'{{ '.$prefix.$key.'.'.$index.' }}')){
                            $buffer = str_replace('{{ '.$prefix.$key.'.'.$index.' }}',$val,$buffer);
                        }
                    }
                    break;
                case 'object':
                    //Faz um loop nos campos do objeto
                    foreach($value->getFields() as $field => $val){
                        //Verifica se o campo em loop é do tipo array ou um outro objetivo, se for, chama a mesma função para trabalhar enele
                        if(gettype($val)==='array' or gettype($val)==='object'){
                            $buffer = $this->getVars($buffer,$key.'.'.$field.'.',$val);
                        }

                        //Caso não seja objeto ou array é trabalhado o conteudo do campo
                        while(strstr($buffer,'{{ '.$prefix.$key.'.'.$field.' }}')){
                            echo 1;
                            $buffer = str_replace('{{ '.$prefix.$key.'.'.$field.' }}',$val[$field] ,$buffer);
                        }
                    }
                    break;
                default:
                    break;
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
            $tpl = str_replace('.',DIRECTORY_SEPARATOR,
                substr(
                    $import,
                    strpos($import,'\'')+1,
                    strlen($import)-11
                )
            );

            if(!file_exists($this->path . DIRECTORY_SEPARATOR . $tpl . '.tpl.php')){
              $_SESSION['data'] = unserialize($_SESSION['data']);
              $_SESSION['data']['title'] = 'Arquivo tpl não localizado:';
              $_SESSION['data']['message'] = 'Import \''.str_replace(['@import(\'','\')'],'',$import).'\' não encontrado.';
              throw new \Exception('Import \''.str_replace(['@import(\'','\')'],'',$import).'\' não encontrado.');
            }

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

}