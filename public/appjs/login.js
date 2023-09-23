import {mostrar_ocultar_clave, mostrarNotificaciones, Redireccionar} from './modules.js';
import {ValidarEmail} from './modules.js';

$(document).ready(function(){
    //spinner
    $('#spinnerLogin').addClass('elementoOculto');
    //boton ver clave
    $('#opcionesClave').unbind('click').bind('click', function(event){
        event.preventDefault();
        //opciones
        const $opciones = {
            boton:$(this),
            input: $('#claveUsuario'), //el input
            ocultarClave: $('#ocultarClave'), //svg ocultar
            mostrarClave: $('#mostrarClave') //svg mostrar
        };
        mostrar_ocultar_clave($opciones);
    });


    //checkear validez email cuando se escribe
    $("#emailUsuario").on('keyup', function(){
        var $email = $(this).val();
        if( ValidarEmail($email) ){
            $(this).removeClass('is-invalid');
            $(this).addClass('is-valid');
        }else{
            $(this).removeClass('is-ivalid');
            $(this).addClass('is-invalid');
        }
    });
    
    // mostrar una notificacion de avisto si el email no es valido
    $("#emailUsuario").on('blur', function(){
        if(!(ValidarEmail($(this).val())) ){
            mostrarNotificaciones({
                tipo: 'aviso',
                titulo: '¡ Revisa el email !',
                mensaje: 'El valor que has introducido como email no tiene un formato correcto'
            });
        }
    });

    // verificar la longitud de la clave
    $("#claveUsuario").on('blur', function() {
        var $longitud = $(this).val().length;
        var $mensaje = '';

        if ($longitud <= 4 ){
            $mensaje = 'La contraseña tiene que tener como mínimo 5 carracteres';
        }
        if($longitud >= 16 ){
            $mensaje = 'La contraseña no puede tener mas de 15 carracteres';
        }

        if( $mensaje != '' ){
            mostrarNotificaciones({
                tipo: 'aviso',
                titulo: '¡ Revisa la clave !',
                mensaje: $mensaje
            });
        }

    });


    //login
    $("#formularioLogin").unbind('submit').bind('submit', function(event) {
        event.preventDefault();
        $('#spinnerLogin').removeClass('elementoOculto');
        $('#textoLogin').text('Validando formulario...');

        //validar formulario
        var $email = $("#emailUsuario").val();
        var $clave = $("#claveUsuario").val();

        if($email == '' || !(ValidarEmail($email))){
            $('#emailUsuario').removeClass('is-ivalid');
            $('#emailUsuario').addClass('is-invalid');
            $email = false;

        }else{
            $('#emailUsuario').removeClass('is-invalid');
            $('#emailUsuario').addClass('is-valid');
        }

        if($clave == '' || $clave.length <= 4 || $clave.length >= 16 ){
            $('#claveUsuario').removeClass('is-ivalid');
            $('#claveUsuario').addClass('is-invalid');
            $clave = false;
        }else{
            $('#claveUsuario').removeClass('is-invalid');
            $('#claveUsuario').addClass('is-valid');
        }

        if( $email && $clave ){ // todo ok
            var formulario = $(this);
            $.ajax({
                url:formulario.attr('action'),
                type:formulario.attr('method'),
                data: formulario.serialize(),
                dataType: "json",
                beforeSend: function(){
                    $('#spinnerLogin').removeClass('elementoOculto');
                    $('#textoLogin').text('Verificando tus datos...');
                    $('#claveUsuario').removeClass('is-invalid').removeClass('is-valid');
                    $('#emailUsuario').removeClass('is-invalid').removeClass('is-valid');
                },
                success: function(respuesta){
                    $('#spinnerLogin').addClass('elementoOculto');
                    //si exito
                    console.log(respuesta)
                    if( respuesta.exito === true && respuesta.redirigir == true ){
                        $('#textoLogin').text('¡¡ Datos correctos !!'); //btn login
                        mostrarNotificaciones({ // notificacion
                            tipo: 'exito',
                            titulo: '¡ Datos Correctos !',
                            mensaje: respuesta.mensaje
                        });

                        // redireccion
                        Redireccionar(respuesta.url,3000);
                        

                    }else{
                        //boton login
                        $('#spinnerLogin').addClass('elementoOculto');
                        $('#textoLogin').text('Volver a intentar...');

                        /*
                        if(respuesta.mensajes.match(/Email/g) ){
                            $('#emailUsuario').removeClass('is-invalid').removeClass('is-valid');
                            $('#emailUsuario').focus();
                        }

                        if(respuesta.mensajes.match(/contraseña/g)){
                            $('#claveUsuario').removeClass('is-invalid').removeClass('is-valid');
                            $('#claveUsuario').focus();
                        }*/


                        //notificacion
                        mostrarNotificaciones({
                            tipo: 'aviso',
                            titulo: '¡ Acceso no concedido !',
                            mensaje: respuesta.mensaje
                        });
                    }
                },
                error: function(error){
                    console.log(error.responseText)
                    console.log('HA OCURIDO UN ERROR:');
                }
            });


        }else{ // no ok

            $("#spinnerLogin").delay(1000).show(10, function() {
                $(this).addClass('elementoOculto');
                $('#textoLogin').text('Volver a intenar...');

            });

            mostrarNotificaciones({
                tipo: 'error',
                titulo: '¡ Revisa el formulario !',
                mensaje: 'El formulario contiene errores, revisa los campos marcados...'
            });
        } //end if else email and clave

        



        
    });
    
});

