<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Formulario - CargoPolo</title>
        <!-- Compiled and minified CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

        <style>   
            .nav-wrapper {
                background-color: #0015ff !important;
            }
            .nav-content {
            background-color: #0051ff !important;
            }


            .mensagem_bemvindo {
                margin-top: 100px;
                text-align: center; 
                font-family:Arial, Helvetica, sans-serif;
            }


        </style>
</head>
<body>
        <!-- Compiled and minified JavaScript -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
 

   
        <nav class="nav-extended">
            <div class="nav-wrapper">
            <a href="#" class="brand-logo center"><b>Formulário - Grupo Cargo Polo</b></a>
            <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
            <ul id="nav-mobile" class="right hide-on-med-and-down">


                <li><a href="/sobre"><i class="fas fa-info-circle"></i></a></li>
            
            </ul>
            </div>
            <div class="nav-content">
            <ul class="tabs tabs-transparent">
                <li class="tab"><a class="active" href="/">Home</a></li>
                <li class="tab"><a class="fornecedor" href="fornecedor_juridico">Fornecedor P.J.</a></li>
                <li class="tab"><a class="fornecedor_fisico" href="fornecedor_fisico">Fornecedor Pessoa Física</a></li>

            </ul>
            </div>
        </nav>

        <script>
            window.onload = function() {
                alert('Seja bem vindo! Escolha uma das opções de preenchimento de formulário!');
                // Você também pode redirecionar para outra página após o pop-up se desejar
            //window.location.href = '/fornecedor_fisico';

            };
        </script>

        <section class="elementor-section elementor-top-section elementor-element elementor-element-1194f43 elementor-section-height-full elementor-section-boxed elementor-section-height-default elementor-section-items-middle elementskit-parallax-multi-container" data-id="1194f43" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;video&quot;,&quot;background_video_link&quot;:&quot;https:\/\/benasantana.com\/wp-content\/uploads\/2024\/03\/cargopolo-site.mp4&quot;,&quot;background_play_on_mobile&quot;:&quot;yes&quot;,&quot;ekit_has_onepagescroll_dot&quot;:&quot;yes&quot;}">
            <div class="elementor-background-video-container">
                                <video class="elementor-background-video-hosted elementor-html5-video" autoplay="" muted="" playsinline="" loop="" src="https://benasantana.com/wp-content/uploads/2024/03/cargopolo-site.mp4" style="width: 100%; height: 100%;"></video>
                        </div>
            <div class="elementor-container elementor-column-gap-default">
        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-d1215db" data-id="d1215db" data-element_type="column">
        <div class="elementor-widget-wrap">
        </div>
        </div>
        </div>
        </section>
</body>
</html>