
const $icono = document.getElementById('icono-indicador');
window.addEventListener('scroll', function(){
  const largo_pantalla = window.outerHeight;
  const largo_documento = document.body.clientHeight;
  let scroll = window.scrollY;

  // si largo de documento es x2 veces mayor que largo pantalla
  if( Math.floor(largo_documento / largo_pantalla) >= 2){

    //si scroll > largo de pantalla
    if(scroll >= Math.floor(largo_pantalla / 2)){
      //$IR_A[0].classList.add('final');
      $('.ir-a').addClass('final visible');
      $('.ir-a').attr('id', 'ir-final');
      $('.ir-a').attr('data-bs-original-title', 'Ir al final de la pagina');
      if($icono.classList.contains('ti-arrow-big-up-lines')){
        $icono.classList.replace('ti-arrow-big-up-lines', 'ti-arrow-big-down-lines');
      }else{
        $icono.classList.add('ti-arrow-big-down-lines');
      }
    }

    // si scroll == largo documento: ir arriba
    if(scroll >= Math.floor(largo_documento / 2)){

      $('.ir-a').removeClass('final visible').addClass('inicio visible');
      $('.ir-a').attr('id', 'ir-inicio');
      $('.ir-a').attr('data-bs-original-title', 'Ir al principio de la pagina');
      if($icono.classList.contains('ti-arrow-big-down-lines')){
        $icono.classList.replace('ti-arrow-big-down-lines', 'ti-arrow-big-up-lines');
      }else{
        $icono.classList.add('ti-arrow-big-up-lines');
      }
    }

    $('.ir-a').off('click').on('click', function(event){
      event.preventDefault();
      if($(this).attr('id') == 'ir-final'){
        window.scrollTo({
            top: largo_documento,
            left: 100,
            behavior: 'smooth'
          });
      }
      if($(this).attr('id') == 'ir-inicio'){
        window.scrollTo({
            top: 0,
            left: 100,
            behavior: 'smooth'
          });
      }
    });
    //console.clear()

  }else{
    $('.ir-a').removeClass('final visible').removeClass('inicio');
  }


});

// loader app
document.getElementById("body-app").onload = () => setTimeout(removeLoader, 1000) ;
const removeLoader = () => {
    const loader = document.getElementById("loader-app");     
    loader.classList.remove('show');      
}

