<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="<?php echo Yii::$app->request->baseUrl; ?>/js/jquery-ui.js" defer></script>

<?php

use app\models\Estados;
use app\models\Peticiones;
use app\models\Usuarios;
use yii\bootstrap4\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Social';
$this->params['breadcrumbs'][] = $this->title;

$enviar = Url::to(['chat/insertar']);
$historial = Url::to(['chat/historial']);

$js = <<< EOT
        let receptor_id;
        // let receptor_username;
        $(document).ready(function () {

            setInterval(() => {
                actualizarHistorial();
            }, 3500);

            $(document).on('click', '.chat', function (e) {
                e.preventDefault();
                receptor_id = $(this).attr('id');
                // receptor_username = $(this).attr('data-username');
                sessionStorage.setItem('receptor_username',$(this).attr('data-username'));
        
                ventanaChat(receptor_id, sessionStorage.getItem('receptor_username'));
            });

            $(document).on('click', '.btn-enviar', (e) => {
                e.preventDefault();
                const mensaje = $(`#chat_message_\${receptor_id}`).val();
        
                const trimeado = mensaje.trim();

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

            // enviar mensajes cuando se presiona enter
            $(document).on('keydown','.input-chat', (e) => {
                e = e || window.event;
                const keyCode = e.keyCode || e.which;

                if (keyCode == 13) {
                    const mensaje = $(`#chat_message_\${receptor_id}`).val();
        
                const trimeado = mensaje.trim();

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
                }
            });


        });

        function ventanaChat(receptor_id, receptor_username) {
            $('body').append(`<div id='receptor_\${receptor_id}'></div>`);
        
            $(`#receptor_\${receptor_id}`).empty();
            $(`#receptor_\${receptor_id}`).attr('id', `receptor_\${receptor_id}`);
            $(`#receptor_\${receptor_id}`).attr('title', `Chat con \${receptor_username}`);
        
            $(`#receptor_\${receptor_id}`).append(`
                    <div id='historial_receptor_\${receptor_id}' class='form-group historial text-break' data-receptorid='\${receptor_id}'>
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
                const receptor_id = $(this).data('receptorid');
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
                            'template' => '{perfil}{add}',
                            'buttons' => [
                                'perfil' => function ($url, $model, $key) {
                                    return Html::a(
                                        'Ver Perfil',
                                        ['usuarios/perfil', 'id' => $key],
                                        ['class' => 'btn btn-sm btn-info']
                                    );
                                },
                                'add' => function ($url, $model, $key) {
                                    if (!Usuarios::esAmigo($model->id)) {
                                        return Html::a(
                                            'Agregar como amigo',
                                            ['peticiones/crear'],
                                            [
                                                'class' => 'btn btn-sm btn-success ml-1',
                                                'data' => [
                                                    'method' => 'POST',
                                                    'params' => ['receptor_id' => $key],
                                                ],
                                            ]
                                        );
                                    } else {
                                        return Html::a(
                                            'Eliminar amigo',
                                            ['amigos/eliminar'],
                                            [
                                                'class' => 'btn btn-sm btn-danger ml-1',
                                                'data' => [
                                                    'method' => 'POST',
                                                    'confirm' => '¿Estás seguro de que desea eliminar a este usuario como amigo?',
                                                    'params' => ['id' => $key],
                                                ],
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
                                            ['amigos/agregar'],
                                            [
                                                'class' => 'btn btn-sm btn-success',
                                                'data' => [
                                                    'method' => 'POST',
                                                    'params' => ['id' => $peticion->emisor_id],
                                                ],
                                            ]
                                        );
                                    ?>
                                </span>
                                <span>
                                    <?=
                                        Html::a(
                                            'Rechazar',
                                            ['peticiones/rechazar'],
                                            [
                                                // 'data-method' => 'POST',
                                                'class' => 'btn btn-sm btn-danger ml-1',
                                                'data' => [
                                                    'method' => 'POST',
                                                    'params' => ['emisor_id' => $peticion->emisor_id],
                                                ],
                                            ]
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
        <?=
            GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'attribute' => 'imagen',
                        'label' => 'Imagen',
                        'value' => function ($model, $widget) {
                            $modelAmigo = Usuarios::findOne($model['id']);
                            $fotoAmigo = $modelAmigo->getImagen();
                            return Html::img($fotoAmigo, ['alt' => 'Imagen de Amigo', 'class' => 'imagen-amigos img-fluid rounded']);
                        },
                        'format' => 'html',
                    ],
                    'nombre',
                    [
                        'attribute' => 'estado',
                        'value' => function ($model, $widget) {
                            $estado = Estados::find()->select('estado')
                                ->where(['=', 'id', $model['estado_id']])->scalar();

                            if ($estado === 'desconectado') {
                                return Html::button('desconectado', [
                                    'class' => 'btn btn-sm btn-danger',
                                ]);
                            } else {
                                return Html::button('conectado', [
                                    'class' => 'btn btn-sm btn-success',
                                ]);
                            }
                        },
                        'format' => 'raw'
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{chat} {ver} {eliminar}',
                        'buttons' => [
                            'chat' => function ($url, $model) {
                                return Html::a('Chat', null, [
                                    'id' => $model['id'],
                                    'role' => 'button',
                                    'class' => 'text-white btn btn-sm btn-primary chat',
                                    'data-username' => $model['nombre'],
                                ]);
                            },
                            'ver' => function ($url, $model) {
                                return Html::a(
                                    'Ver Perfil',
                                    Url::to(['usuarios/perfil', 'id' => $model['id']]),
                                    [
                                        'class' => 'btn btn-sm btn-info',
                                    ],
                                );
                            },
                            'eliminar' => function ($url, $model) {
                                return Html::a(
                                    'Eliminar amigo',
                                    ['amigos/eliminar'],
                                    [
                                        'class' => 'btn btn-sm btn-danger',
                                        'data' => [
                                            'method' => 'POST',
                                            'confirm' => '¿Estás seguro de que desea eliminar a este usuario como amigo?',
                                            'params' => ['id' => $model['id']],
                                        ],
                                    ]
                                );
                            },
                        ],
                    ],
                ],
            ]);
        ?>
    </div>
</div>