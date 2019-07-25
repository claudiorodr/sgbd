<?php
require_once ("custom/php/common.php");
require_once ("phpexcel/PHPExcel.php");
$entrou = verificaLogin('values_import');
$conecta = conectar();
if ($entrou == true)
{
    if ($_REQUEST["estado"] == "")
    {
?>
<h3>
  <b>
    <i>Importação de Valores - escolher objeto
    </i>
  </b>
</h3>
<?php
        //Busca todos os tipos de objetos na BD
        $tipo_objeto = "SELECT *
						FROM obj_type";
        $tipo_objeto_query = mysqli_query($conecta, $tipo_objeto);
        $nr_tipo_objeto_query = mysqli_num_rows($tipo_objeto_query);
?>
<!-- ul - Lista desordenada (unordered list), faz também o espaçamento -->
<ul style="list-style-type:circle">
  <li>
    <b>Objeto:
    </b>
  </li>
  <!-- Listas dentro da lista -->
  <ul style="list-style-type:square">
    <?php
        //Percorre todos os tipos de objeto
        for ($i = 0;$i < $nr_tipo_objeto_query;$i++)
        {
            //tipo de objeto atual
            $tipo_objeto_atual = mysqli_fetch_assoc($tipo_objeto_query);
?>        
    <li>
      <b>
        <?php
            //Imprime o nome do tipo de objeto como elemento da lista
            echo $tipo_objeto_atual["name"];
?>
      </b>
    </li>
    <?php
            //Vou buscar os objetos associados a acada tipo de objeto
            $objeto = "SELECT object.id, object.name
						FROM object, obj_type
						WHERE obj_type.id = object.obj_type_id
						AND obj_type.id ='" . $tipo_objeto_atual["id"] . "'";
            $objeto_query = mysqli_query($conecta, $objeto);
            $nr_objeto_query = mysqli_num_rows($objeto_query);
?>
    <!-- Nova lista com os objetos de cada tipo de objetos -->
    <ul style="list-style-type:square">
      <?php
            //Busco todos os objetos associados a um tipo de objeto
            for ($j = 0;$j < $nr_objeto_query;$j++)
            {
                $objeto_atual = mysqli_fetch_assoc($objeto_query);
?>            
      <!-- Cada objeto tem um hiperligação para a página de introdução -->
      <li>
        <a href=
           <?php
                //hiperligação com id do respetivo objeto nessa entrada
                echo "importacao-de-valores?estado=introducao&obj=" . $objeto_atual["id"] . "";
?>>
        <?php
                //Imprime o nome o objeto atual como um elemento da lista
                echo "[" . $objeto_atual["name"] . "]";
?>
      </a>
    </li>    
  <?php
            }
?>
</ul> 
<!-- Fecha a lista dos objetos -->
<?php
        }
?>
</ul> 
<!-- Fecha a lista dos tipos de objetos -->
</ul> 
<!-- Fecha a lista com o "Objetos", desnecessário -->
<!-- Lista com formularios costumizados -->
<ul style="list-style-type:circle">
  <li>
    <b>Formulários customizados
    </b>
  </li>
  <?php
        //Busco todos os formularios costumizados
        $formulario = "SELECT *
FROM custom_form";
        $formulario_query = mysqli_query($conecta, $formulario);
        $nr_formulario_query = mysqli_num_rows($formulario_query);
?>
  <!-- Lista com formularios costumizados -->
  <ul style="list-style-type:square">
    <?php
        //Percorre todos os formularios
        for ($k = 0;$k < $nr_formulario_query;$k++)
        {
            //Formulario atual do ciclo
            $formulario_atual = mysqli_fetch_assoc($formulario_query);
?>         
    <!-- Hiperligação com o id do formulario atual -->
    <li>
      <a href=
         <?php
            echo "importacao-de-valores?estado=introducao&form=" . $formulario_atual["id"] . "";
?>>
      <?php
            //Imprime o o nome do formulario atual como elemento da lista
            echo "[" . $formulario_atual["name"] . "]";
?>
    </a>
  </li>
<?php
        }
?>
</ul> 
<!-- Fecha a alista dos formularios -->
</ul> 
<!-- Fecha o "Formularios customizados", desnecessario -->
<?php
    }
    //Estado de execução é introdução (foi clicado a hiperligação)
    else if ($_REQUEST['estado'] == "introducao")
    {
        //Vê se foi clicado um objeto
        if ($_REQUEST["obj"])
        {
            //guardo numa variável o id do objeto clicado (vem da hiperligação)
            $obj_id = $_REQUEST["obj"];
            //Busco os atributos com aquele obj_id
            $attribute = "SELECT *
						  FROM attribute
						  WHERE obj_id ='" . $obj_id . "'";
            $attribute_query = mysqli_query($conecta, $attribute);
            $nr_attribute_query = mysqli_num_rows($attribute_query);
?>
<!-- Apresentar uma tabela com duas linhas, contendo, na primeira, em cada coluna os formfieldnames dos atributos do objeto ou formulário clicado.
Na segunda linha, apresentar, apenas para os atributos enum, nas respetivas colunas os valores permitidos. -->
<table class="mytable" style="text-align: left; width: auto;" border="1" cellpadding="2" cellspacing="2"> 
  <!-- Primeira linha, em cada coluna tem os formfieldnames dos atributos do objeto ou formulário clicado.-->
  <tr>
    <th>
      <b>Form field names:
      </b>
    </th>
    <?php
            //Percorre todos os atributos
            for ($i = 0;$i < $nr_attribute_query;$i++)
            {
                //atributo atual do ciclo
                $attribute_atual = mysqli_fetch_assoc($attribute_query);
                //Se atributo atual é enum
                if ($attribute_atual["value_type"] == "enum")
                {
                    //Busca valores permitidos com o attribute_id do atual
                    $allowed_value = "SELECT * 
									  FROM attr_allowed_value
									  WHERE attribute_id = '" . $attribute_atual["id"] . "'";
                    $allowed_value_query = mysqli_query($conecta, $allowed_value);
                    //número de valores permitidos daquele atributo, para depois imprimir o form_field_name
                    $nr_allowed_value_query = mysqli_num_rows($allowed_value_query);
                    //Percorre todos os valores permitidos com aquele attribute_id
                    for ($j = 0;$j < $nr_allowed_value_query;$j++)
                    {
?>
    <td>
      <?php
                        //Imprime form_field_name do atributo atual nº de vezes igual ao nº de valores permitodos desse atributo
                        echo $attribute_atual["form_field_name"];
?>
    </td>
    <?php
                    }
                }
                else
                {
?>
    <td>
      <?php
                    //Imprime só uma vez caso o atributo não seja enum
                    echo $attribute_atual["form_field_name"];
?>
    </td>
    <?php
                }
            }
?>
  </tr>
  <!-- Na segunda linha, apresentar, apenas para os atributos enum, nas respetivas colunas os valores permitidos. -->      
  <tr>
    <th>
      <b>Valores permitidos:
      </b>
    </th>
    <?php
            //Busco os atributos com o obj_id do objeto clicado
            $attribute = "SELECT *
						  FROM attribute
						  WHERE obj_id ='" . $obj_id . "'";
            $attribute_query = mysqli_query($conecta, $attribute);
            $nr_attribute_query = mysqli_num_rows($attribute_query);
            //Percorre os atributos com aquele obj_id
            for ($i = 0;$i < $nr_attribute_query;$i++)
            {
                //atributo atual do ciclo
                $attribute_atual = mysqli_fetch_assoc($attribute_query);
                //Se o atributo atual for enum
                if ($attribute_atual["value_type"] == "enum")
                {
                    //Vai buscar os valores permitidos com aquele attribute_id
                    $attr_allowed_value = "SELECT * 
										   FROM attr_allowed_value
										   WHERE attribute_id = '" . $attribute_atual["id"] . "'";
                    $attr_allowed_value_query = mysqli_query($conecta, $attr_allowed_value);
                    $nr_attr_allowed_value_query = mysqli_num_rows($attr_allowed_value_query);
                    //Percorre todos os valres permitidos para aquele atributo
                    for ($j = 0;$j < $nr_attr_allowed_value_query;$j++)
                    {
                        $attr_allowed_value_atual = mysqli_fetch_assoc($attr_allowed_value_query);
?>
    <td>
      <?php
                        //Imprime todos os valor permitidos para o atributo atual
                        echo $attr_allowed_value_atual["value"];
?>
    </td>
    <?php
                    }
                }
                else
                { //Não imprime nada caso não seja enum
                    
?>
    <td>
      <?php
?>
    </td>
    <?php
                }
            }
?>
  </tr>
</table>
<!-- Fecha a linha e tabela -->
<?php
            echo "Deverá copiar estas linhas para um ficheiro excel e introduzir os valores a importar, sendo que no caso dos atributos enum, deverá constar um 0 quando esse valor permitido não se aplique à instância em causa e um 1 quando esse valor se aplica. ";
?>
<form action=
      <?php echo "?estado=insercao&obj=" . $obj_id; ?> method="post" enctype="multipart/form-data"> 
<input type="file" name="file"/>
<input type="submit" name= "inserir" value="Inserir ficheiro">
</form>
<?php
        }
        //Vê se foi clicado um objeto
        else if ($_REQUEST["form"])
        {
            //guardo numa variávelvel o id do objeto clicado (vem da hiperligaÃ§Ã£o)
            $form_id = $_REQUEST["form"];
            //Vou ao custom_form_has_attribute buscar os attribute_id daquele formulario
            $custom = "SELECT *
					   FROM custom_form_has_attribute
					   WHERE custom_form_id ='" . $form_id . "'";
            $custom_query = mysqli_query($conecta, $custom);
            $nr_custom_query = mysqli_num_rows($custom_query);
?>
<!-- Apresentar uma tabela com duas linhas, contendo, na primeira, em cada coluna os formfieldnames dos atributos do objeto ou formulário clicado.
Na segunda linha, apresentar, apenas para os atributos enum, nas respetivas colunas os valores permitidos. -->
<table class="mytable" style="text-align: left; width: auto;" border="1" cellpadding="2" cellspacing="2"> 
  <!-- Primeira linha, em cada coluna tem os formfieldnames dos atributos do objeto ou formulário clicado.-->
  <tr>
    <th>
      <b>Form field names:
      </b>
    </th>
    <?php
            //Percorre todos os atributos
            for ($i = 0;$i < $nr_custom_query;$i++)
            {
                //atributo atual do ciclo
                $custom_atual = mysqli_fetch_assoc($custom_query);
                $attribute = "SELECT *
							  FROM attribute
							  WHERE attribute.id ='" . $custom_atual["attribute_id"] . "'";
                $attribute_query = mysqli_query($conecta, $attribute);
                $attribute_atual = mysqli_fetch_assoc($attribute_query);
                //Se atributo atual é enum
                if ($attribute_atual["value_type"] == "enum")
                {
                    //Busca valores permitidos com o attribute_id do atual
                    $allowed_value = "SELECT * 
									  FROM attr_allowed_value
									  WHERE attribute_id ='" . $attribute_atual["id"] . "'";
                    $allowed_value_query = mysqli_query($conecta, $allowed_value);
                    //número de valores permitidos daquele atributo, para depois imprimir o form_field_name
                    $nr_allowed_value = mysqli_num_rows($allowed_value_query);
                    //Percorre todos os valores permitidos com aquele attribute_id
                    for ($j = 0;$j < $nr_allowed_value;$j++)
                    {
?>
    <td>
      <?php
                        //Imprime form_field_name do atributo atual não de vezes igual ao nº de valores permitodos desse atributo
                        echo $attribute_atual["form_field_name"];
?>
    </td>
    <?php
                    }
                }
                else
                {
?>
    <td>
      <?php
                    //Imprime só uma vez caso o atributo não seja enum
                    echo $attribute_atual["form_field_name"];
?>
    </td>
    <?php
                }
            }
?>
  </tr>
  <!-- Na segunda linha, apresentar, apenas para os atributos enum, nas respetivas colunas os valores permitidos. -->            
  <tr>
    <th>
      <b>Valores permitidos:
      </b>
    </th>
    <?php
            //Busco os atributos com o obj_id do objeto clicado
            $custom = "SELECT *
					   FROM custom_form_has_attribute
					   WHERE custom_form_id ='" . $form_id . "'";
            $custom_query = mysqli_query($conecta, $custom);
            $nr_custom = mysqli_num_rows($custom_query);
            //Percorre os atributos com aquele obj_id
            for ($i = 0;$i < $nr_custom;$i++)
            {
                //atributo atual do ciclo
                $custom_atual = mysqli_fetch_assoc($custom_query);
                //
                $attribute = "SELECT *
							  FROM attribute
							  WHERE attribute.id ='" . $custom_atual["attribute_id"] . "'";
                $attribute_query = mysqli_query($conecta, $attribute);
                $attribute_query_atual = mysqli_fetch_assoc($attribute_query);
                //Se o atributo atual for enum
                if ($attribute_query_atual["value_type"] == "enum")
                {
                    //Vai buscar os valores permitidos com aquele attribute_id
                    $allowed_value = "SELECT * 
									  FROM attr_allowed_value
									  WHERE attribute_id = '" . $attribute_query_atual["id"] . "'";
                    $allowed_value_query = mysqli_query($conecta, $allowed_value);
                    $nr_allowed_value = mysqli_num_rows($allowed_value_query);
                    //Percorre todos os valres permitidos para aquele atributo
                    for ($j = 0;$j < $nr_allowed_value;$j++)
                    {
                        $allowed_value_atual = mysqli_fetch_assoc($allowed_value_query);
?>
    <td>
      <?php
                        //Imprime todos os valor permitidos para o atributo atual
                        echo $allowed_value_atual["value"];
?>
    </td>
    <?php
                    }
                }
                else
                { //Não imprime nada caso não seja enum
                    
?>
    <td>
      <?php
?>
    </td>
    <?php
                }
            }
?>
  </tr>
</table>
<!-- Fecha a linha e tabela -->
<?php
            echo "Deverá copiar estas linhas para um ficheiro excel e introduzir os valores a importar, sendo que no caso dos atributos enum, deverá constar um 0 quando esse valor permitido não se aplique à  instância em causa e um 1 quando esse valor se aplica. ";
            if (objeto_atual["value_type"] == "enum")
            {
                if (objeto_atual)
                {
                }
            }
?>
<form action=
      <?php echo "?estado=insercao&form=" . $form_id; ?> method="post" enctype="multipart/form-data"> 
<input type="file" name="file"/>
<input type="submit" name= "inserir" value="Inserir ficheiro">
</form>
<?php
        }
    }
    else if ($_REQUEST['estado'] == "insercao")
    {
        if (!empty($_FILES['file']['name'])) 
        {
            // Get File extension eg. 'xlsx' to check file is excel sheet
            $pathinfo = pathinfo($_FILES["file"]["name"]); //informação do caminho
            // check file has extension xlsx, xls and also check
            // file is not empty
			$current_user = wp_get_current_user(); //busca informção do utilizador atual
            if (($pathinfo['extension'] == 'xlsx' || $pathinfo['extension'] == 'xls') && $_FILES['file']['size'] > 0) //Verifica se é um ficheiro excel
            {
				try 
				{
                // Temporary file name
                $inputFileName = $_FILES['file']['tmp_name'];
                // Read excel file by using ReadFactory object.
                $excelReader = PHPExcel_IOFactory::createReaderForFile($inputFileName);
                $excelObj = $excelReader->load($inputFileName);
				}
				catch(Exception $e) 
				{
				die('Erro no carregamento do ficheiro "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
				}
				
                $worksheet = $excelObj->getSheet(0);
                $lastRow = $worksheet->getHighestRow();
                $highestColumnIndex = $worksheet->getHighestDataColumn();
                $column_nr = PHPExcel_Cell::columnIndexFromString($highestColumnIndex);
                
                // It reads data after header. In the my excel sheet,
                // header is in the first row.
				//  Loop through each row of the worksheet in turn
                for ($linha = 3; $linha <= $lastRow; $linha++) //Ler unicamente linha 3
                {
					//Busca o id máximo de obj_inst, sendo o id atual
					$obj_inst = "SELECT max(id) AS obj_inst_id 
								 FROM obj_inst";
                    $obj_inst_query = mysqli_query($conecta, $obj_inst);
                    $obj_inst_atual = mysqli_fetch_assoc($obj_inst_query);
					
					//Para o caso de um objeto ter sido selecionado
                    if ($_REQUEST['obj'])
                    {
						//Insere em obj_inst o id do objeto clicado
                        $objInst = "INSERT INTO `obj_inst` (`id`, `object_id`, `object_name`) 
									VALUES (NULL, '" . $_REQUEST['obj'] . "', NULL)";
                        $objInstQuery = mysqli_query($conecta, $objInst);
                    }
					//Para o caso de um formulario ter sido selecionado 
                    else if ($_REQUEST['form'])
                    {
						//vou ao custom_form_has_attribute buscar os attribute_id daquele formulario
                        $form = "SELECT * 
								 FROM attribute,custom_form_has_attribute 
								 WHERE attribute_id = attribute.id AND custom_form_id = '" . $_REQUEST['form'] . "'";
                        $formQuery = mysqli_query($conecta, $form);
                        $form_atual = mysqli_fetch_assoc($formQuery);
						
						//Insiro esse attribute_id na tabela obj_inst
                        $formInst = "INSERT INTO `obj_inst` (`id`, `object_id`, `object_name`) 
									 VALUES (NULL, '" . $form_atual['obj_id'] . "', NULL)";
                        $formInstQuery = mysqli_query($conecta, $formInst);
                    }
					//Percorrendo a linha 3
                    for ($column = 1; $column < $column_nr; $column++)
                    {
						//Vou buscar o form_field_name na celula a cima da atual
                        $form_field = $worksheet->getCellByColumnAndRow($column, 1)->getValue();
                        //Busco o atributo com aquele form_field_name
						$form_field_attr = "SELECT * 
											FROM attribute 
											WHERE form_field_name = '" . $form_field ."'";
                        $form_field_attr_query = mysqli_query($conecta, $form_field_attr);
                        $attribute = mysqli_fetch_assoc($form_field_attr_query);
                        $cell = $worksheet->getCellByColumnAndRow($column, $linha)->getValue();
						
						//Se na celula atual estiver "1" significa que irá ser inserido na tabela value  	
						if ($cell == "1")
						{							
							$inserir_value = "INSERT INTO `value` (`id`, `obj_inst_id`, `attr_id`, `value`, `date`, `time`, `producer`) 
											  VALUES (NULL, '". $obj_inst_atual["obj_inst_id"] . "', '" . $attribute["id"] . "', '" . $cell . "', CURDATE(), CURTIME(), NULL)";
							$resultado_inserir_value = mysqli_query($conecta, $inserir_value);
							echo "Inseriu com sucesso os dados!";
						}                       
                    }
                }
            } 
			else
			{
				echo "Insira um ficheiro";
			}
        }
        else
        {
            echo "Por favor insira um ficheiro Excel";
        }
    }
}
else
{
    echo "Não tem autorização para aceder a esta página";
    retorna();
}
?>