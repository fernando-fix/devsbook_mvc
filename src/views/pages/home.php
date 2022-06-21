<?php $render('header', ['loggedUser' => $loggedUser]); ?>

<section class="container main">

    <?php $render('sidebar', ['home' => 'active']); ?> <!--https://alunos.b7web.com.br/curso/php/perfil-parte-2-menu-->

    <section class="feed mt-10">

        <div class="row">
            <div class="column pr-5">

                <?php $render('feed-editor', ['user' => $loggedUser]); ?>

                <?php foreach($feed['posts'] as $feedItem): ?>
                    
                    <?php $render('feed-item', [
                        'data' => $feedItem,
                        'loggedUser' => $loggedUser
                    ]); ?>

                <?php endforeach; ?>

                <div class="feed-pagination">
                    <?php for($q=0; $q < $feed['pageCount']; $q++): ?>
                        <a class="<?= ($feed['currentPage'] == $q) ? 'active' : '' ?>" href="<?=$base?>/?page=<?= $q ?>"><?= $q + 1 ?></a>
                    <?php endfor; ?>
                </div>
                
            </div>
            <div class="column side pl-5">

                <?php $render('right-side'); ?>
                
            </div>
        </div>

    </section>
</section>
<?php $render('footer'); ?>