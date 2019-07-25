<?php 
	require_once("custom/php/common.php");
	
$entrou=verificaLogin('manage_objects');
$conecta = conectar();
	if($entrou == true)
	{
		if ($_REQUEST["estado"] == "")
		{
			$objetos = "SELECT * FROM `object` ORDER BY name";
			$resultado = mysqli_query($conecta, $objetos);	
			$num_linhas = mysqli_num_rows($resultado); // indicaçao do numero de linhas presentes na tabela objetos de modo a poder fazer o ajuste dinamico
			
			//-------------Mostrar tabela com os seus tuplos-----------//
			if ($num_linhas == 0) //se a tabela estiver fazia é feita a indicaçao da inexistencia de tuplos/objetos
			{
				echo "<b>Nao há objetos.</b>";
			}
			else
			{
?>
				<link rel="stylesheet" type="text/css" href="ag.css">
				<table class="mytable" style="text-align: left; width: 100%;" border="1" cellpadding="2" cellspacing="2">
				<tr>
				<!--Cabeçalhos da tabela-->
					<th>tipo de objeto</th>			
					<th>ID</th>
					<th>Nome do objeto</th>
					<th>Estado</th>
					<th>Ação</th>
				</tr>
<?php
				/*A inserção dos valores da tabela é feita aqui*/
				$tipo_objetos_tab = "SELECT * FROM obj_type order by id";
				$qry_tipo_obj_tab = mysqli_query($conecta, $tipo_objetos_tab);
				
				while($linha_tab_obj = mysqli_fetch_assoc($qry_tipo_obj_tab))
				{
					
				//para completar completamente falta apenas acrescentar o rowspan quando é feito o td para evitar a redundancia de informação na tabela
				$num_linhas = "SELECT * FROM obj_type, object WHERE obj_type.id = '".$linha_tab_obj["id"]."' and object.obj_type_id = obj_type.id";
				$qry_num_linhas = mysqli_query($conecta, $num_linhas);
				$numtotal_linhas = mysqli_num_rows($qry_num_linhas);
				
					if($numtotal_linhas != NULL)
					{		
?>	
					<tr>
					<td rowspan = <?php echo $numtotal_linhas;?>> 
					<?php
					//query de traduçao doid do objeto para o nome do mesmo
					$nome_tipo_obj = "SELECT * FROM obj_type WHERE id = '".$linha_tab_obj["id"]."'";
					$qry_nome_tipo_obj = mysqli_query($conecta, $nome_tipo_obj);
					$res_nome_tipo_obj = mysqli_fetch_assoc($qry_nome_tipo_obj);
					echo $res_nome_tipo_obj["name"]; ?> </td>
					<?php
					
					$obj_info = "SELECT * FROM object WHERE obj_type_id ='".$linha_tab_obj["id"]."'";
					$qry_obj_info = mysqli_query($conecta, $obj_info);
					
						while($linha_tab = mysqli_fetch_assoc($qry_obj_info))
						{
					?>
					
					<td> <?php echo $linha_tab["id"]; ?> </td>
					<td> <?php echo $linha_tab["name"]; ?> </td> 
					<td> <?php echo $linha_tab["state"]; ?> </td>
					<td> [editar] [desactivar] </td>
					</tr>
<?php					}
					}
				}
?>
				</table>
			
<?php		
			}
		//Formulario para inserção de novos dados após a tabela 
?>
		<form name ="gestao_de_objetos" method = "POST">
		<fieldset>
		<legend><h3 class="formato">Gestão de objetos - Introdução</h3></legend>
		
		<fieldset>
		<legend>Nome do objeto <font color ="red">*obrigatorio</font>:</legend>
		<input type = "text" name = "nome_objeto" ><br>
		</fieldset>
		
		<fieldset>
		<legend>Tipo de objeto <font color = "red">*obrigatorio</font>:</legend>
		<?php
		
		//seleção dos tipos de objetos a partir da tabela da base de dados
		$tipo_objeto = "SELECT * FROM obj_type";
		$query_tipo_obj = mysqli_query($conecta, $tipo_objeto);
		
		while($opcoes_tipo_objeto = mysqli_fetch_assoc($query_tipo_obj))
		{
?>	
		<input type = "radio" name = "tipo_obj" value ="<?php echo $opcoes_tipo_objeto['id']?>"><?php echo $opcoes_tipo_objeto['name'] ?><br>
<?php	
		}
?>
		</fieldset>
		
		<fieldset>
		<legend>Estado <font color = "red">*obrigatorio</font>:</legend> 
		<input type = "radio" name = "state" value = "activo" >Ativo<br>
		<input type = "radio" name = "state" value = "inactivo" >Inativo<br>
		</fieldset>
		
		<input type="hidden" name="estado" value="inserir">
		<input type="submit" value="Inserir objeto">
		
		</fieldset>
		</form>
	
<?php
		}
		else if($_REQUEST['estado'] == "inserir")
		{
			$nome_form = $_REQUEST['nome_objeto'];
			$tipo_obj_form = $_REQUEST['tipo_obj'];
			$estado_form = $_REQUEST['state'];
			?>
		<legend><h3 class="formato">Gestão de objetos - Inserção</h3></legend>
<?php					
		if(empty($nome_form) || empty($tipo_obj_form) || empty($estado_form))//versao de teste : todos os componentes devem estar preenchidos de modo a poder inserir um valor corretamente
			{
?>
				<legend>Unidades inseridas nao são validas</legend>
				<legend>Clique em <b><a href="gestao-de-objetos">Continuar</a></b> retomar. </legend>
				
<?php		}
		else{
			$query_objeto = "INSERT INTO `object` VALUES ( NULL, '$nome_form','$tipo_obj_form', '$estado_form')";
			$inser_tab_obj = mysqli_query($conecta,$query_objeto);
		?>
		<legend>Inseriu os dados de novo objeto com sucesso!</legend>
		<legend>Clique em <b><a href="gestao-de-objetos">Continuar</a></b> para avançar </legend> 
<?php			
			}
		}
	}
	else{
		echo "Não tem autorização para aceder a esta página";
		retornar();
	}
?>