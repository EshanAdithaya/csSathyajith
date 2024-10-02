<?php include('header.php') 
?>
    <main class="main">
        <section class="product section container" id="products">
            <h2 class="section__title-center">
                Check out our <br> products
            </h2>

            <p class="product__description">
                Here are some selected plants from our showroom, all are in excellent 
                shape and has a long life span. Buy and enjoy best quality.
            </p>

            <div class="product__container grid">
                <?php foreach ($products as $product): ?>
                <article class="product__card" data-product-id="<?php echo $product['id']; ?>">
                    <div class="product__circle"></div>

                 
                    <img src="<?php echo htmlspecialchars('dashboard/' . $product['image_path']); ?>" alt="" class="product__img">

                    <h3 class="product__title"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <span class="product__price">$<?php echo number_format($product['price'], 2); ?></span>

                    <button class="button--flex product__button add-to-cart-btn" <?php echo $product['stock'] > 0 ? '' : 'disabled'; ?>>
                        <i class="ri-shopping-bag-line"></i>
                    </button>
                </article>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

  <?php include('footer.php') ?>