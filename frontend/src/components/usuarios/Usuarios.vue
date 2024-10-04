<template>
  <div class="container-fluid">
    <loader-component :cargando_pagina="cargando_pagina"/>
    <div class="row">
      <div class="col-12 text-md-left text-center">
        <h3><i class="fas fa-fw fa-users mr-2"></i>USUARIOS</h3>
      </div>
    </div>


    <div class="row justify-content-center mt-4">
      <div class="col-12">
        <b-card no-body class="p-0">
          <template #header>
            <div class="row">
              <div class="col-6 text-md-left text-center">
                <b-button @click="modal_nuevo = true;" variant="success" block><i class="fas fa-fw fa-user mr-2"></i>NUEVO USUARIO</b-button>
              </div>
              <div class="col-6 text-md-left text-center">
                <b-input-group prepend="FILTRO">
                  <b-form-input v-model="filtro" placeholder="BUSCAR"></b-form-input>
                </b-input-group>
              </div>
            </div>
          </template>
          <b-table show-empty class="mb-0" :tbody-class="'text-center'" :thead-tr-class="'text-center align-middle'" :thead-class="'text-center align-middle'" 
          :head-variant="'light'" @filtered="onFiltered" :items="usuarios" :fields="columnas" 
          :filter="filtro" bordered hover responsive empty-text="SIN DATOS PARA LISTAR." empty-filtered-text="SIN DATOS PARA LISTAR.">
            <template v-slot:table-busy>
              <div class="text-center text-dark my-2">
                <b-spinner class="align-middle"></b-spinner>
                <strong>&nbsp;CARGANDO...</strong>
              </div>
            </template>
            <template v-slot:cell(usr_rut)="data">
              {{  formateaRut(data.value+'-'+data.item.usr_dv) }}
            </template>
            <template v-slot:cell(opciones)="data">
              <b-button variant="info"><i class="fas fa-fw fa-eye"></i> </b-button>
            </template>
          </b-table>
        </b-card>
      </div>
    </div>

    <b-modal v-model="modal_nuevo" size="lg" title="NUEVO USUARIO" hide-footer no-close-on-backdrop :scrollable="false">
      RUT
      <!-- <b-form-input v-model="nuevo_usr.rut" class="mb-3"></b-form-input> -->
      <input maxlength="12" v-model="nuevo_usr.rut" type="text" class="form-control mb-3" @input="nuevo_usr.rut = formateaRut(nuevo_usr.rut)" 
      :class="{'is-valid': verificaRut(nuevo_usr.rut), 'is-invalid': !verificaRut(nuevo_usr.rut) && nuevo_usr.rut != ''}">
      NOMBRES
      <b-form-input v-model="nuevo_usr.nombres" class="mb-3"></b-form-input>
      APELLIDO PATERNO
      <b-form-input v-model="nuevo_usr.apellido_p" class="mb-3"></b-form-input>
      APELLIDO MATERNO
      <b-form-input v-model="nuevo_usr.apellido_m" class="mb-3"></b-form-input>

      <div class="row mt-4 pt-3 border-top">
        <div class="col text-center">
          <b-button :disabled="
            !verificaRut(nuevo_usr.rut) || 
            nuevo_usr.nombres     == '' || 
            nuevo_usr.apellido_p  == '' || 
            nuevo_usr.apellido_p  == ''" @click="registrarUsuario();" variant="primary">REGISTRAR</b-button>
        </div>
      </div>

    </b-modal>

  </div>
</template>

<script>
export default {
  data(){
    return{
      columnas: [
        { key: 'usr_rut'          , label: 'RUT'          , sortable: true,   tdClass:'p-1', thClass:'p-1' },
        { key: 'usr_nombres'      , label: 'NOMBRE'       , sortable: true,   tdClass:'p-1', thClass:'p-1' },
        { key: 'usr_apellido_p'   , label: 'APELLIDO P.'  , sortable: true,   tdClass:'p-1', thClass:'p-1' },
        { key: 'usr_apellido_m'   , label: 'APELLIDO M.'  , sortable: true,   tdClass:'p-1', thClass:'p-1' }
      ],
      usuarios        : [],
      filtro          : '',
      modal_nuevo     : false,
      cargando_pagina : false,
      nuevo_usr       : {rut:'', nombres:'', apellido_p:'', apellido_m:''}
    }
  },
  mounted(){
    this.listarUsuarios();
  },
  methods:{
    listarUsuarios(){
      this.cargando_pagina = true;
      axios.post(base_url+'usuarios/listarUsuarios').then( resp => {
        this.usuarios         = resp.data.usuarios;
        this.cargando_pagina  = false;
      }).catch(error =>{
        console.log(error);
        this.cargando_pagina = false;
      });
    },

    registrarUsuario(){
      this.cargando_pagina = true;
      var usuario = JSON.parse(JSON.stringify(this.nuevo_usr));
      usuario.dv  = usuario.rut.substring(usuario.rut.length - 1, usuario.rut.length);
      usuario.rut = usuario.rut.replace(/[.|-]/g, '').slice(0, -1);
      var data    = new URLSearchParams();
      data.append('usuario', JSON.stringify(usuario));
      axios.post(base_url+'usuarios/registrarUsuario', data).then( resp => {
        this.cargando_pagina  = false;
        if(resp.data.key == 1){
          this.modal_nuevo = false;
          this.nuevo_usr.rut = this.nuevo_usr.nombres = this.nuevo_usr.apellido_p = this.nuevo_usr.apellido_m = '';
          this.listarUsuarios();
        }
        else {
          alert('ERROR AL REGISTRAR USUARIO.')
        }
      }).catch(error =>{
        console.log(error);
        this.cargando_pagina = false;
      });
    },

    formateaRut(rut){
      const newRut = rut.replace(/\./g,'').replace(/[^k|K|\d]/g, '').replace(/\-/g, '').trim().toUpperCase();
      const lastDigit = newRut.substr(-1, 1);
      const rutDigit = newRut.substr(0, newRut.length-1)
      let format = '';
      var i = 0;
      var j = 1;
      for (i = rutDigit.length - 1; i >= 0; i--) {
        const e = rutDigit.charAt(i);
        format = e.concat(format);
        if (j % 3 == 0 && j <= rutDigit.length - 1) {
          format = '.'.concat(format);
        }
        j++;
      }
      var formateado = "";
      if(format.length > 0){
        formateado = format.concat('-').concat(lastDigit);
      }
      else{
        formateado = lastDigit;
      }
      return formateado;
      //return format.concat('-').concat(lastDigit);
    },

    verificaRut(rut){
      let resp=false;
      if(rut != null && rut.toString().length >= 11){
        let dv = rut.substring(rut.length - 1, rut.length);
        rut = rut.replace(/[.|-]/g, '').slice(0, -1);
        if(rut.indexOf('K') == -1){
        //DESCOMENTAR EL IF Y LA LLAVE PARA VALIDAR CONTRA RUT 'ESPECIALES'
        //if(!rut.match(/(KK|0000000|1111111|2222222|3333333|4444444|5555555|6666666|7777777|8888888|9999999)/g)){
            let m=0,s=1;
            for(;rut;rut=Math.floor(rut/10))
              s=(s+rut%10*(9-m++%6))%11;
            let dv_calc= (s?s-1:'K').toString();
            if(dv_calc===dv) resp=true;
        // }
        }
      }
      return resp;
    },
  }
}

</script>

<style scoped>

</style>
