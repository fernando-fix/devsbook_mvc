<?php $render('header', ['loggedUser' => $loggedUser]); ?>

<section class="container main">

    <?php $render('sidebar', ['config' => 'active']); ?>

    <section class="feed">

        <div class="row m-10">
            <div class="config">

                <h2>Configurações</h2>

                <!-- Mostra alerta caso tenha alguma mensagem de erro -->
                <?php if (!empty($_SESSION['flash'])) : ?>
                    <div class="alert">
                        <?php
                        echo $_SESSION['flash'];
                        $_SESSION['flash'] = '';
                        ?>
                    </div>
                <?php endif; ?>

                <form action="<?= $base; ?>/upconfig" method="post" autocomplete="off">

                    <div class="config-item">
                        <label for="">Novo Avatar</label>
                        <input type="file" name="" id="">
                    </div>

                    <div class="config-item">
                        <label for="">Nova capa:</label>
                        <input type="file" name="" id="">
                    </div>

                    <hr>

                    <div class="config-item">
                        <label for="name">Nome completo:</label>
                        <input type="text" name="name" id="name" placeholder="Digite aqui o seu nome completo" value="<?= $userInfo->name; ?>">
                    </div>

                    <?php
                    //recebe a data e formata antes de mandar para o formulário
                    //lembrando que tem que usar o imask em js
                    $newDate = explode('-', $userInfo->birthdate);
                    $newDate = $newDate[2] . $newDate[1] . $newDate[0];
                    ?>

                    <div class="config-item">
                        <label for="birthdate">Data de nascimento:</label>
                        <input type="text" name="birthdate" id="birthdate" placeholder="Digite aqui a sua data de nascimento" id="birthdate" value="<?= $newDate ?>">
                    </div>

                    <div class="config-item">
                        <label for="email" autocomplete="off">E-mail:</label>
                        <input type="text" name="email" id="email" placeholder="Digite aqui o seu e-mail" value="<?= $userInfo->email; ?>">
                    </div>

                    <div class="config-item">
                        <label for="city">Cidade:</label>
                        <input type="text" name="city" id="city" placeholder="Digite aqui a sua cidade" value="<?= $userInfo->city; ?>">
                    </div>

                    <div class="config-item">
                        <label for="work">Trabalho:</label>
                        <input type="text" name="work" id="work" placeholder="Digite aqui onde você trabalha" value="<?= $userInfo->work; ?>">
                    </div>

                    <div class="config-item">
                        <label for="newpass1">Nova senha:</label>
                        <input type="password" name="newpass1" id="newpass1" placeholder="Digite aqui a sua nova senha">
                    </div>

                    <div class="config-item">
                        <label for="newpass2">Repita a senha:</label>
                        <input type="password" name="newpass2" id="newpass2" placeholder="Repita a sua nova senha">
                    </div>

                    <button class="button">Salvar</button>
                </form>

            </div>

        </div>

    </section>

</section>

<script src="https://unpkg.com/imask"></script>
<script>
    IMask(
        document.getElementById('birthdate'), {
            mask: '00/00/0000'
        }
    );
</script>

<?php $render('footer'); ?>