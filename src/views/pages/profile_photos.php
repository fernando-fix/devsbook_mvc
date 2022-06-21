<?php $render('header', ['loggedUser' => $loggedUser]); ?>

<section class="container main">

    <?php $render('sidebar', ['photos' => 'active']); ?>

    <section class="feed">

        <?= $render('perfil-header',[
            'user' => $user,
            'loggedUser' => $loggedUser,
            'isFollowing' => $isFollowing
        ]); ?>

        <div class="row">

            <div class="column">

                <div class="box">

                    <div class="box-body">

                        <div class="full-user-photos">

                            <?php if(!$user->photos): ?>

                                Não há fotos aqui

                            <?php else: ?>

                            <?php foreach($user->photos as $photo): ?>
                                
                                <div class="user-photo-item">
                                <a href="#modal-<?=$photo->id;?>" rel="modal:open">
                                    <img src="<?=$base;?>/media/uploads/<?=$photo->body;?>" />
                                </a>
                                <div id="modal-<?=$photo->id;?>" style="display:none">
                                    <img src="<?=$base;?>/media/uploads/<?=$photo->body;?>" />
                                </div>
                            </div>

                            <?php endforeach; ?>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

</section>

<?php $render('footer'); ?>