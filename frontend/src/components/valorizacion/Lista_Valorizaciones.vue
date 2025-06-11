<template>
  <div class="container-fluid p-0">
    <loader-component :cargando_pagina="cargando_pagina"/>

    <b-card class="mt-4">
      <div class="row">
        <div class="col">
          <h5 class="mb-3">Valorizaciones registradas</h5>
        </div>
        <div class="col">
          <b-form-input v-model="busqueda" class="mb-3" size="sm" placeholder="Buscar valorización"></b-form-input>
        </div>
      </div>
      
      <div class="row justify-content-center">
        <b-table striped hover :items="valorizaciones" :fields="columnas_valorizaciones" :filter="busqueda" class="rounded" :thead-tr-class="'text-center align-middle bg-dark text-white'">
          <template v-slot:cell(val_fecha_hora)="data">
            {{  data.value.split(' ')[0].split('-')[2]+'/'+data.value.split(' ')[0].split('-')[1]+'/'+data.value.split(' ')[0].split('-')[0]+' '+
                data.value.split(' ')[1].split(':')[0]+':'+data.value.split(' ')[1].split(':')[1] 
            }}
          </template>
          <template v-slot:cell(val_finalizada)="data">
            <div class="w-100 text-center">
              <b-badge variant="success" v-if="data.value == 1">Finalizada</b-badge>
              <b-badge variant="danger" v-else>Pendiente</b-badge>
            </div>
          </template>
          <template v-slot:cell(opcion)="data">
            <div class="w-100 text-center">
              <b-button size="sm" variant="primary" @click="verValorizacion(data.item.val_id);"><i class="fas fa-eye"></i></b-button>
            </div>
          </template>
        </b-table>
      </div>
    </b-card>

  </div>
</template>

<script>
export default {
  data(){
    return{
      cargando_pagina   : false,
      valorizaciones    : [],
      busqueda          : '',

      columnas_valorizaciones    : [
        {key: 'val_id'            , label: 'ID'           , sortable: false  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'val_oc'            , label: 'OC'           , sortable: false  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'val_factura'       , label: 'Factura'      , sortable: false  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'val_din'           , label: 'DIN'          , sortable: false  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'val_rut_carpeta'   , label: 'Rut carpeta'  , sortable: false  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'val_fecha_hora'    , label: 'Fecha'        , sortable: false  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'val_finalizada'    , label: 'Estado'       , sortable: false  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'opcion'            , label: '-'            , sortable: false  , tdClass:'p-1 text-center', thClass:'p-1'}
      ],

    }
  },
  mounted(){
    this.listarValorizaciones();
  },
  methods:{
    listarValorizaciones(){
      this.cargando_pagina = true;
      axios.get(base_url+'valorizacion/listarValorizaciones').then( resp => {
        if(resp.data.key == 1){
          this.valorizaciones = resp.data.valorizaciones;
        }
        else {
          alert('Falla al listar valorizaciones.');
        }
        this.cargando_pagina      = false;
      }).catch(error =>{
        alert('Error: '+error);
        this.cargando_pagina = false;
      });
    },

    verValorizacion(val_id){
      this.$emit('ver_val', val_id);
    },
  }
}

</script>

<style >
.espacio {
  cursor: pointer;
}

.espacio:hover {
  color: #155724;
  background-color: #d4edda;
  border-color: #c3e6cb;
}

.espacio:active{
  background-color: #20c997;
}

/* .table-scroll {
  position: relative;
  height: 400px;
  overflow: auto;
} */

.div-scroll {
  position: relative;
  overflow: auto;
  max-height: 400px;
}

.vs__dropdown-toggle {
  padding: 0 0 1px!important;
}

.disabled-option {
  color: red !important; /* O el color que prefieras para las opciones deshabilitadas */
  pointer-events: none !important; /* Evita que se pueda hacer clic en la opción */
}
</style>

<!-- 1178904 -->