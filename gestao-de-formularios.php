<?php 
	require_once("custom/php/common.php");
	
	$entrou=verificaLogin('manage_custom_forms');
	$conecta = conectar();
	
	if($entrou == true)
	{
		if($_REQUEST['estado']=="")
		{
?>		
			<h3 class='formato'>Gestão de formularios customizados</h3>
<?php
			$formul_cost = "SELECT * FROM custom_form";
			$res_form_cost = mysqli_query($conecta, $formul_cost);
			$num_form_cost = mysqli_num_rows($res_form_cost);
			
			if($num_form_cost == 0)
			{
				echo "Nao existem dados na tabela de formularios costumizados";
			}
			else
			{
?>			
				<link rel="stylesheet" type="text/css" href="ag.css">
				<table class="mytable" style="text-align: left; width: 100%;" border="1" cellpadding="2" cellspacing="2">
				<tr>
				<!--Cabeçalhos da tabela-->
					<th>nome formulario</th>	<!--clicavel-->		
					<th>id</th>
					<th>Nome do atributo</th>
					<th>tipo de valor</th>
					<th>nome do campo no formulario</th>
					<th>tipo do campo no formulario</th>
					<th>tipo de unidade</th>			
					<th>ordem no campo do formulario</th>
					<th>tamanho do campo no formulario</th>
					<th>obrigatorio</th>			
					<th>estado</th>
					<th>ação</th>
				</tr>
				
			<?php

				while($linha_form_cost = mysqli_fetch_assoc($res_form_cost))
				{
					
					$atr_form_q = $linha_form_cost['id'];
					$atrib_formul_cost = "SELECT * FROM custom_form_has_attribute, attribute WHERE custom_form_has_attribute.attribute_id = attribute.id and custom_form_has_attribute.custom_form_id = $atr_form_q";
					$res_attr_form = mysqli_query($conecta, $atrib_formul_cost);
					$num_linhas_tab = mysqli_num_rows($res_attr_form);
					
					
					if($num_linhas_tab != 0)
					{				
?>
					<tr>
					<td rowspan=<?php echo $num_linhas_tab; ?>> <a id="<?php echo $linha_form_cost['id']; ?>" href='./?estado=editar_form&id="<?php echo $atr_form_q;?>"'><?php echo $linha_form_cost['name']; ?> </a></td>
					
					<?php
					while($linha = mysqli_fetch_assoc($res_attr_form))
					{
					?>
					<td> <?php echo $linha['custom_form_id']; ?> </td>
					<td> <?php echo $linha['name']; ?> </td> 
					<td> <?php echo $linha['value_type']; ?> </td>
					<td> <?php echo $linha['form_field_name']; ?> </td> 
					<td> <?php echo $linha['form_field_type']; ?> </td>
					<td> <?php echo $linha['unit_type_id']; ?> </td>
					<td> <?php echo $linha['form_field_size']; ?> </td> 
					<td> <?php echo $linha['form_field_order']; ?> </td>
					<td> <?php echo $linha['mandatory']; ?> </td>
					<td> <?php echo $linha['state']; ?> </td> 
					<td> [editar] [desativar] </td>
					</tr>
<?php 
					}
				}
			}
?>
			</table>
<?php 
			}
	?>
		<form name ="gestao-de-formularios" method = "POST">
			<fieldset>
	
			<fieldset>
					<legend>Nome do formulario:</legend>
					<input type = "text" name = "nome_formulario" ><br>
			</fieldset>

		
		
<?php
			
			$attr = "SELECT * FROM attribute";
			$attr_qry = mysqli_query($conecta, $attr);
?>
			<link rel="stylesheet" type="text/css" href="ag.css">
			<table class="mytable" style="text-align: left; width: 100%;" border="1" cellpadding="2" cellspacing="2">
				<tr>
				<!--Cabeçalhos da tabela-->
					<th>objeto</th>					
					<th>id</th>
					<th>Nome do atributo</th>
					<th>tipo de valor</th>
					<th>nome do campo no formulario</th>
					<th>tipo do campo no formulario</th>
					<th>tipo de unidade</th>			
					<th>ordem no campo do formulario</th>
					<th>tamanho do campo no formulario</th>
					<th>obrigatorio</th>			
					<th>estado</th>
					<th>escolher</th> <!-- contem uma checkbox-->
					<th>ordem</th>
				</tr>
				
<?php
			while($linha_attr = mysqli_fetch_assoc($attr_qry))
			{
				$attr_val = $linha_attr['id'];
				$attr_objs_form = "SELECT * FROM attribute WHERE attribute.id = $attr_val";
				$attr_objs_res = mysqli_query($conecta, $attr_objs_form);
				$attr_objs_num_l = mysqli_num_rows($attr_objs_res);
				$attr_objs_final = mysqli_fetch_assoc($attr_objs_res);
				
				if($attr_objs_num_l != 0)
				{
					
?>				<tr>
					<td> <?php echo $attr_objs_final['obj_id']; ?> </td>
					<td> <?php echo $attr_objs_final['id']; ?> </td> 
					<td> <?php echo $attr_objs_final['name']; ?> </td>
					<td> <?php echo $attr_objs_final['value_type']; ?> </td> 
					<td> <?php echo $attr_objs_final['form_field_name']; ?> </td>
					<td> <?php echo $attr_objs_final['form_field_type']; ?> </td>
					<td> <?php echo $attr_objs_final['unit_type_id']; ?> </td> 
					<td> <?php echo $attr_objs_final['form_field_order']; ?> </td>
					<td> <?php echo $attr_objs_final['form_field_size']; ?> </td> 
					<td> <?php echo $attr_objs_final['mandatory']; ?> </td>
					<td> <?php echo $attr_objs_final['state']; ?> </td>
					<td> <input type='checkbox' name='lista[]' value='<?php echo $attr_objs_final['id']; ?>'> <?php echo $attr_objs_final['id']; ?> </td> 
					
					<!-- perguntar ao prof qual é a predeiniçao para um valor minimo e maximo para o valor da ordem-->
					<td> <input type='number' name='ordem_<?php echo $attr_objs_final['id']; ?>' min=0 max=1000 </td>
				</tr>
<?php
				}
			}				
				
	?>	
			</table>
		
			<input type="hidden" name="estado" value="inserir">
			<input type="submit" value="Inserir formulario">
		</fieldset>
		
		</form>
<?php	}
	
		else if($_REQUEST['estado']=="inserir")
		{
			//fazer query para inserção de valores nas tabelas custom_form e custom_form_has_attribute
			//os respetivos valores de acordo com o que for preenchido no formulario anterior
		$marcou_lista = $_REQUEST['lista'];
		$nome_form = $_REQUEST['nome_formulario'];
		
		
		//se forem valores da checkbox forem marcados  
		if(!empty($marcou_lista))
		{
		
			$colocar_form = "INSERT INTO custom_form (name) VALUES ('$nome_form')";
			//echo "$colocar_form <br>";
			$colocar_form_qry = mysqli_query($conecta, $colocar_form);
			$form_colocado_id = mysqli_insert_id($conecta);
		
			foreach($marcou_lista as $marca)
			{
				$ordem = $_REQUEST['ordem_'.$marca];
				//echo "<br> $ordem";
				if(empty($ordem))
				$ordem = 0;
			
			//query de inserçao na tabela 
			$colocar_form_tem_attr = "INSERT INTO custom_form_has_attribute VALUES ($form_colocado_id, $marca, $ordem)"; 
			$qry_coloc_form_attr = mysqli_query($conecta, $colocar_form_tem_attr);
			}
?>
			<legend>Foram inseridos os dados do novo formulario customizado na base de dados <br></legend>
			<legend>Para continuar com a inserçao ou ediçao de formularios costumizados, clique em  <b><a href="gestao-de-formularios">voltar</a></b>.</legend>
<?php
		}
		else
		{
?>
			<legend>Nao foram marcados dados novos para o formulario costumizado <br></legend>
			<legend>Para voltar atras para poder faze-lo, clique em <b><a href="gestao-de-formularios">voltar</a></b>.</legend>
<?php
		}
		}
		else if($_REQUEST['estado']=="editar_form")
		{
			//apresentar um formulario identico ao do estado de inserir, apresentado pre preenchido:
			//o nome do formulario costumizado e pre-selecionado os atributos pertencentes a este form, sendo possivel selecionar novas ou des-selecionar um atributo
			//o estado seguinte deve ser atualizar_form_custom
			$form_id = $_REQUEST['id'];
			//echo $form_id."<br>";
			
			//foi preciso efetuar esta conversao pois o $form_id retornava "\1\" o que cousava problemas de sintaxe no SQL
			preg_match_all('!\d+!', $form_id, $matches);
			//print_r($matches);
			//echo $matches[0][0];
			$valor_form_id = $matches[0][0];
			//echo $valor_form_id;
			
			$attr = "SELECT * FROM attribute";
			$attr_qry = mysqli_query($conecta, $attr);
	?>
	<form name="gestao-de-formularios-editar" method="POST">
	Nome do formulario costumizado: <input type="text" name="nome_actualizado">
			<link rel="stylesheet" type="text/css" href="ag.css">
			<table class="mytable" style="text-align: left; width: 100%;" border="1" cellpadding="2" cellspacing="2">
				<tr>
				<!--Cabeçalhos da tabela-->
					<th>objeto</th>					
					<th>id</th>
					<th>Nome do atributo</th>
					<th>tipo de valor</th>
					<th>nome do campo no formulario</th>
					<th>tipo do campo no formulario</th>
					<th>tipo de unidade</th>			
					<th>ordem no campo do formulario</th>
					<th>tamanho do campo no formulario</th>
					<th>obrigatorio</th>			
					<th>estado</th>
					<th>escolher</th> <!-- contem uma checkbox-->
					<th>ordem</th>
				</tr>

<?php	
			while($linha_attr = mysqli_fetch_assoc($attr_qry))
			{
				$attr_val = $linha_attr['id'];
				$attr_objs_form = "SELECT * FROM attribute WHERE attribute.id = $attr_val";
				$attr_objs_res = mysqli_query($conecta, $attr_objs_form);
				$attr_objs_num_l = mysqli_num_rows($attr_objs_res);
				$attr_objs_final = mysqli_fetch_assoc($attr_objs_res);
				
				if($attr_objs_num_l != 0)
				{					
?>				
				<tr>
					<td> <?php echo $attr_objs_final['obj_id']; ?> </td>
					<td> <?php echo $attr_objs_final['id']; ?> </td> 
					<td> <?php echo $attr_objs_final['name']; ?> </td>
					<td> <?php echo $attr_objs_final['value_type']; ?> </td> 
					<td> <?php echo $attr_objs_final['form_field_name']; ?> </td>
					<td> <?php echo $attr_objs_final['form_field_type']; ?> </td>
					<td> <?php echo $attr_objs_final['unit_type_id']; ?> </td> 
					<td> <?php echo $attr_objs_final['form_field_order']; ?> </td>
					<td> <?php echo $attr_objs_final['form_field_size']; ?> </td> 
					<td> <?php echo $attr_objs_final['mandatory']; ?> </td>
					<td> <?php echo $attr_objs_final['state']; ?> </td>
					<!-- pre seleçao dos valores é feita a partir daqui atraves de uma query para verificar se ja existem valores correspondentes na tabela custom_form_has_attibute-->
					<?php
					
					$lista_valores_ant = "SELECT * FROM custom_form_has_attribute WHERE custom_form_id=$valor_form_id AND attribute_id =".$attr_objs_final['id'];
					//echo $lista_valores_ant;
					
					$qry_lista_valores_ant = mysqli_query($conecta, $lista_valores_ant);
					$linhas_lista_val_ant = mysqli_num_rows($qry_lista_valores_ant);
					$res_linhas_lista_ant = mysqli_fetch_assoc($qry_lista_valores_ant);
					
					//caso haja valores retornados da query anterior, os valore aparecem pre-selecionados os quais podem ser modificados 
					if($linhas_lista_val_ant == 1)
					{
					?>
					<td> <input type='checkbox' name='lista[]' value='<?php echo $attr_objs_final['id']; ?>' checked> <?php echo $attr_objs_final['id']; ?> </td> 
					<td> <input type='number' name='ordem_<?php echo $attr_objs_final['id']; ?>' min=0 max=1000 value="<?php echo $res_linhas_lista_ant['field_order']?>"</td>
					<?php
					}
					//caso nao haja valores retornados da query, os campos aparecem sem valores pre-selecionados 
					else if($linhas_lista_val_ant == 0)
					{
					?>
					<td> <input type='checkbox' name='lista[]' value='<?php echo $attr_objs_final['id']; ?>'> <?php echo $attr_objs_final['id']; ?> </td> 
					<td> <input type='number' name='ordem_<?php echo $attr_objs_final['id']; ?>' min=0 max=1000 </td>
					<?php	
					}
					?>
				</tr>
<?php
				}
			}
	?>		</table>
			<input type = "hidden" name = "estado" value = "atualizar_form_custom">
            <input type = "submit" value = "atualizar form custom">
			</form>	
<?php	}
		else if($_REQUEST['estado']=="atualizar_form_custom")
		{
			//realizar os updates necessarios/deletes nas respetivas tabelas 
			$form_id = $_REQUEST['id'];
			
			//efetua a substituiçao do valor do id obtido uma vez que é retornado no formato "\num"\ causando interferencia com a query
			preg_match_all('!\d+!', $form_id, $matches);
			$valor_form_id = $matches[0][0];
			//echo $valor_form_id."<br>";
			
			$nome_actz_form = $_REQUEST['nome_actualizado'];
			$lista = $_REQUEST['lista'];
			
			$confirmacao = false; 
			
		//query para verificar quais os formularios customizados existentes com o id obtido
		$formul = "SELECT * FROM custom_form WHERE custom_form.id = $valor_form_id";
		$qry_formul = mysqli_query($conecta, $formul);
		$res_qry_formul = mysqli_fetch_assoc($qry_formul);
		
		//se tiver valores inseridos no nome do campo do formulario e este for puramente constituido por numeros alfabeticos é feita a atualizaçao do mesmo
		if(!empty($nome_actz_form) && ctype_alpha($nome_actz_form))
		{
			$atualizacao_nome_form = "UPDATE custom_form SET custom_form.name='$nome_actz_form' WHERE custom_form.id = $valor_form_id";
			$res_atualizacao_nome = mysqli_query($conecta, $atualizacao_nome_form);
			$confirmacao = true;
		}
		
		$temp_formul = "SELECT * FROM custom_form_has_attribute WHERE custom_form_id = $valor_form_id";
		$qry_temp_formul = mysqli_query($conecta, $temp_formul);
			
			while($res_qry_formul = mysqli_fetch_assoc($qry_temp_formul))
			{ 
				$id_attr = $res_qry_formul['attribute_id'];
				//array_key_exists é usado para verificar se uma certa chave existe no array escolhido
				if(!array_key_exists($res_qry_formul['attribute_id'],$lista))
				{
					//efetua a eliminaçao do valor caso faça uncheck
					$elimina_val_form = "DELETE FROM custom_form_has_attribute WHERE attribute_id = $id_attr AND custom_form_id = $valor_form_id";
					$qry_elimina_val_form = mysqli_query($conecta, $elimina_val_form);
					//se entrou, é feita a atualização e aparece a mensagem de sucesso de modificaçao dos dados na base de dados
					//para evitar repetição de mensagens de aprovação é usado um booleano para que apenas apareça uma unica vez a confirmar a inserção/modificação
					$confirmacao = true;
				}
			}
			//array onde estarao todos os atributos respetivos a um certo formulario costumizado
			$atrib = array();
			
			while($res_qry_formul = mysqli_fetch_assoc($qry_temp_formul))
			{
				//é utilizado o array_push pois o $array[] nao funciona
				array_push($atrib, $res_qry_formul['attribute_id']); // o valor é inserido no array de modo a manter os valores todos atualizados 
			}
			
			foreach($lista as $li)
			{
				$ordem = $_REQUEST['ordem_'.$li];
				
				if (!array_key_exists($li, $atrib)) // se não estiver na tabela custom_fom_has_property
                {
					if(empty($ordem))
					{
						$ordem = 0;	
					}	
                    $insere_tab = "INSERT INTO custom_form_has_attribute (custom_form_id, attribute_id, field_order) VALUES ('$valor_form_id', '$li', '$ordem')";
					$resultado_tab = mysqli_query($conecta,$insere_tab);
					$confirmacao = true;
				}
                else // se existe, verifica se o utilizador actualizou alguma coisa no field_order
                {
                    $verifica_ordem = "SELECT * FROM custom_form_has_attribute WHERE custom_form_id = $valor_form_id AND attribute_id = $li";
                    $qry_verif_ordem = mysqli_query($conecta,$verifica_ordem);
                    $res_verif_ordem = mysqli_fetch_assoc($resultado);
                    
					if ($ordem != $res_verif_ordem["field_order"])//verifica se o field order é diferente do dado anterior
                    {
                        $actualiza_ordem ="UPDATE custom_form_has_attribute SET field_order = $ordem WHERE custom_form_id = $id_formulario AND attribute_id = $li";
						$res_atlz_ordem = mysqli_query($conecta,$actualiza_ordem); //actualiza a informação
						$confirmacao = true;
					}
                }
			}
			if($confirmacao)
			{
?>
				<legend> Os dados do formulario costumizado foram atualizados!<br></legend>
				<legend> Para continuar com a modificaçao/inserçao de novos formularios costumizados clique em <a href = '/gestao-de-formularios'><b>Continuar</b></a></legend>
<?php
			}
			else
			{
?>
				<legend> Os dados do formulario costumizado nao foram corretamente inseridos<br></legend>
				<legend> para voltar a inserir os dados do formularios costumizado clique em <a href = '/gestao-de-formularios'><b>Continuar</b></a></legend>
<?php
			}
		}
	}
	else
	{
		echo "Nao tem autorização para aceder a esta pagina";
		retorna();
	}
?>