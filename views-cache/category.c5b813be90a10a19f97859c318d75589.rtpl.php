<?php if(!class_exists('Rain\Tpl')){exit;}?><div class="product-big-title-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="product-bit-title text-center">
                    <h2><?php echo htmlspecialchars( $category["descategory"], ENT_COMPAT, 'UTF-8', FALSE ); ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="single-product-area">
    <div class="zigzag-bottom"></div>
    <div class="container">
        <div class="row">
            <!-- Na linha abaixo vamos fazer um loop para apresentar os produtos que temos no nosso banco de dados -->
            <?php $counter1=-1;  if( isset($products) && ( is_array($products) || $products instanceof Traversable ) && sizeof($products) ) foreach( $products as $key1 => $value1 ){ $counter1++; ?>

            <div class="col-md-3 col-sm-6">
                <div class="single-shop-product">
                    <div class="product-upper">
                        <img src="<?php echo htmlspecialchars( $value1["desphoto"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" alt="">
                    </div>
                    <h2><a href="/products/<?php echo htmlspecialchars( $value1["desurl"], ENT_COMPAT, 'UTF-8', FALSE ); ?>"><?php echo htmlspecialchars( $value1["desproduct"], ENT_COMPAT, 'UTF-8', FALSE ); ?></a></h2>
                    <div class="product-carousel-price">
                        <ins>R$<?php echo formatPrice($value1["vlprice"]); ?></ins>
                    </div>  
                    
                    <div class="product-option-shop">
                        <!-- <a class="add_to_cart_button" data-quantity="1" data-product_sku="" data-product_id="70" rel="nofollow" href="/canvas/shop/?add-to-cart=70">Comprar</a> -->
                        
                        <a class="add_to_cart_button" data-quantity="1" data-product_sku="" data-product_id="70" rel="nofollow" href="/cart/<?php echo htmlspecialchars( $value1["idproduct"], ENT_COMPAT, 'UTF-8', FALSE ); ?>/add">Comprar</a>
                    </div>                       
                </div>
            </div>
            <?php } ?>

        
        </div>    
        <!-- todos os trechos originais deste arquivo que se repetia para os produtos foram excluidas, pois acima vamos informar os produtos dinâmicamente. -->
        <div class="row">
            <div class="col-md-12">
                <div class="product-pagination text-center">
                    <nav>
                        <ul class="pagination">
                    <!--     <li>
                            <a href="#" aria-label="Previous">
                            <span aria-hidden="true">«</span>
                            </a>
                        </li> -->
                        <!-- substituimos as linhas acima pela de baixo... -->
                        <?php $counter1=-1;  if( isset($pages) && ( is_array($pages) || $pages instanceof Traversable ) && sizeof($pages) ) foreach( $pages as $key1 => $value1 ){ $counter1++; ?>

                        <li><a href="<?php echo htmlspecialchars( $value1["link"], ENT_COMPAT, 'UTF-8', FALSE ); ?>"><?php echo htmlspecialchars( $value1["page"], ENT_COMPAT, 'UTF-8', FALSE ); ?></a></li>
                        <!-- inibimos também as linhas abaixo -->

                       <!--  <li><a href="#">2</a></li>
                        <li><a href="#">1</a></li>
                        <li><a href="#">3</a></li>
                        <li><a href="#">4</a></li>
                        <li><a href="#">5</a></li>
                        <li>
                            <a href="#" aria-label="Next">
                            <span aria-hidden="true">»</span>
                            </a>
                        </li> -->
                        <?php } ?>

                        </ul>
                    </nav>                        
                </div>
            </div>
        </div>
    </div>
</div>