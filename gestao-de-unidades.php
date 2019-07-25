<?php	
	require_once("custom/php/common.php");
	
$entrou=verificaLogin('manage_unit_types');	
$conecta = conectar();	// vai buscar a funcao coneccao defenida no ficheiro common para fazer a ligação a base de dados
	if($entrou == true)
	{
		if($_REQUEST['estado'] == "") // iniciação default, o programa inicia em qualquer definiçao de estado
		{
			$unidade_tipos = "SELECT * FROM attr_unit_type"; //seleciona os dados da tabela da base de dados attr_unit_type
			$res_unit = mysqli_query($conecta, $unidade_tipos);
			$tuplos_tabela_unidades = mysqli_num_rows($res_unit);					
			if($tuplos_tabela_unidades == 0)// caso nao haja dados na base de dados é mostrado erro
			{				
				echo "<b>Nao ha tipos de unidades na base de dados.</b>";
			}
			else
			{
?>				<link rel="stylesheet" type="text/css" href="ag.css">
				<table class="mytable" style="text-align: left; width: 100%;" border="1" cellpadding="2" cellspacing="2">
				<tr>
					<th>id</th>
					<th>unidade</th>
				</tr>
<?php
				while($linha = mysqli_fetch_assoc($res_unit))
				{
?>				<tr>
					<td> <?php echo $linha["id"]; ?> </td> <!-- imprime o valor corresponde á coluna id na linha atual-->
					<td> <?php echo $linha["name"]; ?> </td>
				</tr>
<?php		
				}
?>		
				</table>

				 
<?php	//-------introduçao de novos tipos de unidades na base de dados----//

			}			
		//formulário mostrado após a tabela
		
?>					
			    <h3><b>Gestao de Unidades - Introducao</b></h3>  
			
			    <form name="gestao_de_unidades" method="POST">
			    <fieldset>
				<legend>Inserir Unidade na base de dados:</legend>

				<legend>Nome da Unidade:</legend>
				<input type="text" name="nome">
				
				<input type="hidden" name="estado" value="inserir">  
				<input type="submit" value="Inserir tipo de unidade">
			    
				</fieldset>
			    </form>
<?php	}
		else if($_REQUEST['estado'] == "inserir")
		{
			$unidade = $_REQUEST['nome'];
?>			
			<legend><h3 class="formato">Gestao de unidades - Insercao</h3></legend>
<?php
			if(empty($unidade))
			{
?>
				<legend>A Unidade inserida nao e validas</legend>
				<legend>Clique em <b><a href="gestao-de-unidades">Continuar</a></b> retomar. </legend>
					
<?php		}
			else
			{
				//inserção da query em formato string
				$query_uindade = 'INSERT INTO `attr_unit_type` (`name`) VALUES ("'.$unidade.'")';
				$insercao_table = mysqli_query($conecta,$query_uindade);
?>	
				<legend>A Insercao de um novo tipo de unidade foi efetuada com sucesso!</legend>
				<legend>Clique em <b><a href="gestao-de-unidades">Continuar</a></b> para avancar </legend> 
				
<?php		}
		}
	}		
	else
	{
		echo "Não tem autorização para aceder a esta página";
		retornar();
	}
?>