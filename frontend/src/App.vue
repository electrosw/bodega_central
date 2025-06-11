<template>
  <div id="app">
    <loader-component :cargando_pagina="cargando_pagina"/>
    <div class="fixed-nav sticky-footer bg-dark sidenav-toggled" id="page-top">
      <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top pl-2" id="mainNav">
        <a class="navbar-brand" href="<?=base_url?>"><i class="fas fa-warehouse fa-fw fa-lg mr-3 ml-1"></i>BODEGA CENTRAL</a>
        <button class="navbar-toggler navbar-toggler-right" type="button" @click="muestraBarraSuperior()" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav navbar-sidenav" id="menuLateral" style="min-width: 55px;">

            <li  v-for="(item, index) in menu" v-bind:key="index" class="nav-item item_lateral" :id="'item_'+item.nombre" style="min-width: 55px;" v-b-tooltip.hover.right :title="item.nombre" @click="cambiaVista(item.nombre)">
              <a class="nav-link" style="cursor:pointer;">
                <span v-html="item.icono"></span>
                <span v-if="barra" class="nav-link-text texto_barra" v-text="item.nombre"></span>
              </a>
            </li>

            <!-- <li class="nav-item item_lateral" :id="'item_'+index" style="width: 55px;" v-b-tooltip.hover.right :title="'Cambio de clave'" @click="modal_cambiar_clave=true; ocultaMenu();">
              <a class="nav-link" style="cursor:pointer;">
                <i class="fas fa-key fa-fw mr-2"></i>
                <span class="nav-link-text texto_barra" v-text="'Cambio de clave'" style="display:none;"></span>
              </a>
            </li> -->

            <!-- FIN DEL MENU LATERAL -->
          </ul>
          <ul class="navbar-nav sidenav-toggler">
            <li class="nav-item item_lateral" style="min-width: 55px;">
              <a v-if="barra" @click="ocultaBarra();" class="nav-link text-center">
                <i class="fas fa-angle-left"></i>
              </a>
              <a v-else @click="muestraBarra();" class="nav-link text-center">
                <i class="fas fa-angle-right"></i>
              </a>
            </li>
          </ul>
          <!-- <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <a @click="modal_cerrar_sesion=true;" id="logout-btn" class="nav-link">
              <i class="fas fa-sign-out-alt"></i> CERRAR SESIÓN</a>
            </li>
          </ul> -->
        </div>
      </nav>
    <div class="content-wrapper">
      <!-- <div class="container-fluid pt-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item  ml-2"><a href="<?=base_url?>">INICIO</a></li>
                <li class="breadcrumb-item active"><span v-text="nom_empresa"></span></li>
                <li class="breadcrumb-item active" v-if="num_empresa == id_electrocom"><span v-text="'/ '+nom_bodega"></span></li>
            </ol>
        </nav> 
      </div> -->
      
      <!-- DIV DINAMICO QUE CONTIENE EL CUERPO DE LA PAGINA -->
      <!-- MENU ADMINISTRADOR -->
      <div class="mt-4">
        <ingreso-bodega-component       v-if="menu[0].visible"/>
        <salida-bodega-component        v-if="menu[1].visible"/>
        <bodega-component               v-if="menu[2].visible"/>
        <informes-bodega-component      v-if="menu[3].visible"/>
        <valorizacion-component         v-if="menu[4].visible"/>

      </div>

    </div>
      <!-- Scroll to Top -->
      <a class="scroll-to-top rounded" href="#page-top" style="margin-bottom: 55px;">
        <i class="fas fa-angle-up"></i>
      </a>
    </div>

    <!--<b-modal :content-class="'border-0 rounded-0 mb-5'" v-model="modal_selecciona_local" title="SELECCIONE EMPRESA/LOCAL" size="lg" hide-footer hide-header-close no-close-on-esc no-close-on-backdrop centered>
      <label>EMPRESA</label>
      <select v-model="empresa_selecc" class="form-control">
        <option :key="index" v-for="(empresa, index) in empresas" :value="empresa" v-text="empresa.emp_nombre"></option>
      </select>
      <div v-if="empresa_selecc.emp_id == id_electrocom">
        <label class="mt-3">BODEGA</label>
        <select v-model="bodega_selecc" class="form-control">
          <option :key="index" v-for="(bodega, index) in bodegas" :value="bodega" v-text="bodega.bod_nombre"></option>
        </select>
      </div>
      <div class="row justify-content-center mt-4 mb-2">
        <button class="btn btn-success col-4" @click="setEmpresa()"><i class="fas fa-sign-in-alt mr-2"></i>ACEPTAR</button>
      </div>
     
    </b-modal>-->

    <b-modal v-model="modal_cambiar_clave" title="CAMBIO DE CLAVE" hide-footer no-close-on-backdrop no-close-on-esc>
      <div class="row">
        <div class="col-2 pr-0">
          <h6 class="text-left">NOMBRE</h6>
        </div>
        <div class="col-1 p-0">
          <h6 class="text-center font-weight-light">:</h6>
        </div>
        <div class="col-9 pl-0">
          <h6 class="text-left font-weight-light text-uppercase">{{usuario.nombres+' '+usuario.apellidos}}</h6>
        </div>
      </div>
      <div class="row mb-3 pb-2 border-bottom">
        <div class="col-2 pr-0">
          <h6 class="text-left">RUT</h6>
        </div>
        <div class="col-1 p-0">
          <h6 class="text-center font-weight-light">:</h6>
        </div>
        <div class="col-9 pl-0">
          <h6 class="text-left font-weight-light" v-if="usuario.rut!=''">{{formateaRut(usuario.rut+'-'+usuario.dv)}}</h6>
        </div>
      </div>

      <h6 class="text-left font-weight-bold">INGRESA TU CLAVE ACTUAL</h6>
      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fas fa-lock fa-fw"></i></span>
        </div>
        <b-input v-model="usuario.clave_actual" type="password" class="form-control" placeholder="Clave actual"/>
      </div>

      <h6 class="text-left font-weight-bold mt-4">NUEVA CLAVE</h6>
      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fas fa-lock fa-fw"></i></span>
        </div>
        <b-input v-model="usuario.clave_nueva1" type="password" class="form-control" placeholder="Ingresa una clave de seguridad" :state="validaClave(usuario.clave_nueva1)"/>
      </div>
      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fas fa-lock fa-fw"></i></span>
        </div>
        <b-input v-model="usuario.clave_nueva2" :disabled="!validaClave(usuario.clave_nueva1)" type="password" class="form-control" placeholder="Reingresa tu nueva clave" :state="validaClave(usuario.clave_nueva1) && usuario.clave_nueva1==usuario.clave_nueva2"/>
      </div>
      <h6 class="text-center font-weight-bold mt-2"><i class="fas fa-exclamation-circle fa-fw"></i> <span class="font-weight-light">La nueva clave debe contener mayúsculas, minúsculas y números, con un mínimo de 6 caracteres de largo.</span></h6>
      <div class="row justify-content-beteween border-top pt-3">
        <div class="col-6">
          <button @click="modal_cambiar_clave=false; usuario={clave_actual:'', clave_nueva1:'', clave_nueva2:''}" class="btn btn-block btn-sm btn-danger"><i class="fas fa-times fa-fw mr-2"></i>CANCELAR</button>
        </div>
        <div class="col-6">
          <button @click="guardaClave()" :disabled="!validaInputsClave()" class="btn btn-block btn-sm btn-success"><i class="fas fa-check fa-fw mr-2"></i>GUARDAR</button>
        </div>
      </div>
    </b-modal>

    <b-modal v-model="modal_cerrar_sesion" title="CERRAR SESIÓN" :footer="false" hide-footer>
      <h5 class="text-center">¿SEGURO QUE DESEA SALIR?</h5>
      <div class="row justify-content-between border-top pt-1 mt-4">
        <div class="col-lg-5 col-md-6 col-sm-12 mt-2">
          <button @click="modal_cerrar_sesion=false;" class="btn btn-secondary btn-sm btn-block"><i class="fas fa-ban"></i> CANCELAR</button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12 mt-2">
          <button @click="cerrarSesion();" class="btn btn-primary btn-sm btn-block"><i class="fas fa-sign-out-alt"></i> CERRAR SESIÓN</button>
        </div>
      </div>
    </b-modal>

  </div>
</template>

<script>

export default {
  name: 'App',
  data(){
    return {
      id_electrocom         : 2,
      num_empresa           : 0,
      num_bodega            : 0,
      nom_empresa           : '',
      nom_bodega            : '',
      barra                 : false, // VARIABLE QUE DETERMINA SI LA BARRA LATERAL ESTA OCULTA
      menu                  : [
        {nombre:'Ingreso'       , visible: false, icono: '<i class="fas fa-fw mr-2 fa-box"></i>'        },
        {nombre:'Salida'        , visible: false, icono: '<i class="fas fa-fw mr-2 fa-truck"></i>'      },
        {nombre:'Bodega'        , visible: false, icono: '<i class="fas fa-fw mr-2 fa-warehouse"></i>'  },
        {nombre:'Informes'      , visible: false, icono: '<i class="fas fa-fw mr-2 fa-file-alt"></i>'   },
        {nombre:'Valorizacion'  , visible: false, icono: '<i class="fas fa-fw mr-2 fa-dollar-sign"></i>'   }
      ],
      modal_cambiar_clave   : false,
      modal_cerrar_sesion   : false,
      usuario               : {rut:'', dv:'', nombres:'', apellidos:'', clave_actual:'', clave_nueva1:'', clave_nueva2:'', admin:false},
      cargando_pagina       : false,
      seccion               : 'Ingreso'
    }
  },

  mounted(){
    if(this.getParameterByName('menu') != ''){
      this.cambiaVista(this.getParameterByName('menu'));
    }
    else {
      this.cambiaVista('Ingreso');
    }
    this.ocultaBarra();
    this.intervaloSesion = setInterval(this.validaSesion, 10000);
  },

  methods:{
    validaInputsClave(){
      if(this.usuario.clave_actual != '' && this.usuario.clave_nueva1 == this.usuario.clave_nueva2 && this.usuario.clave_nueva1.length>5 && this.validaClave(this.usuario.clave_nueva1)){
        return true;
      }else{
        return false;
      }
    },

    validaClave(clave){
      var regex = /^(?=.*\d)(?=.*[a-záéíóúüñ]).*[A-ZÁÉÍÓÚÜÑ]/;
      if(regex.test(clave) && clave.length > 5){
        return true;
      }else{
        return false;
      }
    },

    validaSesion(){
      axios.get(base_url+'home/estadoSesion').then( resp => {
        if(resp.status === 200){
          var mensaje = resp.data;
          if(mensaje.key != 1){
            window.location.reload();
          }
        }
      });
    },

    obtieneDatosUsuario(){
      this.cargando_pagina = true;
      axios.post(base_url+'Perfil/obtieneDatosUsuario').then( resp => {
        if(resp.status === 200){
          var mensaje = resp.data;
          if(mensaje.key == 1){
            this.cargando_pagina        = false;
            this.usuario.rut            = mensaje.usb_rut;
            this.usuario.dv             = mensaje.usb_dv;
            this.usuario.nombres        = mensaje.usb_nombres;
            this.usuario.apellidos      = mensaje.usb_apellidos;
            this.usuario.usuario        = mensaje.usb_usuario;
            this.usuario.administrador  = mensaje.usb_administrador;
            this.usuario.administrativo = mensaje.usb_administrativo;
            if(this.usuario.administrador || this.usuario.administrativo){
              this.menu = this.menu_admin;
            }
            else{
              this.menu = this.menu_personal;
            }
            setTimeout(()=>{
              this.cambiaVista(0);
            }, 1);
          }
          this.cargando_pagina        = false;
        }
        else{
          this.cargando_pagina        = false;
          this.modal_cambiar_clave    = false;
          this.muestraToast('ERROR', 'ERROR AL CARGAS DATOS NECESARIOS, INTENTELO MAS TARDE.', 'danger');
        }
      });
    },

    guardaClave(){
      this.cargando_pagina = true;
      var dato = new URLSearchParams();
      dato.append('clave_actual', sha1(md5(this.usuario.rut+this.usuario.clave_actual)));
      dato.append('clave_nueva', sha1(md5(this.usuario.rut+this.usuario.clave_nueva1)));
      axios.post(base_url+'Perfil/cambiaClave', dato).then( resp => {
        if(resp.status === 200 && 'resp' in resp.data){
          var mensaje = resp.data.resp;
          if(mensaje.key == 1){
            this.cargando_pagina      = false;
            this.modal_cambiar_clave  = false; 
            this.usuario.clave_actual = '';
            this.usuario.clave_nueva1 = ''; 
            this.usuario.clave_nueva2 = '';
            this.muestraToast('ÉXITO', 'CLAVE CAMBIADA CON ÉXITO.', 'success');
          }
          else if(mensaje.key == 2) {
            this.cargando_pagina      = false;
            this.muestraToast('ATENCIÓN', 'LA CLAVE ACTUAL INGRESADA NO ES CORRECTA.', 'danger');
          }
          else if(mensaje.key == 0) {
            this.cargando_pagina      = false;
            this.muestraToast('ERROR', 'ERROR AL REALIZAR CAMBIOS, INTENTELO MAS TARDE.', 'danger');
          }
        }
        else{
          this.cargando_pagina        = false;
          this.muestraToast('ERROR', 'ERROR AL REALIZAR CAMBIOS, INTENTELO MAS TARDE.', 'danger');
        }
      });
    },

    ocultaBarra(){
      $('#menuLateral').css('width', '55px');
      $('.item_lateral').css('width', '55px');
      $('.content-wrapper').css('margin-left', '55px');
      this.barra = false;
      console.log('ocultando menu');
    },

    muestraBarra(){
      $('#menuLateral').css('width', '250px');
      $('.item_lateral').css('width', '250px');
      $('.content-wrapper').css('margin-left', '250px');
      this.barra = true;
      console.log('mostrando menu');
      
    },

    cambiaVista(vista){
      history.pushState(null, "", "?menu="+vista);
      for(var i=0; i<this.menu.length; i++){
        if(this.menu[i].nombre == vista)  { 
          this.menu[i].visible = true;   
        }
        else { 
          this.menu[i].visible = false;
        }
      }
      
      $('.item_lateral').removeClass('active');
      $('#item_'+vista).addClass('active');  
      this.ocultaMenu();
    },

    ocultaMenu(){
      $('#navbarResponsive').hide(100); 
    },

    muestraBarraSuperior(){
      $('#navbarResponsive').toggle(100);
    },

    setEmpresa(){
      axios.post(base_url+'sucursal/setempresa').then( resp => {
        this.num_empresa  = resp.data.dts_empresa;
        this.num_bodega   = resp.data.dts_bodega;
        this.nom_empresa  = resp.data.emp_nombre;
        this.nom_bodega   = resp.data.bod_nombre;
      });
    },

    getParameterByName(name) {
      name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
      var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
      results = regex.exec(location.search);
      return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }

  }

}
</script>

<style>
html {
  background-color: #343a40;
}

.v-select .vs__dropdown-menu {
  max-height: 200px;
}

.v-select .vs__dropdown-toggle{
  border: 0!important;
}

.content-wrapper {
  margin-left: 55px; 
  margin-bottom: 55px; 
  padding-top: 55px!important;
}

</style>

<style scoped>
@media (max-width: 991px) {
  .content-wrapper{
    margin-left: auto!important;
  }

  .texto_barra{
    display: inline-block !important;
  }

  #menuLateral{
    width:250px !important;
  }

  .item_lateral{
      width:250px !important;
  }
}

/*.modal
{
  background: black !important;
}*/
</style>