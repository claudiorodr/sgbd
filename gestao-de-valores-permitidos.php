<?php
require_once("custom/php/common.php");
$entrou  = verificaLogin('manage_allowed_values');
$conecta = conectar();
if ($entrou == true) {
    if ($_REQUEST["estado"] == "") {
?>
   
    <table class="mytable" style="text-align: left; width: 100%;" border="1" cellpadding="2" cellspacing="2">
    <tr> 
	<!-- table headers -->
    <th> <b> Objeto </b> </th> 
    <th> Id </th> 
    <th> <b> Atributo </b> </th> 
    <th> Id </th> 
    <th> Valores permitidos </th> 
    <th> Estado </th> 
    <th> Ação </th> 
    </tr>
    
    <?php
        
        //Query que devolve os objetos com atributos do tipo enum
        $objetos_query = "SELECT DISTINCT object.id,object.name 
                      FROM attr_allowed_value, attribute, object
                      WHERE object.id=attribute.obj_id 
                      AND attribute.id = attr_allowed_value.attribute_id 
                      AND attribute.value_type='enum'";
        $objetos       = mysqli_query($conecta, $objetos_query);
        $nr_objetos    = mysqli_num_rows($objetos);
        //Percorre todos os objetos 
		
        for ($i = 0; $i < $nr_objetos; $i++) {
            //
			
			
            $objeto_atual    = mysqli_fetch_assoc($objetos);
            //Query que dcevole todos os atributos de um objeto
            $atributos_query = "SELECT DISTINCT attribute.id,attribute.name 
                            FROM attribute
                            WHERE attribute.obj_id='" . $objeto_atual["id"] . "'
                            AND value_type='enum'";
            $atributos       = mysqli_query($conecta, $atributos_query);
            $nr_atributos    = mysqli_num_rows($atributos);
			
            //Query que devolve todos os valores permitidos de um objeto
            $total_valores_atributo_query = "SELECT attr_allowed_value.id 
                                        FROM attr_allowed_value, attribute, object
                                        WHERE object.id=attribute.obj_id 
                                        AND attribute.id = attr_allowed_value.attribute_id
                                        AND attribute.value_type = 'enum'
                                        AND object.id='" . $objeto_atual["id"] . "'";
										
            $total_valores_atributo       = mysqli_query($conecta, $total_valores_atributo_query);
            $nr_total_valores_attr    = mysqli_num_rows($total_valores_atributo);
			
			$attributos_val_null = "SELECT * FROM attribute 
									WHERE attribute.obj_id ='".$objeto_atual["id"]."' 
									AND value_type='enum' 
									AND attribute.id NOT IN (SELECT attr_allowed_value.attribute_id FROM attr_allowed_value)";
			//echo "<br>".$attributos_val_null."<br>";
			
			//a função not in verifica que a expressão seguinte nao tem qualquer valores presentes no argumento pretendido - biblioteca do MYSQL
			$qry_attr_val_null = mysqli_query($conecta, $attributos_val_null);
			$num_attr_val_null = mysqli_num_rows($qry_attr_val_null);
			//echo $num_attr_val_null;
			
			$nr_total_valores_atributo = $nr_total_valores_attr + $num_attr_val_null;
			//adiciona ao numero total de valores nao-nulos de valores permitidos para um certo atributo, 
			//o numero de valores nulos relativos ao atributo na tabela attr_allowed_values
			//echo $nr_total_valores_atributo;	
?>
       
        <!--Numero de valores permitidos = nº de celulas da coluna objeto-->
        <tr> <td rowspan = <?php echo $nr_total_valores_atributo; ?> > <?php echo $objeto_atual["name"]; ?> </td> <?php
            
            // 
            for ($j = 0; $j < $nr_atributos; $j++) {
                // Busca um dos atributos
                $atributo_atual         = mysqli_fetch_assoc($atributos);
                // Busca os valores (permitidos) dessa atributo 
                $valores_atributo_query = "SELECT *
                                       FROM attr_allowed_value 
                                       WHERE attr_allowed_value.attribute_id='" . $atributo_atual["id"] . "'";
                $valores_atributo       = mysqli_query($conecta, $valores_atributo_query);
                // Busca o número de valores permitidos dessa atributo
                $nr_valores_atributo    = mysqli_num_rows($valores_atributo);
                
                //Caso de existirem valores permitidos para esse atributo
                if ($nr_valores_atributo) {
?>
           
            <!--Numero de valores permitidos desse atributo = nº de celulas da coluna id-->
            <td rowspan = <?php
                    echo $nr_valores_atributo;
?> > 
                <?php
                    echo $atributo_atual["id"];
?> </td>
                
            <!--Numero de valores permitidos desse atributo = nº de celulas da coluna atributo-->                
            <td rowspan = <?php
                    echo $nr_valores_atributo;
?> > 
                <a href=<?php
                    echo "gestao-de-valores-permitidos?estado=introducao&atributo=" . $atributo_atual["id"] . "";
?> > 
                    <?php
                    echo "[" . $atributo_atual["name"] . "]";
?> </a></td> <?php
                    
                    //Todos os valores permitidos de um atributo
                    for ($k = 0; $k < $nr_valores_atributo; $k++) {

                        $valor_permitido_atual = mysqli_fetch_assoc($valores_atributo);
?>
                       <td> <?php
                        echo $valor_permitido_atual["id"];
?> </td> 
                        <td> <?php
                        echo $valor_permitido_atual["value"];
?> </td> 
                        <td> <?php
                        echo $valor_permitido_atual["state"];
?> </td>  
                        <td> [editar] [desativar] </td> </tr>
                        <?php
                    }
                }
                //Caso não existam valores permitidos para esse atributo
                else {
?> 
                    <td> <?php
                    echo $atributo_atual["id"];
?> </td> 
                    <td> <a href=<?php
                    echo "gestao-de-valores-permitidos?estado=introducao&atributo=" . $atributo_atual["id"] . "";
?> > 
                        <?php
                    echo "[" . $atributo_atual["name"] . "]";
?> </a></td>
                    <!-- Se para um atributo não houverem tuplos na tabela attr_allowed_value, deve ser apresentado o texto -->
                    <!-- Nas celulas id(valor permitido), valor permitido, estado e ação, é apresentado o texto-->
                    <td colspan = 5> Não há valores permitidos definidos. </td> </tr> <?php
                }
            }
			
        }
?>
   </table>
    <?php
    }
    
    else if ($_REQUEST['estado'] == "introducao") {
        $_SESSION["attribute_id"] = $_REQUEST["atributo"];
		
		$atributo = $_SESSION['attribute_id'];
		
		echo $atributo;

?>
             <h3><b>Gestão de valores permitidos - introdução</b></h3>  
                <form name="gestao_de_valores_permitidos" method="POST">
                <fieldset>
                <legend>Inserir valor permitido na base de dados:</legend>
                <legend>Valor <font color ="red">*obrigatorio*</font>:</legend>
                <input type="text" name="valor_permitido"><br>
                <input type="hidden" name="estado" value="inserir">  
                <input type="submit" value="Inserir valor permitido">
                </fieldset>
                </form>  
<?php
    } else if ($_REQUEST['estado'] == "inserir") {
       
	    $atributo_form   = $_SESSION['attribute_id'];
        $valor_perm_form = $_REQUEST["valor_permitido"];
        $estado_valor    = $_REQUEST["estado"];
?>
         <legend><h3 class="formato">Gestão de valores permitidos - Inserção</h3></legend>
    <?php
        if (empty($atributo_form) || empty($valor_perm_form)) 
            {
				
?>
				
                  <legend>Valor inserido não é valido</legend>
                    <legend>Clique em <b><a href="gestao-de-valores-permitidos">Continuar</a></b> retomar. </legend>
    <?php
        } else {
            $query_valores_perm = "INSERT INTO `attr_allowed_value` (`attribute_id`, `value`, `state`) 
                                   VALUES ('$atributo_form', '$valor_perm_form', 'active' )";
            $inser_tab_obj      = mysqli_query($conecta, $query_valores_perm);
?>
         <legend><i><b>Inseriu os dados de novo valor permitido com sucesso.</b></i></legend>
            <legend>Clique em <b><a href="gestao-de-valores-permitidos">Continuar</a></b> para avançar </legend> 
<?php
        }
    }
} else {
    echo "Não tem autorização para aceder a esta página";
    retorna();
}
?>