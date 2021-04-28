<?php if(!class_exists('Rain\Tpl')){exit;}?><form method="post" action="https://pagseguro.uol.com.br/v2/checkout/payment.html">  
          
        <!-- Campos obrigatórios -->  
        <input name="receiverEmail" type="hidden" value="jsesconetto@gmail.com">  
        <input name="currency" type="hidden" value="BRL">  
        
        <?php $counter1=-1;  if( isset($products) && ( is_array($products) || $products instanceof Traversable ) && sizeof($products) ) foreach( $products as $key1 => $value1 ){ $counter1++; ?> <!-- O rantpl quando utiliza-se o loop ele cria uma váriavel que seria o índice. No caso aqui esta variável chama-se counter, então a cada produtos listados, incrementamos esta váriavel em mais 1 (um) -->
        <input name="itemId<?php echo htmlspecialchars( $counter1+1, ENT_COMPAT, 'UTF-8', FALSE ); ?>" type="hidden" value="<?php echo htmlspecialchars( $value1["idproduct"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">  
        <input name="itemDescription<?php echo htmlspecialchars( $counter1+1, ENT_COMPAT, 'UTF-8', FALSE ); ?>" type="hidden" value="<?php echo htmlspecialchars( $value1["desproduct"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">  
        <input name="itemAmount<?php echo htmlspecialchars( $counter1+1, ENT_COMPAT, 'UTF-8', FALSE ); ?>" type="hidden" value="<?php echo htmlspecialchars( $value1["vltotal"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">  
        <input name="itemQuantity<?php echo htmlspecialchars( $counter1+1, ENT_COMPAT, 'UTF-8', FALSE ); ?>" type="hidden" value="<?php echo htmlspecialchars( $value1["nrqtd"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">  
        <input name="itemWeight<?php echo htmlspecialchars( $counter1+1, ENT_COMPAT, 'UTF-8', FALSE ); ?>" type="hidden" value="<?php echo htmlspecialchars( $value1["vlweight"]*1000, ENT_COMPAT, 'UTF-8', FALSE ); ?>"> <!-- a multiplicação por 1000 é para transformar o valor do produto que esta em dicimal para inteiro --> 
        <?php } ?>

        <input name="reference" type="hidden" value="<?php echo htmlspecialchars( $order["idorder"], ENT_COMPAT, 'UTF-8', FALSE ); ?>"> <!-- o idorder é o nº do pedido que será identificado no pagseguro da nossa venda -->
          
        <!-- Informações de frete (opcionais) -->  
        <input name="shippingType" type="hidden" value="1">
        <input name="shippingAddressPostalCode" type="hidden" value="<?php echo htmlspecialchars( $order["deszipcode"], ENT_COMPAT, 'UTF-8', FALSE ); ?>"> <!-- Tipo do frete --> 
        <input name="shippingAddressStreet" type="hidden" value="<?php echo utf8_encode($order["desaddress"]); ?>"> <!-- End.--> 
        <input name="shippingAddressNumber" type="hidden" value="<?php echo utf8_encode($order["desnumber"]); ?>">  <!-- Nº -->
        <input name="shippingAddressComplement" type="hidden" value="<?php echo utf8_encode($order["descomplement"]); ?>"> <!-- Complemento --> 
        <input name="shippingAddressDistrict" type="hidden" value="<?php echo utf8_encode($order["desdistrict"]); ?>"> 
        <input name="shippingAddressCity" type="hidden" value="<?php echo utf8_encode($order["descity"]); ?>">
        <input name="shippingAddressState" type="hidden" value="<?php echo utf8_encode($order["desstate"]); ?>"> 
        <input name="shippingAddressCountry" type="hidden" value="<?php echo utf8_encode($order["descountry"]); ?>">  
  
        <!-- Dados do comprador (opcionais) -->  
        <input name="senderName" type="hidden" value="<?php echo utf8_encode($order["desperson"]); ?>">  
        <input name="senderAreaCode" type="hidden" value="<?php echo htmlspecialchars( $phone["areaCode"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">  
        <input name="senderPhone" type="hidden" value="<?php echo htmlspecialchars( $phone["number"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">  

        <input name="senderEmail" type="hidden" value="<?php echo htmlspecialchars( $order["desemail"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">  

<!-- Como esta abaixo, o usuário teria que clicar num logo do pagseguro para ir para o pagseguro. Então fizemos as linhas abaixo para ir diretor para o pagseguro -->
        <!-- <input alt="Pague com PagSeguro" name="submit" type="image"
src="https://p.simg.uol.com.br/out/pagseguro/i/botoes/pagamentos/120x53-pagar.gif"/>      -->

</form>  
<script type="text/javascript">
document.forms[0].submit();
</script>