<?php

function retornar() //Botão para voltar atras
{
    /* ligação Voltar atrás */
    echo "<script type='text/javascript'>document.write(\"<a href='javascript:history.back()' class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>\");</script>
                <noscript>
                    <a href='" . $_SERVER['HTTP_REFERER'] . "‘ class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>
                </noscript>";
}

//funcao que permite connectar a base dados Nota: a password tenho duvida se nao é bitnami
function conectar()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if (!$link) {
        //Convém invocar a função mysqli_error como é feito nos slides da aula teórica para conseguir informações sobre um dado erro
        //Função imprime mensagem e sai do atual script
        die('Erro ao conectar:' . mysqli_connect_error());
    } else {
        return $link;
    }
}

//funcao que verifica se houve login
function verificaLogin($login)
{
    if (is_user_logged_in() and current_user_can($login)) {
        #echo"foi feito login <br>";
        return true;
    } else {
        #echo"nao foi feito login <br>";
        return false;
    }
}

function get_Enum_Values($tabela, $campo)
{
	$liga_db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $enum_array = array(); //construtor atribui valores a um array, tirado do diap.45
    $query      = 'SHOW COLUMNS FROM `'. $tabela .'` LIKE "' . $campo . '"'; //guarda a query numa string
    $result = mysqli_query($liga_db, $query); //envia um query única para a base de dados atualmente ativa no servidor
    $row = mysqli_fetch_row($result); //busca uma linha de dados do resultado associado com o identificador específico. A linha é retornada como um array.
	//resultado da query : value_type enum('text','bool','int','double','enum','obj_ref'NO NULL
	
	preg_match_all('/\'(.*?)\'/', $row[1], $enum_array);
	if (!empty($enum_array[1])) 
	{ //evita o erro do foreach() que ocorre se estiver vazio
        foreach ($enum_array[1] as $mkey => $mval)
            $enum_fields[$mkey + 1] = $mval; //move as key do array para enquadrar com os index, podemos usar index em vez de strings
        //echo $enum_fields[1][0] . "," . $enum_fields[1][1] . "\n";
        return $enum_fields;
    } else
       return $enum_array; // devolve um array vazio para evitar erros/warnings .
}
?>