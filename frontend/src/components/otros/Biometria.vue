<template>
    <div class="container-fluid">  
  
      <b-modal title="VERIFICACIÓN BIOMETRICA" v-model="modal_verificar_huella" size="lg" hide-footer hide-header-close no-close-on-backdrop no-close-on-esc @show="verificacion_correcta=verificacion_error=verificacion_incorrecta=false" @shown="verificarHuella();">
        <div class="container-fluid">
          <div v-if="!verificacion_correcta && !verificacion_incorrecta && !verificacion_error" class="row justify-content-center">
            <div class="col-5 text-center pt-4 pb-3 animated infinite pulse">
              <img class="img-fluid" src="../../assets/img/huella2.png"/>
            </div>
            <div class="col-12 text-center">
              <b-alert variant="primary" show><i class="fas fa-fingerprint mr-2"></i>DIGITA TU HUELLA</b-alert>
            </div>
          </div>
          <div v-if="verificacion_correcta" class="row justify-content-center">
            <div class="col-5 text-center pt-4 pb-3 animated zoomin">
              <img class="img-fluid" src="../../assets/img/huella3.png"/>
            </div>
            <div class="col-12 text-center">
              <b-alert variant="success" show><i class="fas fa-fingerprint mr-2"></i>HUELLA CORRECTA</b-alert>
            </div>
          </div>
          <div v-if="verificacion_incorrecta" class="row justify-content-center">
            <div class="col-5 text-center pt-4 pb-3 animated zoomin">
              <img class="img-fluid" src="../../assets/img/huella4.png"/>
            </div>
            <div class="col-12 text-center">
              <b-alert variant="warning" show><i class="fas fa-fingerprint mr-2"></i>HUELLA INCORRECTA</b-alert>
            </div>
          </div>
          <div v-if="verificacion_error" class="row justify-content-center">
            <div class="col-5 text-center pt-4 pb-3 animated zoomin">
              <img class="img-fluid" src="../../assets/img/huella4.png"/>
            </div>
            <div class="col-12 text-center">
              <b-alert variant="danger" show><i class="fas fa-fingerprint mr-2"></i>{{ verificacion_msj }}</b-alert>
            </div>
          </div>
          <div class="row justify-content-center">
            <div class="col-12 text-center">
              <b-button variant="danger" size="sm" @click="cerrarModalBiometria()">CANCELAR</b-button>
            </div>
          </div>
        </div>
      </b-modal>
  
    </div>
  </template>
  
  <script>
  export default {
    props: ['modal_verificar_huella', 'funcion_ejecucion'],
    name:   'Biometria',
    data(){
      return{
        usuario: '',
        modal_verificar_huella  : false,
        verificacion_correcta   : false,
        verificacion_incorrecta : false,
        verificacion_error      : false,
        verificacion_msj        : '',
        interval_estado_v       : null
      }
    },
    mounted(){ 
    },
    methods:{
      cerrarModalBiometria(){
        clearInterval(this.interval_estado_v);
        this.$emit('cerrar_modal_biometria');
        $.ajax({
          url: "http://127.0.0.1/biometria/cancelar/cancelar.php",
        }).done( resp => {
          console.log(resp);
        }); 
      },


      verificarHuella(){ 
        this.interval_estado_v = setInterval( () => {
          $.ajax({
            url: "http://127.0.0.1/biometria/verificar/estado.php",
          }).done( resp => {
            if(resp.trim() == '204' || resp.trim() == 204){
              $('#ldr_verificacion').hide();
              $('#msj_verificacion').show();
            }
          }); 
        },300);

        axios.post(base_url+'biometria/listarHuellasUsuario').then( resp => {
          if(resp.data.key == 1){
            let huellas = resp.data.huellas
            if((huellas.usb_huella1!=null && huellas.usb_huella1!='') || (huellas.usb_huella2!=null && huellas.usb_huella2!='')){
              //$("#modal_verificar_huella").modal("show");
              $.ajax({
                type: "POST",
                url: "http://127.0.0.1/biometria/verificar/verificar.php",
                data : { huella1: huellas.usb_huella1, huella2: huellas.usb_huella2},
              }).done(resp => {
                clearInterval(this.interval_estado_v);
                var estado = resp.trim();
                //$("#modal_verificar_huella").modal("hide");
                //$('#ldr_verificacion').show();
                //$('#msj_verificacion').hide();
  
                // [ 201 ] : CODIGO DE RESPUESTA DE QUE LA VERIFICACION DE LA HUELLA ES CORRECTA (VERIFICACION CORRECTA)
                if(estado == '201'){      
                  this.verificacion_correcta = true;
                  this.$emit('verificacion_correcta', this.funcion_ejecucion);
                }
      
                // [ 202 ] : CODIGO DE RESPUESTA QUE HUBO UN ERROR EN EL SISTEMA, LECTOR MAL CONECTADO, NO HAY CONEXION, ETC (ERROR DE SISTEMA)
                else if(estado == '202' || (estado != '201' && estado != '202' && estado != '203')){ 
                  this.verificacion_error = true;
                  this.verificacion_msj = 'ERROR DE SISTEMA.';
                }
                
                // [ 203 ] : CODIGO DE QUE LA HUELLA VERIFICADA NO CORRESPONDE A LA REGISTRADA Y ASOCIADA AL RUT (VERIFICACION INCORRECTA)
                else if(estado == '203'){ 
                  this.verificacion_incorrecta = true;
                }
              }).fail(() => {
                clearInterval(this.interval_estado_v);
                this.verificacion_error = true;
                this.verificacion_msj = 'ERROR DE CONEXIÓN CON TERMINAL.';
              });
            }
            else{
              clearInterval(this.interval_estado_v);
              this.verificacion_error = true;
              this.verificacion_msj = 'NO SE ENCONTRARON HUELLAS REGISTRADAS.';
            }
          }
          else{
            clearInterval(this.interval_estado_v);
            this.verificacion_error = true;
            this.verificacion_msj = 'NO SE ENCONTRORON REGISTROS.';
          }
        }).catch(error =>{
          clearInterval(this.interval_estado_v);
          this.verificacion_error = true;
          this.verificacion_msj = 'ERROR DE CONEXIÓN CON SERVIDOR.';
        }); 
      }
    }
  }
  </script>
  
  <style scoped>
  
  </style>