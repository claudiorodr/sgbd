<?php 
	//para ceder às funcoes no ficheiro common
	require_once("custom/php/common.php");
	//verifica se o user esta logado e possui a capability
	$entrou=verificaLogin('manage_attributes');
	$conecta = conectar();
	
if($entrou ==true)
{
	
	if ($_REQUEST["estado"] == "")//se o valor do estado for nulo
	{
			echo "paxa";
			$atrib = "SELECT * FROM attribute"; //seleciona os dados da tabela da base de dados attribute, guarda na variavel
			$atrib_query = mysqli_query($conecta, $atrib);//faz a query usando a instruçao anterior
			$tabela_atribut = mysqli_num_rows($atrib_query);
			
			if($tabela_atribut == 0)
			{//Se não houverem tuplos na tabela attributes, apresentar o texto "Não há propiedades especificadas"
				echo "<i>Não há atributos especificados</i>";
			}
			else// caso contrário, apresentar uma lista com todas os atributos
			{
				//é feita a query para verificar todos os objetos de modo a nao criar objetos a mais na tabela
			$attr_objetos = "SELECT * FROM object ORDER BY name"; //seleciona os dados da tabela da base de dados attribute, guarda na variavel
			$atrr_obj_qry = mysqli_query($conecta, $attr_objetos);//faz a query usando a instruçao anterior
				
			?>
			<!-- criamos no topo da tabela o titulo de cada coluna-->
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
					<th>ação</th>
				</tr>
				<?php
				
				//iniciar a Inserção dos valores da base de dados na tabela//
			while($line_tab = mysqli_fetch_assoc($atrr_obj_qry))//mysqli_fetch_assoc, vai buscar ao result como array associativo
				{
					$num_linha_objetos = "SELECT * from attribute WHERE obj_id = '".$line_tab["id"]."'";
					$num_linha_qry = mysqli_query($conecta, $num_linha_objetos);
					$num_linha_res = mysqli_num_rows($num_linha_qry);
					//tal como na gestao de objetos, para completar o layout da tabela falta apenas o rowspawn quando é feito o <td>
					/*
					<tr>
					<td rowspan = "<?php echo $num_linha_res;?>">
					*/
					if($num_linha_res != 0)
					{
?>				<tr>
					<td rowspan = "<?php echo $num_linha_res;?>">
				<?php
					//é feita uma query para poder obter o nome dos objetos para mostrar na tabela ao inves de mostrar o seu id
					$id_obj = $line_tab["id"];
					$nome_obj = "SELECT object.name FROM object WHERE object.id = '$id_obj'";
					$resul_obj = mysqli_query($conecta, $nome_obj);
					$line_obj = mysqli_fetch_assoc($resul_obj);
					
					echo $line_obj['name'];?> </td> 
				<?php 
				while($line_tab_atributos = mysqli_fetch_assoc($num_linha_qry))//mysqli_fetch_assoc, vai buscar ao result como array associativo
				{
					?>
					<td> <?php echo $line_tab_atributos['id']; ?> </td>
					<td> <?php echo $line_tab_atributos['name']; ?> </td> 
					<td> <?php echo $line_tab_atributos['value_type']; ?> </td>
					<td> <?php echo $line_tab_atributos['form_field_name']; ?> </td> 
					<td> <?php echo $line_tab_atributos['form_field_type']; ?> </td>
					
					<td> <?php 
					//é feita uma query para poder selecionar o nome dos tipos de unidade ao inves de aparecer o seu id
					$id_unid = $line_tab_atributos['unit_type_id'];
					$nome_tipo_unid = "SELECT attr_unit_type.name FROM attr_unit_type WHERE attr_unit_type.id = '$id_unid'";
					$resul_tipo_u = mysqli_query($conecta, $nome_tipo_unid);
					$line_tipo_u = mysqli_fetch_assoc($resul_tipo_u);
					echo $line_tipo_u['name']; ?> </td>			
					
					<td> <?php echo $line_tab_atributos['form_field_order']; ?> </td> 
					<td> <?php echo $line_tab_atributos['form_field_size']; ?> </td>
					<td> <?php echo $line_tab_atributos['mandatory']; ?> </td>
					<td> <?php echo $line_tab_atributos['state']; ?> </td> 
					<td> [editar] [desativar] </td>
					
				</tr>
<?php			
					}
				}
			}				
		}
?>
				</table>
				
	<form name ="gestao-de-atributos" method = "POST">
		<fieldset>
		<legend><h3 class="formato">Gestão de atributos - Introdução</h3></legend>
		
		<fieldset>
		<legend>Nome do atributo <font color ="red">*obrigatorio</font>:</legend>
		<input type = "text" name = "nome_atributo" ><br>
		</fieldset>
		<!------------------------------------->
		<fieldset>
		<legend>Tipo de valor <font color = "red">*obrigatorio</font>:</legend>
<?php
		//os diferentes tipos de valor presentes no atributo value_type da tabela attribute sao guardados num array para depois serem mostrados 1 a 1
		$tipo_valores = get_Enum_Values("attribute", "value_type");
			foreach($tipo_valores as $val_tip)
			{
		?>
			<input type = "radio" name = "tipo_valor" value = "<?php echo $val_tip ?>" > <?php echo $val_tip ?> <br>
<?php		}
?>		
		</fieldset>
		<!------------------------------------->
		<fieldset>
		<legend> Objeto a que irá pertencer este atributo<font color = "red">*obrigatorio</font>:</legend>
		<!---selectbox-->
		<select name = "atr_pertence_obj">
		<?php
		
		$obj_nomes = "SELECT * FROM object";
		//criaçao e execuçao da query para obter os dados sobre os objetos para poder mostra-los dinamicamente no formulario
		$qry_obj_nome =mysqli_query($conecta, $obj_nomes);
		
		//enquanto houver resultados da query sao colocados os valores obtidos no dropdown 
		while($ha_linhas = mysqli_fetch_array($qry_obj_nome))
		{
		?>
		<option value="<?php echo $ha_linhas['id'] ?>"> <?php echo $ha_linhas['name']?> </option>
		<?php
		}
		?>
		</select>
		</fieldset>
		<!------------------------------------->
		<fieldset>
		<legend>Tipo do campo do formulario<font color = "red">*obrigatorio</font>:</legend>
<?php
			//os tipos de campo de formulario presentes no atibuto form_field_type da tabela attibute sao guardados num array para depois serem mostradas todas as opçoes 
		$tipo_campo_form = get_Enum_Values("attribute", "form_field_type");
		
		foreach($tipo_campo_form as $val_tip_camp)
		{
			?>
			<input type = "radio" name = "tipo_campo_form" value = "<?php echo $val_tip_camp?>" > <?php echo $val_tip_camp?> <br>
<?php			
		}
?>
		<fieldset>
		<!------------------------------------->
		<legend> Tipo de unidade:</legend>
		<select name = "tipo_de_unidade">
<?php
		$unid_tipos = "SELECT * FROM attr_unit_type";
		$qry_unid_tipos = mysqli_query($conecta, $unid_tipos);
		?>
			<option></option> <!--é semelhante a ter <option value ==""></option> tem valor nulo-->
		<?php
			while($ha_attr = mysqli_fetch_array($qry_unid_tipos))
			{
?>
			<option value="<?php echo $ha_attr['id'] ?>"> <?php echo $ha_attr['name']?> </option>
<?php
			}
?>
		</select>
		</fieldset>
		<!------------------------------------->
		<fieldset>
		<legend>Ordem do campo no formulario <font color ="red">*obrigatorio</font>:</legend><!--numero superior a 0-->
		<input type = "text" name = "ordem_camp_form" ><br>
		</fieldset>
		<!------------------------------------->
		<fieldset>
		<legend>Tamanho do campo no formulario <font color ="red">*obrigatorio</font>:</legend><!--obrigatório no caso de o tipo de campo ser text ou textbox-->
		<input type = "text" name = "tam_camp_form" ><br>
		</fieldset>
		<!------------------------------------->
		<fieldset>
		<legend>Obrigatório <font color = "red">*obrigatorio</font>:</legend> 
		<input type = "radio" name = "obrigatorio" value = "1" >Sim<br>
		<input type = "radio" name = "obrigatorio" value = "0" >Não<br>
		</fieldset>
		<!------------------------------------->
		<fieldset>
		<legend> Objeto referenciado por este atributo :</legend>
		<select name = "objeto_ref_atributo">
		<?php
		//select box
		//necessario mostrar todos os objetos
		//refere-se ao atributo obj_fk_id da tabela attribute
		$obj_nom = "SELECT * FROM object";
		$qry_obj_ref =mysqli_query($conecta, $obj_nom);
		?>
		<option value=NULL></option>
		<?php
		while($ha_atrib = mysqli_fetch_array($qry_obj_ref))
		{
		?>
		<option value="<?php echo $ha_atrib['id'] ?>"> <?php echo $ha_atrib['name']?> </option>
		<?php
		}
		?>
		</select>
		</fieldset>
		
		<input type="hidden" name="estado" value="inserir">
		<input type="submit" value="Inserir atributo">
		
		</fieldset>
	</form>
<?php
		}
		else if($_REQUEST['estado']=="inserir")
		{
			$nome_atr = $_REQUEST['nome_atributo'];
			$tipo_val = $_REQUEST['tipo_valor'];
			$objeto_atr = $_REQUEST['atr_pertence_obj'];
			$tip_camp_form = $_REQUEST['tipo_campo_form'];
			$tipo_unidade = $_REQUEST['tipo_de_unidade'];
			$ord_camp_f = $_REQUEST['ordem_camp_form'];
			$tamanho_camp_f = $_REQUEST['tam_camp_form'];
			$obrigt = $_REQUEST['obrigatorio'];
			$obj_ref_attr = $_REQUEST['objeto_ref_atributo'];
?>
		
			<legend><h3 class="formato">Gestão de objetos - Inserção</h3></legend>
<?php
			if(($tip_camp_form == 'text' || $tip_camp_form == 'textbox') && empty($tamanho_camp_f))//se o tipo de campo escolhido foi o text ou text box E nao tenha sido indicado nenhum valor para o tamanho do campo
			{
?>
				<legend>Falha na inserçao do tamanho do campo de formulario</legend>
				<legend>ou na escolha do tipo do campo de formulario</legend>
				<legend>Para retificar o erro, clique em <b><a href="gestao-de-atributos">Continuar</a></b>.</legend>
			
<?php		}
			//é verificado se o valor dos campos ordem do campo no formulario e tamanho do campo no formulario sao um numero e para tal é usada a funçao ctype_digit
			else if(!(ctype_digit($ord_camp_f)) || (!(ctype_digit($tamanho_camp_f)) && $tip_camp_form != 'textbox'))
				{
?>				<legend>Falha na inserçao dos campos Ordem do campo no formulario</legend>
				<legend>ou na inserçao do tamanho do campo no formulario</legend>
				<legend>Para retificar o erro, clique em <b><a href="gestao-de-atributos">Continuar</a></b>.</legend>
					
<?php			}
			else if($tip_camp_form == 'textbox' && !(preg_match("/(?P<AA>\d{2})x(?P<BB>\d{2})/", $tamanho_camp_f)) )
			{
?>
				<legend>Falha na inserçao do tamanho do campo de formulario</legend>
				<legend>nao foi inserido nenhum valor ou o valor inserido nao foi do formato AAxBB para o tamanho do campo de formulario</legend>
				<legend>Para retificar o erro, clique em <b><a href="gestao-de-atributos">Continuar</a></b>.</legend>
		
<?php
			}
			else{
				 if(empty($tipo_unidade))
				{
					//NOTA: MODIFICAR A INSERÇAO .. INSERIR PRIMEIRO O ATRIBUTO COM O FORM FIELD NAME VAZIO E DEPOIS INSERIR NO ATRIBUTO INSERIDO, O FORM FIELD NAME COM O RESPETIVO 
					//criação e execuçao de uma query para inserçao do novo atributo na tabela
					
					$query_attr = "INSERT INTO `attribute` (`id` , `name` , `obj_id` , `value_type` , `form_field_type` , `unit_type_id` , `form_field_order` , `form_field_size` , `mandatory` , `state`, `obj_fk_id`) VALUES(NULL , '$nome_atr' , '$objeto_atr' , '$tipo_val' , '$tip_camp_form' ,NULL, '$ord_camp_f' , '$tamanho_camp_f' , '$obrigt' , 'active', $obj_ref_attr )";
					$insert_na_tabela = mysqli_query($conecta, $query_attr);
					$id_atual = mysqli_insert_id($conecta);
					
					$n_obj = "SELECT object.name FROM object WHERE object.id = $objeto_atr ";
					$n_obj_qry = mysqli_query($conecta, $n_obj);
					$res_qry_n_obj = mysqli_fetch_assoc($n_obj_qry);
					$name_qry = $res_qry_n_obj['name'];
					$dimin_obj = substr($name_qry,0,3);
					$nome_prov_camp_form = "$dimin_obj "." $id_atual "."$nome_atr";
					$nome_prov_camp_form = preg_replace('/\s+/', '-', $nome_prov_camp_form);
					
					$atualizar_bd_attr = "UPDATE attribute SET form_field_name ='$nome_prov_camp_form' WHERE id='$id_atual'";
					$qry_atualizar_db = mysqli_query($conecta, $atualizar_bd_attr);
					
					if($qry_atualizar_db)
					{
?>					
					<legend>Inseriu os dados de novo tipo de unidade com sucesso!</legend>
					<legend>Clique em <b><a href="gestao-de-atributos">Continuar</a></b> para avançar </legend> 
<?php			
					}
					else
					{
?>					
					<legend>Ocorreu erro na atualização/inserção dos atributos</legend>
					<legend>Clique em <?php retornar(); ?> para voltar atrás</legend> 
<?php	
					}
				}	
				else{
										
					$query_attr = "INSERT INTO `attribute` (`id` , `name` , `obj_id` , `value_type` ,`form_field_type` , `unit_type_id` , `form_field_order` , `form_field_size` , `mandatory` , `state` , `obj_fk_id`) VALUES(NULL , '$nome_atr' , '$objeto_atr' , '$tipo_val' , '$tip_camp_form' , '$tipo_unidade' , '$ord_camp_f' , '$tamanho_camp_f' , '$obrigt', 'active', $obj_ref_attr )";
					$insert_na_tabela = mysqli_query($conecta, $query_attr);
					$id_atual = mysqli_insert_id($conecta); //é obtido o valor do id do atributo inserido anteriormente
					//o nome do campo do formulario é dado pela vairavel nome_prov_camp_form
					//realizar o update da tabela para nome do campo do formulario seguindo os pres requisitos
	
					$n_obj = "SELECT object.name FROM object WHERE object.id = $objeto_atr ";
					$n_obj_qry = mysqli_query($conecta, $n_obj);
					$res_qry_n_obj = mysqli_fetch_assoc($n_obj_qry);
					$name_qry = $res_qry_n_obj['name'];
					$dimin_obj = substr($name_qry,0,3);
					$nome_prov_camp_form = "$dimin_obj "." $id_atual "."$nome_atr";
					$nome_prov_camp_form = preg_replace('/\s+/', '-', $nome_prov_camp_form);
					 
					echo $nome_prov_camp_form;
					$atualizar_bd_attr = "UPDATE attribute SET form_field_name = '$nome_prov_camp_form' WHERE id='$id_atual'";
					$qry_atualizar_db = mysqli_query($conecta, $atualizar_bd_attr);
					
					if($qry_atualizar_db)
					{
?>					
					<legend>Inseriu os dados de novo tipo de unidade com sucesso!</legend>
					<legend>Clique em <b><a href="gestao-de-atributos">Continuar</a></b> para avançar </legend> 
<?php			
					}
					else
					{
?>					
					<legend>Ocorreu erro na atualização/inserção dos atributos</legend>
					<legend>Clique em <?php retornar(); ?> para voltar atrás</legend> 
<?php	
					}
					}
			}
			
		}
}
else
{
	echo "Não tem autorização para aceder a esta página";
	retornar();
}
?>