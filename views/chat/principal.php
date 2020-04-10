<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="<?php echo Yii::$app->request->baseUrl; ?>/js/jquery-ui.js" defer></script>

<?php

use app\models\Peticiones;
use app\models\Usuarios;
use yii\bootstrap4\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Social';
$this->params['breadcrumbs'][] = $this->title;

$amigos = Usuarios::amigos(Yii::$app->user->id);

$enviar = Url::to(['chat/insertar']);
$historial = Url::to(['chat/historial']);

$js = <<< EOT
        var receptor_id;
        var receptor_username;
        $(document).ready(function () {

            setInterval(function () {
                actualizarHistorial();
            }, 3500);

            $(document).on('click', '.chat', function (e) {
                e.preventDefault();
                receptor_id = $(this).attr('id');
                receptor_username = $(this).attr('data-username');
        
                ventanaChat(receptor_id, receptor_username);
            });

            $(document).on('click', '.btn-enviar', function (e) {
                e.preventDefault();
                var mensaje = $(`#chat_message_\${receptor_id}`).val();
        
                var trimeado = mensaje.trim();
        
                if (trimeado !== '') {
                    // peticion ajax para insertar el mensaje en la DB
                    $.ajax('$enviar', {
                        method: 'POST',
                        data: { receptor_id: receptor_id, mensaje: trimeado },
                        success: function (data) {
                            // limpiar textarea
                            $(`#chat_message_\${receptor_id}`).val('');
                            $(`#historial_receptor_\${receptor_id}`).html(data);

                            // auto scroll
                            $(`.historial`).scrollTop($(`.historial`)[0].scrollHeight - 
                            $(`.historial`)[0].clientHeight);
                        }
                    });
                }
            });


        });

        function ventanaChat(receptor_id, receptor_username) {
            $('body').append(`<div id='receptor_\${receptor_id}'></div>`);
        
            $(`#receptor_\${receptor_id}`).empty();
            $(`#receptor_\${receptor_id}`).attr('id', `receptor_\${receptor_id}`);
            $(`#receptor_\${receptor_id}`).attr('title', `Chat con \${receptor_username}`);
        
            $(`#receptor_\${receptor_id}`).append(`
                    <div id='historial_receptor_\${receptor_id}' class='form-group historial' data-receptorid='\${receptor_id}'>
                    \${recogerHistorial(receptor_id)}
                    </div>
                    <div class='form-group'>
                    <textarea name='chat_message_\${receptor_id}' id='chat_message_\${receptor_id}' class='form-control input-chat'></textarea>
                    <button id='\${receptor_id}' name='enviar' class='btn btn-sm btn-info btn-enviar'>Enviar</button>
                    </div>
                    `);
        
            $(`#receptor_\${receptor_id}`).dialog({
                width: 400,
                // modal: true,
            });
        
            $(`#receptor_\${receptor_id}`).dialog();
        }

        function recogerHistorial(receptor_id) {
            $.ajax('$historial', {
                method: 'POST',
                data: {receptor_id: receptor_id},
                success: function (data) {
                    \$(`#historial_receptor_\${receptor_id}`).html(data);

                    // auto scroll
                    $(`.historial`).scrollTop($(`.historial`)[0].scrollHeight - 
                    $(`.historial`)[0].clientHeight);
                }
            });
        }

        function actualizarHistorial() {
            $(`.historial`).each(function () {
                var receptor_id = $(this).data('receptorid');
                recogerHistorial(receptor_id);
            });
        }
    EOT;

$this->registerJs($js);
?>

<div class="chat-principal">
    <div class="jumbotron">
        <h1>Busca algún usuario</h1>
        <p class="lead">Busca por nombre o email</p>
        <p>
            <?= Html::beginForm(['chat/principal'], 'get') ?>
            <div class="form-group">
                <?= Html::textInput('cadena', $cadena, ['class' => 'form-control']) ?>
            </div>
            <div class="form-group">
                <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary']) ?>
            </div>
            <?= Html::endForm() ?>
        </p>
    </div>

    <div class="body-content">
        <?php if ($usuarios->totalCount > 0) : ?>
            <h3>Usuarios</h3>
            <div class="row">
                <?= GridView::widget([
                    'dataProvider' => $usuarios,
                    'columns' => [
                        'nombre',
                        [
                            'class' => ActionColumn::class,
                            'controller' => 'usuarios',
                            'template' => '{view} {add}',
                            'buttons' => [
                                'add' => function ($url, $model, $key) {
                                    if (!Usuarios::esAmigo($model->id)) {
                                        return Html::a(
                                            'Agregar como amigo',
                                            ['peticiones/crear', 'receptor_id' => $key],
                                            [
                                                'data-method' => 'POST',
                                                'class' => 'btn btn-sm btn-success'
                                            ]
                                        );
                                    } else {
                                        return Html::a(
                                            'Eliminar amigo',
                                            Url::to(['amigos/eliminar', 'id' => $key]),
                                            [
                                                'data-method' => 'POST',
                                                'class' => 'btn btn-sm btn-danger'
                                            ]
                                        );
                                    }
                                },
                            ],
                        ],
                    ],
                ]) ?>
            </div>
        <?php endif ?>


        <?php
        $tengoPeticiones = Peticiones::find()->where(['=', 'receptor_id', Yii::$app->user->id])->exists();

        if ($tengoPeticiones) {
            $peticiones = Peticiones::find()->where(['=', 'receptor_id', Yii::$app->user->id])->all();
        ?>
            <h2>Peticiones de amistad pendientes:</h2>
            <table class="table w-100 peticiones">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($peticiones as $peticion) {
                        // Yii::debug($peticion->receptor_id);
                        $nombre = Usuarios::find()->select('nombre')->where(['=', 'id', $peticion->emisor_id])->scalar();
                    ?>
                        <tr scope="row">
                            <td><?= $nombre ?></td>
                            <td>
                                <span>
                                    <?=
                                        Html::a(
                                            'Aceptar',
                                            Url::to([
                                                'amigos/agregar',
                                                'id' => $peticion->emisor_id,
                                            ]),
                                            ['class' => 'btn btn-sm btn-success']
                                        );
                                    ?>
                                </span>
                                <span>
                                    <?=
                                        Html::a(
                                            'Rechazar',
                                            Url::to([
                                                'peticiones/rechazar',
                                                'emisor_id' => $peticion->emisor_id
                                            ]),
                                            ['class' => 'btn btn-sm btn-danger']
                                        );
                                    ?>
                                </span>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        <?php
        }
        ?>

        <h2 class="mt-4">Mis amigos:</h2>
        <table class="table w-100 usuarios">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody class="cuerpo-usuarios">
                <?php
                foreach ($amigos as $amigo) {
                ?>
                    <tr>
                        <td scope="row"><?= $amigo['nombre'] ?></td>
                        <td>
                            <?php
                            $estado = Yii::$app->db->createCommand("SELECT estado FROM estados WHERE id = :estado_id")
                                ->bindValue(':estado_id', $amigo['estado_id'])
                                ->queryScalar();

                            if ($estado == 'desconectado') {
                            ?>
                                <div class="btn btn-sm btn-danger">
                                    <?= $estado ?>
                                </div>
                            <?php
                            } else {
                            ?>
                                <div class="btn btn-sm btn-success">
                                    <?= $estado ?>
                                </div>
                            <?php
                            }
                            ?>
                        </td>
                        <td>
                            <a href=""><span id="<?= $amigo['id'] ?>" role="button" class="btn btn-sm btn-primary chat" data-username="<?= $amigo['nombre'] ?>">Chat</span></a>

                            <span>
                                <?=
                                    Html::a(
                                        'Eliminar amigo',
                                        Url::to([
                                            'amigos/eliminar',
                                            'id' => $amigo['id']
                                        ]),
                                        ['class' => 'btn btn-sm btn-danger']
                                    );
                                ?>
                            </span>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>