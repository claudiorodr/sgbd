<?php
//para aceder às funcoes no ficheiro common
require_once ("custom/php/common.php");
require_once 'PHPExcel-1.8/Classes/PHPExcel.php';
require_once 'PHPExcel-1.8/Classes/PHPExcel/Writer/Excel2007.php';
$xls_filename = 'export_'.date('Y-m-d').'_'.date('H:i:s').'.xls'; // Define Excel (.xls) file name

//verifica se o user esta logado e possui a capability
$entrou = verificaLogin('dynamic_search');
$conecta = conectar();
// se fez o login e tem acesso
if ($entrou == true)
{
	// verifico o estado
	if (!$_REQUEST['estado']) //se for nulo
	{
		echo " jfasdeu";
		// ver se tem objetos
		echo "<h3><i> Pesquisa Dinâmica - escolher objeto </i></h3>";
		$obj_query = "SELECT * FROM obj_type"; //guarda a query numa variavel
		$obj_result = mysqli_query($conecta, $obj_query); //efetua uma query
		
		if (mysqli_num_rows($obj_result) > 0) //caso nao existam tuplos
		{
			echo "Tipos de Objetos: <br />";
			echo "<ul style=list-style-type:circle>"; //abre o 1º ul	/imprime por paragrago iniciado com uma bola

			// ver quais os object types existentes
			while ($row = mysqli_fetch_assoc($obj_result)) //guarda todos os resultados num array assoc. ate terminar
			{
				echo "<li>" . $row['name'] . "</li>"; //mostra o campo name do valor da query

				$obj_sql = "SELECT * FROM object WHERE object.obj_type_id=" . $row['id'] . ""; //pus todos mas podemos SELECT DISTINCT object.id, object.name...
				$resultsql = mysqli_query($conecta, $obj_sql);
				if (mysqli_num_rows($resultsql) > 0) //se existirem tuplos
				{
					echo "<ul style=list-style-type:circle>"; //abre o 2nd ul
					while ($rowsql = mysqli_fetch_assoc($resultsql)) //enquanto existirem tuplos guardamos num array associativo
					{
						$obj_atribute = "select distinct object.id, object.name from attribute, object where attribute.obj_id = " . $rowsql["id"] . " and attribute.obj_fk_id = object.id  ORDER BY id"; //guarda a query numa variavel , evita repetidos (distinct)
						$result_obj = mysqli_query($conecta, $obj_atribute); //executa a query anterior e guarda no array

						if (mysqli_fetch_assoc($result_obj) == 0) //se for nulo
						{

							// indicamos que nao existem

							echo "<li>" . $rowsql["name"] . "</li>";
							echo "<ul><li>Não existem objetos </li></ul>";
						}
						else
						{
							echo "<li>" . $rowsql["name"] . "</li>";
							echo "<ul>";
							$result_obj = mysqli_query($conecta, $obj_atribute);
							while ($row_obj = mysqli_fetch_assoc($result_obj))
							{
								echo "<li><a id =" . $row_obj['id'] . " href='../pesquisa-dinamica?estado=escolha&obj=" . $row_obj['id'] . "'>[" . $row_obj['name'] . "]</a></li>";
							}

							echo "</ul>"; //fecha o 2nd ul
						}
					}

					echo "</ul>"; //fecha o 1º ul
				}
				else
				{
					echo "Não existem tipos de objectos para mostrar";
				}
			}

			echo "</ul>" . "\n";
		}
		else// se o valor for zero então informa que
		{
			echo "Não existem tipos de objetos criados, por favor insira um objeto antes de efectuar a pesquisa";
		}
	} //Apresentar uma lista dos atributos do objeto escolhido, sendo cada atributo seguido por uma checkbox
	//ate aqui esta implementado como pedido**************************
	
	else if ($_REQUEST["estado"] == "escolha") // se o estado estiver como escolha
	{
		$obj_anterior = $_REQUEST['obj'];
		$atrib_query = "select * from attribute where attribute.obj_id = $obj_anterior"; //valor passado do obj escolhido
		$atrib_result = mysqli_query($conecta, $atrib_query); //fazempos a query
		$atrib_num_linha_result = mysqli_num_rows($atrib_result);

		if ($atrib_num_linha_result == 0)
		{
			echo "<b>Não existem atributos neste objecto</b><br />";
			echo "Prima <a href='../pesquisa-dinamica/'>aqui</a> para retornar à pagina da Pesquisa Dinâmica<br />";
		}
		else
		{ //cria as checkboxes
			echo "<form name ='pesquisa_dinamica' method = 'POST'>";			
			echo "<fieldset>"; 
			echo "<legend>Atributos:</legend>"; 
			while ($atrib_row = mysqli_fetch_assoc($atrib_result)) //atribuimos a query feita ao array
			{ //criamos as checkboxes
				echo "<input type='checkbox' name='atributos' value='" . $atrib_row['id'] . "'> " . $atrib_row['name'] . "<br />"; //mostra o id e o nome retirado do array
			}
			echo "</fieldset>";

			// apresentamos os a lista que mostra apenas os atributos dos objetos que contenham pelo menos
            //um atributo cujo value_type seja "obj_ref" e cujo atributo obj_fk_id referencie o objeto escolhido

			$atrib_obj_ref = "select * from attribute where attribute.value_type = 'obj_ref' and attribute.obj_fk_id = $obj_anterior";
			$result_obj_ref = mysqli_query($conecta, $atrib_obj_ref);
			if (mysqli_num_rows($result_obj_ref) == 0)
			{
				echo "Não se encontram atributos que tenham o objeto de valor <b>'obj_ref'</b><br />";
				echo "Por favor, crie esse atributo .<br />";
				echo "Prima <a href='pesquisa-dinamica/'>Voltar atrás</a> para retornar<br />";
			}
			else
			{
				echo "<fieldset>";
				echo "<legend>Atributos com o tipo de objeto referido</b></legend>";
				//echo "<legend>Atributos com o tipo de objecto : obj_ref:</legend>";
				while ($row_obj_ref = mysqli_fetch_assoc($result_obj_ref))
				{
					echo "<input type='checkbox' name='atributos_obj_ref' value=" . $row_obj_ref["id"] . ">" . $row_obj_ref["name"] . "<br />";
				}

				echo "</fieldset>";
			}
			echo "<br>";
			echo "<input type='submit' name='pesquisar objeto'>";
			echo "<input type='hidden' name='estado' value='filtrar'>";
			echo "<input type='hidden' name='id_obj' value=$obj_anterior>";

			echo "</form>";
		}
	}
	else if($_REQUEST['estado'] == 'filtrar')
	{
				//array de operadores necessarios para a seleção dava erro do common (resolver)
			$operadores=array();
			$operadores['='] = '=';
			$operadores['!='] = '!=';
			$operadores['<'] = 'lt';
			$operadores['>'] = 'mt';
		
		//print_r($operadores);
			echo "<form name ='pesquisa_dinamica_filtragem' method = 'POST'>";
			echo "<fieldset>";
			
			$id_obj_escolhido = $_REQUEST['id_obj'];
			//echo $id_obj_escolhido;
			
			$val_atributos_ant = $_REQUEST['atributos'];
			//echo $val_atributos_ant;
			//é necessario criar um array para passar os varios valores
			//caso sejam escolhidos mais do que um atributo
			// fazer igual que na inserçao de valores
			//realizar testes para uma comparação com um unico valor e posteriormente passar para diversos valores
			
			$val_attr_obj_ref_ant = $_REQUEST['atrib_obj_ref'];
			//echo $val_attr_obj_ref_ant;// a mesma situaçao para os obj_ref, criar array para os diversos valores escolhidos caso existam
			
			echo "<legend>Filtragem</legend><br>";
			
			echo "Atributos do objeto :";
			echo "<select name ='atributos_objeto'>";
			
			$attr_anteriores = "SELECT * FROM attribute WHERE id = $val_atributos_ant";
			$qry_attr_ant = mysqli_query($conecta, $attr_anteriores);
			
				while($row_obj_ref = mysqli_fetch_assoc($qry_attr_ant))
				{
				echo "<option value =".$row_obj_ref['id'].">". $row_obj_ref["name"] ."</option>";
				}
				
			echo "</select>";
			echo "<br><br>";
//------------------------//
			echo "Operador :";
			
			$tipo_op_usar = mysqli_query($conecta, $attr_anteriores);
			echo "<select name ='atributos_operador'>";
			echo "<option></option>";
			while($res_tipo_op = mysqli_fetch_assoc($tipo_op_usar))
			{	
				switch ($res_tipo_op['value_type']) 
				{
   					case 'int':
					case 'double':
?>
					<option value="equal"><?php echo $operadores['=']; ?></option>
					<option value="unequal"><?php echo $operadores['!=']; ?></option>
					<option value="less"><?php echo $operadores['<']; ?></option>
					<option value="greater"><?php echo $operadores['>']; ?></option>
<?php 	
       				break;
   					case 'text':
   					case 'bool':
					?>
					<option value="equal"><?php echo $operadores['=']; ?></option>
					<option value="unequal"><?php echo $operadores['!=']; ?></option>
       				<?php
      				break;
					
					case 'enum':
					?>
					<option value="equal"><?php echo $operadores['=']; ?></option>
					<option value="unequal"><?php echo $operadores['!=']; ?></option>
					<?php
					break;
					
					case 'obj_ref':
					?>
					<option value="equal"><?php echo $operadores['=']; ?></option>
					<option value="unequal"><?php echo $operadores['!=']; ?></option>
       				<?php
					break;
				}
			echo "</select>";
			echo "<br><br>";
			}
//------------------------//
			echo "Valor :";

			$operador_obj_attr = "SELECT attr_allowed_value.id, attr_allowed_value.value FROM object, attribute, attr_allowed_value WHERE object.id = $id_obj_escolhido and attribute.obj_id = object.id and attr_allowed_value.attribute_id = $val_atributos_ant and attr_allowed_value.attribute_id = attribute.id" ;
			$result_operador_obj= mysqli_query($conecta, $operador_obj_attr);
			$row_obj_operat = mysqli_fetch_assoc($result_operador_obj);
			print_r($row_obj_operat);
			if(mysqli_num_rows($result_operador_obj)!= NULL)
			{
			echo "<select name ='valor_atributos'>";
			echo "<option value = NULL></option>";
			echo "<option value =".$row_obj_operat['id'].">". $row_obj_operat["value"] ."</option>";
			echo "</select>";
			}
			else
			{
				echo "<input type='text' name='valor_atributos'>";
				
			}
			echo "<br><br>";
			echo "<input type='submit' name='pesquisa_final_objeto'>";
			echo "<input type='hidden' name='estado' value='execucao'>";
			echo "</fieldset>";
			
			echo "</form>";
	}
	else if($_REQUEST['estado']=='execucao')
	{
		$atributo_pesquisa = $_REQUEST['atributos_objeto'];//id do atributo
		$operador_pesquisa = $_REQUEST['atributos_operador'];//operador selecionado
		$valor_pesquisa = $_REQUEST['valor_atributos'];//id do attribute value escolhido
		
		echo $atributo_pesquisa."<br>";
		echo $operador_pesquisa."<br>";
		echo $valor_pesquisa."<br>";
		
		$trad_attr_id = "SELECT * FROM attribute WHERE id = $atributo_pesquisa";
		$qry_trad_attr_id = mysqli_query($conecta, $trad_attr_id);
		$res_trad_attr_id = mysqli_fetch_assoc($qry_trad_attr_id);
		//echo $res_trad_attr_id['name']."<br>";
		
				
		$trad_val_perm_id = "SELECT * FROM  attr_allowed_value WHERE id = $valor_pesquisa";
		$qry_trad_val_perm = mysqli_query($conecta, $trad_val_perm_id);
		$res_trad_val_perm_id = mysqli_fetch_assoc($qry_trad_val_perm);
		
		if(empty($res_trad_val_perm_id))
		{
			echo "Erro na inserção do valor para procura relativamente ao atributo escolhido";
			$teste=0;
		}
		else
		{
			//echo $res_trad_val_perm_id['value'];
			echo"<br><br>";
			$teste=1;
		}
		
		
	if($teste==1){
		
		switch($operador_pesquisa) //Para descoificar o operador
				{
					case 'equal':
						$operador_pesquisa="=";
						$query="select attr_allowed_value.value,attribute.name, attribute.form_field_name from attribute,attr_allowed_value where attribute.id=$atributo_pesquisa and attr_allowed_value.id=$valor_pesquisa";
						break;
					case 'unequal':
						$operador_pesquisa="!=";
						$query="select attr_allowed_value.value,attribute.name, attribute.form_field_name from  attribute,attr_allowed_value where attribute.id=$atributo_pesquisa and attr_allowed_value.id!=$valor_pesquisa";

						break;
					case 'less':
					$query="select attr_allowed_value.value,attribute.name, attribute.form_field_name from  attribute,attr_allowed_value where attribute.id=$atributo_pesquisa and attr_allowed_value.id<$valor_pesquisa";
						$operador_pesquisa="<";
						break;
					case 'greater':
						$operador_pesquisa=">";
						$query="select attr_allowed_value.value,attribute.name, attribute.form_field_name from  attribute,attr_allowed_value where attribute.id=$atributo_pesquisa and attr_allowed_value.id>$valor_pesquisa";

						break;
				}

		
		
          
		$string_concatena_pesquisa = $res_trad_attr_id['name']." ".$operador_pesquisa."  ".$res_trad_val_perm_id['value'];
		//echo $string_concatena_pesquisa;

		//realizar as queries para procura conforme os dados especificados anteriormente
		//exporta para o excel bem verificar quais os campos a serem feitos
		$objPHPExcel = new PHPExcel();
					// indicamos quais as colunas e que campos iram ser guardados em cada
					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('A1', $string_concatena_pesquisa)
								->setCellValue('A2', 'form_field_name')
								->setCellValue('A3', 'name')
								->setCellValue('A4', 'value')
								;

					//teste usar a tabela atribute e eportar alguns campos
		//echo $operador_pesquisa."<br>";
				//$query="select * from attribute,attr_allowed_value where attribute.id=attr_allowed_value.attribute_id and attr_allowed_value.id=$valor_pesquisa";
				//$query="select * from attribute,attr_allowed_value where attribute.id=$res_trad_attr_id and attr_allowed_value.id=$valor_pesquisa";
				$result = mysqli_query($conecta, $query);
				//num_colum conta os campos
				$num_colum = 'A';//começa a 2 pois pertenc 1 ao "titulo" e vamos incrementar as letras
				//ira percorer e guaradar os campos selecionados
				foreach ($result as $value) {
				  $A = $value['form_field_name'];
				  $B = $value['name'];
			      $C = $value['value'];
				  
				$num_colum++;//incrementa ao prox letra devia ser colum
				  //na tabela a usar indica em cada coluna ira guardar qual campo
						$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue($num_colum.'2', $A )
								->setCellValue($num_colum.'3', $B )
								->setCellValue($num_colum.'4', $C )
								;
				}
		$border_style= array('borders' => array('right' => array('style' =>
			PHPExcel_Style_Border::BORDER_THICK,'color' => array('argb' => '00000000'),)));
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->getStyle("A2:A100")->applyFromArray($border_style);
		$sheet->getStyle("B2:B100")->applyFromArray($border_style);


					//permite que a coluna tenha um tamanho adequado ao seu valor inserido
					foreach(range('A2',$num_colum.'2') as $columnID) {
						$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
					}
					//linha do titulo :1 seja a negrito, coloca a inicial e a final
					$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setBold(true);


						//adicionar uma cor à linha so titulo
						$objPHPExcel->getActiveSheet()
									->getStyle('A1:B1')
									->getFill()
									->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
									->getStartColor()
									->setARGB('0000ff');


						$objPHPExcel->getActiveSheet()->setTitle('pesquisa dinamica'); //titulo da folha, nao pusos restantes atributos
						$objPHPExcel->setActiveSheetIndex(0);//podemos alterar para fazer em varias folhas
						header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
						header('Content-Type: application/vnd.ms-excel');
						header('Content-Disposition: attachment;Filename='.$xls_filename);
						header('Cache-Control: max-age=0');
						$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
						ob_clean();//evita os characteres giberish
						$objWriter->save(".$xls_filename");//nome dinamico
						//acesso ao ficheiro guardado no servidor
						echo"<a href='../../.$xls_filename' download='.$xls_filename'>Download Ficheiro Excel</a>";
	}
	}
}
else
{
	echo "<b>Não tem autorização para aceder a esta página, tente fazer login antes de aceder</b><br />";
	retornar(); //botao volta atras
}

?>
