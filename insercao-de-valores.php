<!DOCTYPE html>
<html>
<?php
// NOTA: o request é um array associativo que contem os metodos POST, GET,cookies
// sao acedidos os valores de um form, o uso de get ou post provem do method do form
// <li> permite definir um item da lista, onde o <ul>  permite criar sublistas( ou um menu)
// session guarda o valor do array request, que devolve o id pois inserimos no link o respetivo id

require_once("custom/php/common.php");
$entrou  = verificaLogin('insert_values'); //verifica a capability e se fez o login
$conecta = conectar(); //conecta à BD

if ($entrou == true)
    {
    // aqui verifico se o estado é null usando o request
    // o request acede ao formulario, e onde o name=estado verifica e será usado o metodo POST ou GET,
    // estes que sao definidos no fromulario em method=...
	
    if (!$_REQUEST["estado"]) //se for null
        {
        echo "<h3><i>Inserção de valores - escolher objeto/formulário customizado</i></h3>";
        // criar uma query que devolva os tuplos da tabela  obj_type ordenados alf        
        $tipo_obj      = "select * from obj_type order by name"; //guardamos a query na variavel ordenada alfab.
        $res_lista_obj = mysqli_query($conecta, $tipo_obj); // fazemos a query com a variavel anterior
        
        //----- Apresentar uma lista dos tipos de objetos existentes na BD, apresenta a info se tiver, caso contrario o seg.-----
        echo "<ul style='list-style-type:circle'><li><b>Objetos:</b></li><ul>"; //englobante
        if (mysqli_num_rows($res_lista_obj) == 0) //se nao houverem tuplos
            {
            echo "Não há existem tipos de objeto a mostrar<br />"; //mostramos msg de erro
            }
        else //caso hajam tuplos
            {
            // vamos mostrar os objectos       
            while ($lista_obj = mysqli_fetch_assoc($res_lista_obj)) //guarda mnum array associativo os valores da retornados da bd
                {
                
                // desses valores queremos mostrar os apontados pela key (name)                
                echo "<ul style='list-style-type:square'><li><b>". $lista_obj['name'] . "</b></li><ul style='list-style-type:square'>"; //3ºul  //mostra cada itereacao o nome do tipo de objeto do tuplo dessa pos do array
                
				//------dos tipos de obj entao acedemos qual o objecto que pertence a esse tipo------
                // obtemos todos os nomes da tabela object,onde comparamos a foreign key da
                // tabela obj_type (id´s ) com os id´s da tabela object (.$lista_obj['id'] da query anterior) 
                $obj_name     = "select * from object where object.obj_type_id='" . $lista_obj['id'] . "' ORDER BY name";
                $res_tipo_obj = mysqli_query($conecta, $obj_name);
                if (mysqli_num_rows($res_tipo_obj) == 0) //se nao houverem tuplos
                    {
                    echo "Não há existem objeto a mostrar<br />";
                    }
                else
                    {
                    // enquanto houverem tuplos guardamos na variavel                    
                    while ($atrib_get = mysqli_fetch_assoc($res_tipo_obj))
                        {
                        
                        //criamos uma ligacao para o endereço indicado no moodle, onde $atrib_get['id'] é o id de um objt com o nome ($atrib_get['name'])                        
                        echo "<li> <a href='insercao-de-valores?estado=introducao&obj=" . $atrib_get['id'] . "'>[" . $atrib_get['name'] . "]</a></li>";
                        }
                    
                    echo "</ul>"; // 1º<ul>
                    }
					echo "</ul>";
                }
            
            echo "</ul></ul>"; //2º e 3º<ul>
            } //acaba os objectos
        
        //--Formularios
        // acedemos a todos os formularios da tabela custom_form (POR ORDEM ALFABETICA)
        $formulario        = "select * from custom_form order by name";
        $result_formulario = mysqli_query($conecta, $formulario);
        
        if (mysqli_num_rows($result_formulario) == 0) //nao existem tuplos
            {
            echo "Não existem formulários costumizados para apresentar<br />";
            }
        else //caso haja formularios
            {
            echo "<ul style='list-style-type:circle'><li><b>Formulários customizados :</b></li><ul>"; //marca redodnda ext
            echo "<ul style='list-style-type:square'> "; //marca quadr.
            
            // enquanto houverem tuplos guardamos na variavel
            while ($form_res = mysqli_fetch_assoc($result_formulario))
                {
                
                // mostramos os tuplos com uma ligacao para o endereço indicado no moodle,$form_res['id'] é o id do formulario com o nome ($form_res['name'])
                
                echo "<li><a href='insercao-de-valores?estado=introducao&form=" . $form_res['id'] . "'>[" . $form_res['name'] . "]</a></li>";
                }
            
            echo "</ul></ul></ul></ul>"; //3º <ul> e englobante
            } //fecha o else
        } //se for estado == null, end
    
    // quando o valor de estado for introducao
    else if ($_REQUEST["estado"] == "introducao")
        {
        
        // temos de criar variaveis de sessao onde guardamos nas variaveis o valor retornado pelo Request
        //-----------------------------------------------
        // para os obj
        $_SESSION['obj_id '] = $_REQUEST['obj']; //session guarda o valor do array request obj do href
        $valor_obj_id        = $_SESSION['obj_id ']; // echo $_SESSION['obj_id '];
        
        //Para os formularios
        $_SESSION['form_id'] = $_REQUEST['form'];
        $form_s_id           = $_SESSION['form_id']; //echo $_SESSION['form_id'];
        //-----------------------------------------------
        
        if ($valor_obj_id != "") //---------------------------------Objectos
        //se o obj_id tiver valor            
            {
            //quermos saber quais os tuplos correspondentes a sessao escolhida
            $query_object        = "select * from object where object.id='$valor_obj_id' "; //guardamos a instrucao numa variavel
            $result_query_object = mysqli_query($conecta, $query_object); //fazemos a query
            $query_obj           = mysqli_fetch_assoc($result_query_object); // guarda na variavel q é um array assoc os tuplos
            
            $_SESSION['obj_name']    = $query_obj['name']; // busca no array assc com a key (name( o nome do obj
            $_SESSION['obj_type_id'] = $query_obj['obj_type_id']; // busca no array assc a key (obj_type) o obj type id
            
			// echo $_SESSION['obj_name'];//verificar o valor de output
            // as variaveis sao usadas para evitar conflitos das variaveis de sessao
            
			$var_obj_name            = $_SESSION['obj_name']; //variavel com o obj_name da ocao escolhida
            $var_obj_type            = $_SESSION['obj_type_id']; //variavel com o obj_type_id da ocao escolhida
            // unset ou session_destroy, ou session_destroy destroi todas as variaveis de sessao
            
            echo "<h3>Inserção de valores - $var_obj_name </h3><br />"; // mostra insercao de valores seguido do nome do obj selecionado
            echo "<form id='insercao' name=obj_type_" . $var_obj_type . "_obj_" . $valor_obj_id . " action=/insercao-de-valores?estado=validar&obj=" . $valor_obj_id . " method=POST><br />";
            // echo "name=obj_type_"."$var_obj_type"."_obj_"."$valor_obj_id";
            
			//saber quais os atributos relacionado com os tipos de obj
            $query_dual   = "select * from attribute where attribute.obj_id = '$valor_obj_id' and attribute.state ='active'"; //dupla pk vou fazer comum para os dois
			// echo $query_dupla;
            $result_atrib = mysqli_query($conecta, $query_dual);
            
            } //caso do obj
        //----------------------------------------------------------Formularios        
        else
            {
            $query_f_name          = "select * from custom_form where id = '$form_s_id'"; // guarda a instrucao para fazaer a query        
            // executa a query        
            $result_f_name         = mysqli_query($conecta, $query_f_name);
            $form_n                = mysqli_fetch_assoc($result_f_name); //guarda os tuplos na varivel
            
			$_SESSION['form_name'] = $form_n['name']; //guarda na variavel de session o valor retornado pelo campo acedido name
            $var_form_name         = $_SESSION['form_name'];
            
            
            //print mas para o form com a alt no href
            echo "<h3>Inserção de valores - $var_form_name </h3><br/>"; // mostra insercao de valores seguido do nome do form selecionado
            // cria um formulario com o nome e tipo de obj com o submit para validar
            echo "<form id='insercao' name=form_$var_form_name action=/insercao-de-valores?estado=validar&form=$form_s_id method=POST><br />";
            //quero saber os tuplos de atrribute e de custom_form_has_attribute, esta query tava incompleta        
            $query_dual   = "select * from attribute ,custom_form_has_attribute where custom_form_has_attribute.custom_form_id = $form_s_id and attribute.id = custom_form_has_attribute.attribute_id and attribute.state ='active'";
            // echo $query_dupla;
            $result_atrib = mysqli_query($conecta, $query_dual);
            
            } //fecha o else
        
        while ($query = mysqli_fetch_assoc($result_atrib))
            {
			//echo $query['id']."<br>";
			$tipo_unidade_temp = $query['value_type'];
			//echo $tipo_unidade_temp;

			//indicar campos obrigatorios
		    if ($query['mandatory'])
			{
            //mudei para query pk serve agora as duas situacoes form e obj
                echo "<fieldset><legend>" . $query['name'] . "  <font color='red'>(obrigatório)</font> </legend>";
                //criamos as bordas e o nome com indicaçao obrigati«orio
            }
            else
            {
                echo "<fieldset><legend>" . $query['name'] . "</legend>"; //criamos as bordas e o nome
            }
			
            // agora vemos quais tipo de unidade de cada atributo, verificando qual tem o id com a FK da tabela atribute
            $sql_unit_type        = "select * from attr_unit_type where attr_unit_type.id=" . $query['unit_type_id'] . ""; //query com uint type
            $result_sql_unit_type = mysqli_query($conecta, $sql_unit_type); //fazemos a query
            $tipo_de_unidade      = mysqli_fetch_assoc($result_sql_unit_type); //temos o array com os tuplos
			
            // Para cada value_type criamos uma opcao de execucao
            switch ($tipo_unidade_temp)
            {
				case 'enum': 
					// se for enum, temos que apresentar um input do tipo radio ou checkbox ou selectbox, referente ao tipo de campo especificado na BD
                    // procurar na tabela attr_allowed_value, qual o attribute id que corresponde ao id da tabela attribute de modo a retornar os enum´s
					
                   $attr_allowed        = "select * from attr_allowed_value where attr_allowed_value.attribute_id='". $query['id'] ."'";
				   //echo $attr_allowed;
                   $result_attr_allowed = mysqli_query($conecta, $attr_allowed); //fazemos a query
				 // echo $attr_allowed;
				   
                    // se for uma selectbox
                    if ($query['form_field_type'] == 'selectbox')
                        {
                        echo "<select name=" . $query['form_field_name'] . ">";
						echo "<option value=NULL></option>";
                        while ($t_attr_allowed = mysqli_fetch_assoc($result_attr_allowed))
                            {
                            echo "<option value=" . $t_attr_allowed['value'] . "> " . $t_attr_allowed['value'] . "</option>";
                            }
                        echo "</select>";
                        }
                    
					else if($query['form_field_type'] == 'checkbox')
						{
                        while ($t_attr_allowed = mysqli_fetch_assoc($result_attr_allowed))
                            {
                            echo "<input type='".$query['form_field_type']."' name=".$query['form_field_name']." value='" . $t_attr_allowed['value'] . "'> " . $t_attr_allowed['value'] . "<br />";
							}
						}
					else
						{
                        while ($t_attr_allowed = mysqli_fetch_assoc($result_attr_allowed))
                            {
                            echo "<input type='".$query['form_field_type']."' name=".$query['form_field_name']." value='" . $t_attr_allowed['value'] . "'> " . $t_attr_allowed['value'] . "<br />";
							}
						}
                    break;
				
                // para o texto
                case 'text':
                    // Se for uma linha simples de texto
                    if ($query['form_field_type'] == 'text')
                        {
                        // o input tag especifica onde o user pode inserir dados                            
                        echo "<input type = 'text' name=" . $query['form_field_name'] . ">";
                        echo $tipo_de_unidade['name'];
                        echo "<br />";
                        }
                    // se for uma caixa de texto neste caso
                    else
                        {
                        // o tag <textarea> define uma multi-line de text .ela pode ter um num. ilimitedo de characters, com um fixed-width font
                        echo "<textarea name=" . $query['form_field_name'] . "cols=20 rows=4>Inserir</textarea>"; //os row e cols tenho que colocar uma solucao mais dinamica 4*5=20 opcao
                        echo "<br />";
                        }
                    break; //fim text
                
                case 'bool': // se for booleano, coloca o nome do form_field_name
                    
                    // se for true
                    echo "<input type='radio' name=" . $query['form_field_name'] . " value='1'> Verdadeiro<br />";
                    // se for false
                    echo "<input type='radio' name=" . $query['form_field_name'] . " value='0'> Falso<br />";
                    break;
					
                case 'int':
					echo "<input type = 'text' name=" . $query['form_field_name'] . "> "; //usei type = 'text' pk o munmber só aceita inteiros e nao floats
                    if ($query['unit_type_id'] != "") 
                        {
                        
                        // vai buscar os nomes da tab attr_unit_type associados aos tabela attribute com o atributo unit_type_id
                        
                        $int_or_doub        = "select * from attr_unit_type where attr_unit_type.id=" . $query['unit_type_id'] . ""; //instrucao
                        $result_int_or_doub = mysqli_query($conecta, $int_or_doub); //fazemos a query
                        $valor              = mysqli_fetch_assoc($result_int_or_doub); //guardamos o valor
                        echo " <i>" . $valor['name'] . "</i>"; //mostramos o valor apontado pela key name indicando o tipo de unidade relativo
                        }
                    echo "<br />"; //break line
                    break;
					
				case 'double': // se for int OU double o input pedido é do tipo text, facilita um switch, como a resoluçao oé igual
                    
                    echo "<input type = 'text' name=" . $query['form_field_name'] . "> "; //usei type = 'text' pk o munmber só aceita inteiros e nao floats
                    if ($query['unit_type_id'] != NULL)
                        {
                        
                        // vai buscar os nomes da tab attr_unit_type associados aos tabela attribute com o atributo unit_type_id
                        
                        $int_or_doub        = "select * from attr_unit_type where attr_unit_type.id=" . $query['unit_type_id'] . ""; //instrucao
                        $result_int_or_doub = mysqli_query($conecta, $int_or_doub); //fazemos a query
                        $valor              = mysqli_fetch_assoc($result_int_or_doub); //guardamos o valor
                        echo " <i>" . $valor['name'] . "</i>"; //mostramos o valor apontado pela key name indicando o tipo de unidade relativo
                        }
                    echo "<br />"; //break line
                    break;
                
                // se for obj_ref apresentamos um input do tipo selectbox em que as opções são obtidas através de uma query à BD, de modo a obter a lista de tuplos
                case 'obj_ref':
                    
                    // tuplos onde obj_ref referencia o id da tabela object, onde o atributo obj_fk_id da tabela attribute
                    $query_obj_ref  = "select * from obj_inst where obj_inst.object_id=". $query['obj_fk_id']."";
                    $result_obj_ref = mysqli_query($conecta, $query_obj_ref);
                    echo "<select name=" . $query['form_field_name'] . ">"; //craimos a cx de selecao
                    echo "<option value=NULL> ----- </option>"; //apresenta a select box com ---- desativado, o resto em dropdown
                    while ($object_ref = mysqli_fetch_assoc($result_obj_ref))
                        {
                        echo "<option value=" . $object_ref['id'] . "> " . $object_ref['object_name'] . "</option>"; //mostra o resto da opcoes do drop down
                        }
                    
                    echo "</select><br />";
                    break;

            } //fim do switch
            
			echo "</fieldset>"; //fecha a marca externa //borda
            
            // criamos uma caixa de titulo Nome objeto:
			//obj_inst_name vai ser usado no inserir pelo request
			
            } //fecha o while 
			
			if($valor_obj_id != NULL)
			{
				echo " <fieldset><legend> Nome objeto : </legend>
                  <input type='text' name=inst method=POST>(opcional)<br />
                  </fieldset>"; //outra box com opcional à frente
			}
			else
			{
				echo " <fieldset><legend> Nome do Formulario : </legend>
				<input type='text' name=inst method=POST>(opcional)<br /></fieldset>"; //outra box com opcional à frente
			}
			
			echo "<input type='hidden' name='estado' value='validar' >
				<input type='submit' value='Submeter'>"; //cria o botao submit
            
            retorna(); //return 

			 echo "</form>";
		} 
		//se for estado == introducao, end
		
    else if ($_REQUEST["estado"] == "validar") // estado == validar
        {
			
        $_SESSION['obj_id'] = $_REQUEST['obj']; //session guarda o valor do array request obj do href
        // echo $_SESSION['obj_id'];
        $new_var_obj_id       = $_SESSION['obj_id'];
		
		$_SESSION['form_id'] = $_REQUEST['form'];
        $form_s_id           = $_SESSION['form_id']; //echo $_SESSION['form_id'];
		
		if($new_var_obj_id != "")
		{
        $query_name           = "select * from object where object.id= $new_var_obj_id "; //guardamos a instrucao numa variavel
        $result_name          = mysqli_query($conecta, $query_name); //excutamos a query
        $query_obj            = mysqli_fetch_assoc($result_name); //guarda na variavel q é um array assoc os tuplos
        
		$_SESSION['obj_name'] = $query_obj['name']; //busca no array assc com a key name o nome do obj
        $new_var_obj_name     = $_SESSION['obj_name'];
		
        
        echo "<h3>Inserção de valores - $new_var_obj_name - validar</h3>"; //apresentar o sub-titulo com o valor de sessao
        // procuramos na tabela atributo onde o id da sessao é igual ao do objeto e o estado seja ativo
        
        $sql_atrib = "select * from attribute where attribute.obj_id ='$new_var_obj_id' and attribute.state ='active'";
        //echo $sql_atrib;
        $result_sql_atrib   = mysqli_query($conecta, $sql_atrib); //executar a query
        $nome_inserido_form = $_REQUEST['inst'];//vai buscar o valor do nome inserido no formulario anterior
     	
		$opcional= "Nome do Objeto :";
        //echo $nome_inserido_form."<br>";		
		//     apresentar uma lista com os dados preenchidos no estado anterior (só para o utilizador ler, não é possível a edição)
        echo "<form action=/insercao-de-valores?estado=inserir&obj=$new_var_obj_id&inst_name=$nome_inserido_form method=POST>";      	
		}
		
		else
		{
			$query_f_name          = "select * from custom_form where id = '$form_s_id'"; // guarda a instrucao para fazaer a query              
            $result_f_name         = mysqli_query($conecta, $query_f_name);
            $form_n                = mysqli_fetch_assoc($result_f_name); //guarda os tuplos na varivel
            
			$_SESSION['form_name'] = $form_n['name']; //guarda na variavel de session o valor retornado pelo campo acedido name
            $var_form_name         = $_SESSION['form_name'];
			
            $nome_inserido_form    = $_REQUEST['inst'];
            
            //print mas para o form com a alt no href
            echo "<h3>Inserção de valores - $var_form_name - validar </h3><br/>"; // mostra insercao de valores seguido do nome do form selecionado
           
            //quero saber os tuplos de atrribute e de custom_form_has_attribute, esta query tava incompleta        
            $sql_atrib   = "select * from attribute ,custom_form_has_attribute where custom_form_has_attribute.custom_form_id = $form_s_id and attribute.id = custom_form_has_attribute.attribute_id and attribute.state ='active'";
            $result_sql_atrib = mysqli_query($conecta, $sql_atrib);
			$nome_inserido_form = $_REQUEST['inst'];
			$opcional= "Nome do Formulario";
			//echo $nome_inserido_form."<br>";
			echo "<form action=/insercao-de-valores?estado=inserir&form=$form_s_id&form_name=$var_form_name&inst_name=$nome_inserido_form method=POST><br >";
		}
		$obrigatorio = true; //true indica que os campos obrigatorios foram preenchidos
        while ($atrib = mysqli_fetch_assoc($result_sql_atrib)) //enquato houver tuplos
            {
				$compara = trim($_REQUEST[$atrib['form_field_name']]);
				//echo $compara."<br>";
					//var_dump($compara);
				if (strlen($compara)==0) //vemos se é null o valor que form_field_name aponta, acedida nesse formulario pelo request
				{
					if($atrib['mandatory'] == 1)
					{
						// var_dump($compara);
						//se o atributo mandatory for 1, como é int 1 indica sim 0 nao
									
						echo $obrigatorio;
                        echo "<br />";
						// o    Caso algum campo obrigatório não tenha sido preenchido apresentar mensagem de erro indicando que é obrigatório
						// o preenchimento do campo (mostrar o nome do atributo) e apresentar ligação para voltar atrás.
										
						echo "É obrigatório o preenchimento do campo '" . $atrib['name'] . "'<br />";
                       // echo $_REQUEST[$atrib['form_field_name']];  echo "<br />";
                        //echo $atrib['form_field_name'];  echo "<br />";

						// echo "Por Favor prima em ".retorna()." e preencha os campos obrigatórios.<br />"; //verificar se persite o erro, se sim por fora do qhile
										
						$obrigatorio = false; //indica q nao foram preenchidos todos os campos nao foram preenchido devolve um false
                        echo $obrigatorio;
					}
					else
					{
						echo "Por favor insira algum elemento, os campos encontram-se vazios";
						echo "<br />";
						$obrigatorio = false; //indica q nao foram preenchidos todos os campos nao foram preenchido devolve um false
					}
				}				
				else 
				{
					if($atrib['value_type'] =='int')
					{
					//check if int
						if(!preg_match('/^[0-9]+$/',$compara))
						{
							echo "O campo <b>".$atrib['name']."</b> só permite números inteiros.<br>";
							//echo $atrib['form_field_name'];
							$obrigatorio = false;
						}
					}
					else if($atrib['value_type'] =='double')
					{
						//check if double
						if(!preg_match('/^[0-9]+[,.][0-9]+$/',$compara))
						{
							echo "O campo <b>".$atrib['name']."</b> só permite números decimais.<br>";
							//echo $atrib['form_field_name'];
							$obrigatorio = false;
						}
					}
					else if($atrib['value_type'] =='text')
					{
						if(!preg_match('/^[a-zA-Z0-9]+$/',$compara))
						{
							echo "O campo <b>".$atrib['name']."</b> só permite letras, números.<br>";
                            //echo $atrib['form_field_name'];
							$obrigatorio = false;
						}
					}
				}
			}
            //entao 
			$val_inseridos = array();
			
            if ($obrigatorio) //todos os campos foram preenchidos
			{
				echo "Estamos prestes a inserir os dados abaixo na base de dados. Confirma que os dados estão corretos e pretende submeter os mesmos?<br />";
				echo "<br>";
				$result_sql_atrib = mysqli_query($conecta, $sql_atrib);
				
				while ($atrib = mysqli_fetch_assoc($result_sql_atrib)) //enquato houver tuplos
				{
					// apresenta a lista
					$id_val_inserido 	 = $atrib['id'];
					//echo $id_val_inserido."<br>";
					
					$id_attr_temp 		 = $atrib['obj_id']; 
					$nome_attr_temp      = $atrib['name'];
					$nome_camp_attr_temp = $atrib['form_field_name'];
					$tipo_valores 		 = $atrib['value_type'];
					$tipo_camp_attr_temp = $atrib['form_field_type'];
					$req_field_name      = $_REQUEST[$atrib['form_field_name']]; // verificar se funciona corretamente 
					
					//coloca os valores previamente inseridos num array com o seu id como chave identificadora
					
					echo "<input type='hidden' name=$nome_camp_attr_temp value=$tipo_camp_attr_temp >";
					echo " $nome_attr_temp : <br />";
					
					if ($atrib['value_type'] == 'enum') //se ovalue type for enum
						{
						if ($atrib['form_field_type'] == 'selectbox') //se o valor do enum for  selectbox
							{
								
								$query_selectbox        = "select * from attr_allowed_value where value ='$req_field_name'";
								$result_query_selectbox = mysqli_query($conecta, $query_selectbox);
								$allowed_selectbox      = mysqli_fetch_assoc($result_query_selectbox);
								echo $allowed_selectbox['value'];
								$val_inseridos[$id_val_inserido] = $allowed_selectbox['value'];
							}
						else if ($atrib['form_field_type'] == 'checkbox') //se o valor do enum for  checkbox
							{
								// para receber os valores das checkboxes que foram checked como um array numerico
								//echo $req_field_name;
								
								$query_checkbox        = "select * from attr_allowed_value where value ='$req_field_name'"; 
								$result_query_checkbox = mysqli_query($conecta, $query_checkbox); //faz a query
								$allowed_checkbox = mysqli_fetch_assoc($result_query_checkbox); //guarda num aarray assoc
								echo $allowed_checkbox['value'];
								$val_inseridos[$id_val_inserido] = $allowed_checkbox['value'];
								
							}
						else if ($atrib['form_field_type'] == 'radio') //se o valor do enum for do tipo radio
							{
							$query_radio        = "select * from attr_allowed_value where value ='$req_field_name'";
							//echo $query_radio;
							$result_query_radio = mysqli_query($conecta, $query_radio);
							$allowed_radio      = mysqli_fetch_assoc($result_query_radio);
							echo $allowed_radio['value'];
							$val_inseridos[$id_val_inserido] = $allowed_radio['value'];
							}
						}
						else if ($atrib['value_type'] == 'bool') //se o valor do enum for do tipo booleano
							{
							// verificamos se é false  ou true
							if ($req_field_name == 0)
								{
								echo "Falso <br>";
								$val_inseridos[$id_val_inserido] = $req_field_name;
								}
							else
								{
								echo "Verdadeiro <br>";
								$val_inseridos[$id_val_inserido] = $req_field_name;
								}
							}
						else if ($atrib['value_type'] == 'obj_ref') //se o valor do enum for do tipo obj_ref
							{
							
							// queremos todos os valores de obj_inst onde o id é igual ao inserifo no form onde o qual o request tem accesso atraves .$_REQUEST[$atribut['form_field_name']].
							
							$query_obj_ref   = "select * from obj_inst where id=$req_field_name"; //instrucao que sera usada para fazer a query
							//echo $query_obj_ref."<br>";
							$result_obj_ref  = mysqli_query($conecta, $query_obj_ref); //faz a query
							$allowed_obj_ref = mysqli_fetch_assoc($result_obj_ref);
							echo  $allowed_obj_ref['object_name'];
							$val_inseridos[$id_val_inserido] = $allowed_obj_ref['object_name'];
							}
						else
							{
							echo $req_field_name;//permite passar os valores na verificaçao sem edicao
							$val_inseridos[$id_val_inserido] = $req_field_name;
							}
						 echo "<br />";
						 echo "<br />";
					}
					echo $opcional."<br>" ;
					if(empty($nome_inserido_form))
					{
					echo "--Valor opcional nao inserido--";
					}
					else
					{
					echo $nome_inserido_form."<br>" ;
					}
					//print_r($val_inseridos);
					$string_array = json_encode($val_inseridos);
					//echo $string_array;
					
					echo "<input type=hidden name='valores_inseridos' value=$string_array ><br />";
					
					echo "<input type=hidden name='estado' value='inserir'><br />";
					echo "<input type=submit value=Submeter><br />";
							
					echo "</form>"; //cria o botao submter
					
			} //final do if
			else// caso ocorra o erro acima mencionado
            {
				echo"<br/>";
				echo "Por Favor prima em ";
				retornar();
				echo " e preencha os campos obrigatórios ou introduza corretamente os inputs.<br />"; //verificar se persite o erro
			}
		}
		
    // se for estado == validar, end
	
    // se o estado inserir
    else if ($_REQUEST["estado"] == "inserir")
        {
		
		//o array é passado codigicado no formato json
		$val_anteriores_inseridos = $_REQUEST['valores_inseridos'];
		//é feita a remoçao das barras devido a transiçao de pagina
		$val_anteriores_descod = stripslashes($val_anteriores_inseridos);
		//echo $val_anteriores_descod."<br>";
		//é efetuada a descodificação da string codificada, transformando-a novamente num array
		$val_anteriores = json_decode($val_anteriores_descod,true);
		//print_r ($valores_provisorios);
		echo "<br>";
		
        $_SESSION['obj_id']        	= $_REQUEST['obj'];
        $temp_id_obj               	= $_SESSION['obj_id'];
		
		$_SESSION['form_id'] 		= $_REQUEST['form'];
        $form_s_id           		= $_SESSION['form_id']; //echo $_SESSION['form_id'];
		
		$_SESSION['inst_name'] 		= $_REQUEST['inst_name'];
        $temp_name_inst_obj		   	= $_SESSION['inst_name'];
		
		//caso dos objetos ******************************
		if($temp_id_obj != NULL)
		{
			$query_name                = "select * from object where object.id= $temp_id_obj "; //guardamos a instrucao numa variavel
			//echo $query_name;
			
			$result_name               = mysqli_query($conecta, $query_name); //excutamos a query
			$query_obj                 = mysqli_fetch_assoc($result_name); //guarda na variavel q é um array assoc os tuplos
			
			$_SESSION['obj_name']      = $query_obj['name']; //busca no array assc com a key name o nome do obj
			$temp_nome_obj             = $_SESSION['obj_name'];
			
			// subtitulo
			
			echo "<h3>Inserção de valores - $temp_nome_obj - inserção</h3>";
			
			// para atributo
			
			$sql_attr        = "select * from attribute where obj_id= $temp_id_obj ";
			$result_sql_attr = mysqli_query($conecta, $sql_attr);
			//echo $sql_attr."<br>";
			
			
			//---------------SQL INJECTION verificaçao antes da inserçao -----------------------------
			$temp_id_obj = mysqli_real_escape_string($conecta, $temp_id_obj);
			$temp_name_inst_obj = mysqli_real_escape_string($conecta, $temp_name_inst_obj);
			//------------------------------------inserir-------------------------------------//
			
			//adiciona o nome do objeto na base de dados corretamente.. nao o altera, apenas adiciona uma instancia do mesmo
			$insert_obj_inst="insert into obj_inst (`id`,`object_id`, `object_name`) values (NULL, '$temp_id_obj','$temp_name_inst_obj')";
			$insert_db=mysqli_query($conecta, $insert_obj_inst);//insere na bd
			//echo $insert_obj_inst."<br>";
			
			//esta query esta a ser feita para que? 
			$get_obj_inst="select obj_inst.id from obj_inst where object_id=$temp_id_obj and object_name='$temp_name_inst_obj'";
			//echo $get_obj_inst."<br>";
			$get_id = mysqli_query($conecta, $get_obj_inst);
			$get_id_resultado = mysqli_fetch_assoc($get_id);
			$val_id_resultado = $get_id_resultado['id'];
			//print_r($val_id_resultado);
			
			while($attr = mysqli_fetch_assoc($result_sql_attr))
            {
				$attribute_id = $attr['id'];
				//echo $attribute_id."<br>";
				$valor_inserir = $val_anteriores[$attribute_id];
				//echo $valor_inserir."<br>";
				
				// (CURDATE() - devolve a data atual em YYYY/MM/DD)
				// (CURTIME() - devolve as horas atuais em HH:MM:SS)
				
				$user         = wp_get_current_user(); //se o utilizador estiver logado acede ao dados do utilizador
				$username     = $user->user_login; //vamos buscar o username
				
			//---------------SQL INJECTION verificaçao antes da inserçao -----------------------------
			$val_id_resultado = mysqli_real_escape_string($conecta, $val_id_resultado);
			$attribute_id = mysqli_real_escape_string($conecta, $attribute_id);
			$valor_inserir = mysqli_real_escape_string($conecta, $valor_inserir);

			
				//------------------------------------inserir-------------------------------------//
				$insert_value = "INSERT INTO `value` (`id`, `obj_inst_id`, `attr_id`, `value`, `date`, `time`, `producer`)
				 VALUES (NULL, '$val_id_resultado', '$attribute_id', '$valor_inserir', CURDATE(), CURTIME(), '$username')";
				$insert_db=mysqli_query($conecta, $insert_value);//insere na bd
			//echo "<br> $insert_value <br>";
			
			}
        }
		//caso dos formularios *************************
		else 
		{
			//os valores respetivos a inserçao inicial nos formularios passam corretamente pelas paginas sendo identificados pelo id correspondentes
			
			$query_f_name          = "select * from custom_form where id = '$form_s_id'"; // guarda a instrucao para fazaer a query              
            $result_f_name         = mysqli_query($conecta, $query_f_name);
            $form_n                = mysqli_fetch_assoc($result_f_name); //guarda os tuplos na varivel
            
			$_SESSION['form_name'] = $form_n['name']; //guarda na variavel de session o valor retornado pelo campo acedido name
            $var_form_name         = $_SESSION['form_name'];
			
			echo "<h3>Inserção de valores - $var_form_name - inserção</h3>";
			//------------------------------------inserir-------------------------------------//
			if(!empty($temp_name_inst_obj))
			{
				
			//-------------------SQL INJECTION-----------------------------	
			$temp_name_inst_obj = mysqli_real_escape_string($conecta, $temp_name_inst_obj);
			$sql_inserir = "insert into `custom_form` (`id`, `name`) values (NULL, '$temp_name_inst_obj')";
			$insert_db = mysqli_query($conecta, $sql_inserir); //executa a query
			}
			
			$sql_attr        = "select * from attribute,custom_form_has_attribute where custom_form_has_attribute.custom_form_id= $form_s_id and  custom_form_has_attribute.attribute_id= attribute.id";
			//echo "<br>";
			//echo $sql_attr;
			$result_sql_attr = mysqli_query($conecta, $sql_attr);
			$obj_inst=mysqli_fetch_assoc($result_sql_attr);
			//print_r($obj_inst);
			$id_obj_form = $obj_inst['obj_id'];
			//echo $id_obj_form;
			$obj_form = "select distinct obj_inst.id, obj_inst.object_name, obj_inst.object_id from obj_inst, attribute where attribute.obj_id = $id_obj_form and obj_inst.object_id = attribute.obj_id";
			//echo $obj_form;
			$obj_form_query = mysqli_query($conecta, $obj_form);
			$obj_form_res = mysqli_fetch_assoc($obj_form_query);
			//print_r($obj_form_res);
			
			$result_sql_attr = mysqli_query($conecta, $sql_attr);
			while($attr = mysqli_fetch_assoc($result_sql_attr))
            {
				//$obj_form_res = mysqli_fetch_assoc($obj_form_query);
				$attribute_id = $attr['id'];
				//echo $attribute_id."<br>";
				$valor_inserir = $val_anteriores[$attribute_id];
				//echo $valor_inserir."<br>";
				$objeto_inst_id_form = $obj_form_res['id'];
				// (CURDATE() - devolve a data atual em YYYY/MM/DD)
				// (CURTIME() - devolve as horas atuais em HH:MM:SS)
				
				$user         = wp_get_current_user(); //se o utilizador estiver logado acede ao dados do utilizador
				$username     = $user->user_login; //vamos buscar o username
				
				//---------------SQL INJECTION verificaçao antes da inserçao -----------------------------
				$objeto_inst_id_form = mysqli_real_escape_string($conecta, $objeto_inst_id_form);
				$attribute_id = mysqli_real_escape_string($conecta, $attribute_id);
				$valor_inserir = mysqli_real_escape_string($conecta, $valor_inserir);
				//------------------------------------inserir-------------------------------------//
				$insert_value = "INSERT INTO `value` (`id`, `obj_inst_id`, `attr_id`, `value`, `date`, `time`, `producer`) 
				VALUES (NULL, '$objeto_inst_id_form', '$attribute_id', '$valor_inserir', CURDATE(), CURTIME(), '$username')";
				$insert_db=mysqli_query($conecta, $insert_value);//insere na bd
			
			}
		}
			
			echo "<br />Inseriu o(s) valor(es) com sucesso.<br />";
			echo "Clique em <a href='insercao-de-valores'>Voltar</a> para voltar ao início da inserção de valores e poder escolher outro objeto ou em <a href='insercao-de-valores?estado=introducao&obj=" . $_SESSION['obj_id'] . "'>Continuar a inserir valores neste objeto</a> se quiser continuar a inserir valores";
        }
		
    }
else
    {
    echo "<b>Não tem autorização para aceder a esta página, tente fazer login antes de aceder</b><br />";
    retornar(); //botao volta atrasd
    }
?> 