<template>
  <div class="pagina">
  <loader-component :cargando_pagina="cargando_pagina"/>
  <div class="container-fluid p-1 pagina2">
    
    <div class="row">
      <div class="col-12 text-md-left text-center">
        <h3><i class="fas fa-fw fa-warehouse mr-2"></i>ESTADO DE BODEGA</h3>
      </div>
    </div>
    <div class="row">
      <div class="col-12 text-md-left text-center">
        <b-card style="display:flex;">
          <h6><strong>CAPACIDADES:</strong></h6>
          <div style="float:left;" class="mr-3">DISPONIBLES:</div>
          <div style="float:left;"><b-card no-body class="text-center alert-primary" style="width:100px; border-radius:0!important;">2.000 Kg</b-card></div>
          <div style="float:left;" class="ml-3"><b-card no-body class="text-center alert-warning" style="width:100px; border-radius:0!important;">1.200 Kg</b-card></div>
          <div style="float:left;" class="ml-3"><b-card no-body class="text-center alert-info" style="width:100px; border-radius:0!important;">200 Kg</b-card></div>
          <div style="float:left;" class="ml-5 mr-3">OCUPADOS:</div>
          <div style="float:left;"><b-card no-body class="text-center alert-primary text-danger border-danger espacio_ocupado" style="width:100px; border-radius:0!important;">2.000 Kg</b-card></div>
          <div style="float:left;" class="ml-3"><b-card no-body class="text-center alert-warning text-danger border-danger espacio_ocupado" style="width:100px; border-radius:0!important;">1.200 Kg</b-card></div>
          <div style="float:left;" class="ml-3"><b-card no-body class="text-center alert-info text-danger border-danger espacio_ocupado" style="width:100px; border-radius:0!important;">200 Kg</b-card></div>
        </b-card>
      </div>
    </div>


    <div class="row justify-content-between mt-2 pt-2 pb-2 bg-secondary">
      <div v-for="(estante,index1) in estantes" :key="'est'+index1" :class="{
        'col-12'      : estante.est_capacidad == 21, 
        'col-12 pr-5' : estante.est_id == 17 || estante.est_id == 19 || estante.est_id > 19, 
        'col-6'       : estante.est_capacidad < 10,
        /* 'col-7'       : estante.est_id == 15 */
        }">
        <b-card no-body class="p-0 text-center" header-class="p-1" :class="{
          'w-25'    : estante.est_capacidad == 3, 
          'ml-auto' : estante.est_id == 7  || estante.est_id == 4, 
          'w-50'    : estante.est_id == 17 || estante.est_id == 19 || estante.est_id > 19
          }">
          <template #header>
            <strong><span v-if="estante.est_id < 20">ESTANTE</span><span v-else>ESPACIO EN SUELO</span> {{estante.est_id}}</strong>
          </template>
          <div class="container-fluid">
            <div class="row p-1 justify-content-betwen">
              <div class="col p-0" v-for="(seccion,index2) in estante.secciones" :key="'sec'+index2">
                <b-card no-body class="p-1">
                  Sec. {{seccion.sec_id}}
                  <b-card v-for="(espacio,index3) in seccion.espacios" @click="modal_detalle=true; espacio_seleccionado=espacio; listarArticulosEspacio(espacio.esp_id); listarHistorialEspacio(espacio.esp_id);" no-body :key="'esp'+index3" class="p-0 espacio" 
                  :class="{
                    'alert-dark'    : espacio.esp_peso == 10000, 
                    'alert-primary' : espacio.esp_peso == 2000, 
                    'alert-warning' : espacio.esp_peso == 1200, 
                    'alert-info'    : espacio.esp_peso == 200, 
                    'text-danger border-danger espacio_ocupado' : espacio.esp_ocupado
                    }">
                    <strong v-if="espacio.esp_ocupado" class="">{{ estante.est_id }}-{{seccion.sec_id}}-{{espacio.esp_numero}}</strong>
                    <span v-else>{{ estante.est_id }}-{{seccion.sec_id}}-{{espacio.esp_numero}}</span>
                    <!-- {{ espacio.esp_ancho/100+'x'+espacio.esp_alto/100+'x'+espacio.esp_fondo/100}} mt <br>
                    {{ espacio.esp_peso }} kg -->

                  </b-card>
                </b-card>
              </div>
            </div>
          </div>
        </b-card>
        
      </div>
    </div>

    <b-modal v-model="modal_detalle" size="xl" title="DETALLES DE RACK" hide-footer no-close-on-backdrop :scrollable="false">
      <!-- <div class="row">
        <b-table striped hover :items="articulos_espacio" :fields="columnas_articulos" :thead-tr-class="'text-center align-middle bg-dark text-white'">
        </b-table>
      </div> -->
      <h5><strong>Información:</strong></h5>
      <div class="card alert-warning p-2">
        <div v-if="espacio_seleccionado" class="container-fluid">
          <div class="row text-center pb-1">
            <div class="col font-weight-bold"><span class="badge badge-primary badge-pill">Estante: {{ espacio_seleccionado.esp_estante }}</span></div>
            <div class="col font-weight-bold"><span class="badge badge-primary badge-pill">Sección: {{ espacio_seleccionado.esp_seccion }}</span></div>
            <div class="col font-weight-bold border-right"><span class="badge badge-primary badge-pill">Numero:  {{ espacio_seleccionado.esp_numero  }}</span></div>
          <!-- </div>
          <div class="row"> -->
            <div class="col font-weight-bold"><span class="badge badge-warning badge-pill">Ancho:    {{ espacio_seleccionado.esp_ancho }} cm</span></div>
            <div class="col font-weight-bold"><span class="badge badge-warning badge-pill">Alto:     {{ espacio_seleccionado.esp_alto  }} cm</span></div>
            <div class="col font-weight-bold"><span class="badge badge-warning badge-pill">Fondo:    {{ espacio_seleccionado.esp_fondo }} cm</span></div>
            <div class="col font-weight-bold"><span class="badge badge-warning badge-pill">Peso max: {{ espacio_seleccionado.esp_peso  }} kg</span></div>
          </div>
        </div>
      </div>
      <h5 class="mt-4"><strong>Ocupación actual:</strong></h5>
      <div class="w-100 text-center"><b-spinner v-if="cargando_articulos_espacio" style="width: 3rem; height: 3rem;" variant="primary" label="Cargando"></b-spinner></div>
      <b-alert v-if="articulos_espacio.length == 0 && !cargando_articulos_espacio" variant="success" show>Sin articulos.</b-alert>
      <div v-if="!cargando_articulos_espacio" class="card alert-info p-2" v-for="articulo in articulos_espacio" :key="articulo.ari_id">
        <div class="container-fluid">
          <div class="row" style="cursor:pointer;" v-b-toggle="'collapse-'+articulo.ari_id">
            <div class="col-10 font-weight-bold">{{ '['+articulo.ari_codigo+'] '+articulo.ari_nombre }}</div>
            <div class="col-2 text-right"><span class="badge badge-primary badge-pill">{{ articulo.ari_cantidad+' '+articulo.ari_unidad_medida }}</span></div>
            <!-- <b-button v-b-toggle="'collapse-'+articulo.ari_id" size="sm">Toggle Inner Collapse</b-button> -->
          </div>
          <b-collapse :id="'collapse-'+articulo.ari_id">
            <div  class="row mt-2 pt-2 border-top">
              <div class="col-4">Numero Ingreso: {{ articulo.ing_id }}</div>
              <div class="col-4">Orden de compra: {{ articulo.ing_orden_compra }}</div>
              <div class="col-4" v-if="articulo.ing_documento">Doc. asociado: <span class="text-lowercase" v-show="articulo.ing_tipo_documento==tipo.value" v-for="tipo in tipos_documentos" :key="'tip'+tipo.value">{{ tipo.text }}</span> {{ articulo.ing_documento }}</div>
              <!-- <div class="col-4">Doc. asociado: {{ articulo.ari_documento }}</div> -->
              <div class="col-4">Fecha Ingreso: {{ articulo.ing_fecha_ingreso.split(' ')[0].split('-')[2]+'-'+articulo.ing_fecha_ingreso.split(' ')[0].split('-')[1]+'-'+articulo.ing_fecha_ingreso.split(' ')[0].split('-')[0]+' '+articulo.ing_fecha_ingreso.split(' ')[1].split(':')[0]+':'+articulo.ing_fecha_ingreso.split(' ')[1].split(':')[1] }}</div>
              <div class="col-4">Destino: Bodega {{ articulo.ari_sucursal_destino }}</div>
              <div class="col-4">Despacho estimado: {{ articulo.ing_despacho_estimado.split(' ')[0].split('-')[2]+'-'+articulo.ing_despacho_estimado.split(' ')[0].split('-')[1]+'-'+articulo.ing_despacho_estimado.split(' ')[0].split('-')[0]  }}</div>
              <div class="col-12">Observacion: {{ articulo.ing_observacion }}</div>
              
            </div>
          </b-collapse>
        </div>
      </div>
      
      <h5 class="mt-4"><strong>Historial:</strong></h5>
      <div class="w-100 text-center"><b-spinner v-if="cargando_historial_espacio" style="width: 3rem; height: 3rem;" variant="primary" label="Cargando"></b-spinner></div>
      <b-alert v-if="historial_espacio.length == 0 && !cargando_historial_espacio" variant="dark" show>Sin articulos.</b-alert>
      <div v-if="!cargando_historial_espacio" class="card alert-dark p-2" v-for="articulo in historial_espacio" :key="articulo.ari_id">
        <div class="container-fluid">
          <div class="row border-bottom pb-2 mb-2">
            <div class="col-10 font-weight-bold">{{ '['+articulo.ari_codigo+'] '+articulo.ari_nombre }}</div>
            <div class="col-2 text-right"><span class="badge badge-primary badge-pill">{{ articulo.ars_cantidad+' '+articulo.ari_unidad_medida }}</span></div>
          </div>
          <div class="row">
            <div class="col-4">Orden de compra: {{ articulo.ing_orden_compra }}</div>
            <div class="col-4">Doc. asociado: <span class="text-lowercase" v-show="articulo.ing_tipo_documento==tipo.value" v-for="tipo in tipos_documentos" :key="'tip'+tipo.value">{{ tipo.text }}</span> {{ articulo.ing_documento }}</div>
            <!-- <div class="col-4">Doc. asociado: {{ articulo.ari_documento }}</div> -->
            <div class="col-4">Destino: Bodega {{ articulo.ari_sucursal_destino }}</div>
            <div class="col-4">Ingreso: {{ articulo.ing_fecha_ingreso.split(' ')[0].split('-')[2]+'-'+articulo.ing_fecha_ingreso.split(' ')[0].split('-')[1]+'-'+articulo.ing_fecha_ingreso.split(' ')[0].split('-')[0]+' '+articulo.ing_fecha_ingreso.split(' ')[1].split(':')[0]+':'+articulo.ing_fecha_ingreso.split(' ')[1].split(':')[1] }}</div>
           <!--  <div class="col-4">Despacho estimado: {{ articulo.ing_despacho_estimado.split(' ')[0].split('-')[2]+'-'+articulo.ing_despacho_estimado.split(' ')[0].split('-')[1]+'-'+articulo.ing_despacho_estimado.split(' ')[0].split('-')[0]  }}</div> -->
            <div class="col-4">Salida: {{ articulo.sld_fecha_salida.split(' ')[0].split('-')[2]+'-'+articulo.sld_fecha_salida.split(' ')[0].split('-')[1]+'-'+articulo.sld_fecha_salida.split(' ')[0].split('-')[0]+' '+articulo.sld_fecha_salida.split(' ')[1].split(':')[0]+':'+articulo.sld_fecha_salida.split(' ')[1].split(':')[1] }}</div>
          </div>
        </div>
      </div>



      <div class="row mt-3 pt-3 border-top">
        <div class="col text-center">
          <b-button @click="modal_detalle=false;" variant="primary" size="sm">CERRAR</b-button>
        </div>
      </div>

    </b-modal>

  </div>
  </div>
</template>

<script>
export default {
  data(){
    return{
      meses : [
        {value:1, text:'ENERO'},
        {value:2, text:'FEBRERO'},
        {value:3, text:'MARZO'},
        {value:4, text:'ABRIL'},
        {value:5, text:'MAYO'},
        {value:6, text:'JUNIO'},
        {value:7, text:'JULIO'},
        {value:8, text:'AGOSTO'},
        {value:9, text:'SEPTIEMBRE'},
        {value:10, text:'OCTUBRE'},
        {value:11, text:'NOVIEMBRE'},
        {value:12, text:'DICIEMBRE'}
      ],
      modal_detalle               : false,
      cargando_pagina             : false,
      cargando_articulos_espacio  : false,
      cargando_historial_espacio  : false,
      estantes                    : [],
      articulos_espacio           : [],
      historial_espacio           : [],
      espacio_seleccionado        : null,
      columnas_articulos          : [
        {key: 'art_codigo'            , label: 'SKU'                , sortable: true  , tdClass:'p-1', thClass:'p-1'},
        {key: 'art_nombre'            , label: 'Nombre'             , sortable: true  , tdClass:'p-1', thClass:'p-1'},
        {key: 'art_cantidad'          , label: 'Cantidad'           , sortable: true  , tdClass:'p-1', thClass:'p-1'},
        {key: 'art_unidad_medida'     , label: 'U. Medida'          , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'art_orden_compra'      , label: 'OC'                 , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'art_sucursal'          , label: 'Sucursal destino'   , sortable: true  , tdClass:'p-1', thClass:'p-1'},
        {key: 'art_documento'         , label: 'Documento'          , sortable: true  , tdClass:'p-1', thClass:'p-1'},
        {key: 'art_tipo_documento'    , label: 'Tipo Doc.'          , sortable: true  , tdClass:'p-1', thClass:'p-1'},
        {key: 'art_fecha_ingreso'     , label: 'Fecha ingreso'      , sortable: true  , tdClass:'p-1', thClass:'p-1'},
        {key: 'art_despacho_estimado' , label: 'Despacho estimado'  , sortable: true  , tdClass:'p-1', thClass:'p-1'}
      ],
      tipos_documentos  : [
        {value:33, text:'FACTURA'}, 
        {value:39, text:'BOLETA'}, 
        {value:52, text:'GUIA DESPACHO'}
      ],
    }
  },
  mounted(){
    this.listarEstantes();
  },
  methods:{
    listarEstantes(){
      this.cargando_pagina = true;
      axios.post(base_url+'bodega/listarEstantes').then( resp => {
        this.estantes = resp.data.estantes;
        this.cargando_pagina  = false;
        console.log(this.estantes);
      }).catch(error =>{
        console.log(error);
        this.cargando_pagina = false;
      });
    },

    listarArticulosEspacio(id_espacio){
      this.cargando_articulos_espacio = true;
      const datos = new URLSearchParams();
      datos.append('id_espacio', id_espacio);
      axios.post(base_url+'bodega/listarArticulosEspacio', datos).then( resp => {
        this.articulos_espacio          = resp.data;
        this.cargando_articulos_espacio = false;
      }).catch(error =>{
        console.log(error);
        this.cargando_articulos_espacio = false;
      });
    },

    listarHistorialEspacio(id_espacio){
      this.cargando_historial_espacio = true;
      const datos = new URLSearchParams();
      datos.append('id_espacio', id_espacio);
      axios.post(base_url+'bodega/listarHistorialEspacio', datos).then( resp => {
        this.historial_espacio          = resp.data;
        this.cargando_historial_espacio = false;
      }).catch(error =>{
        console.log(error);
        this.cargando_historial_espacio = false;
      });
    }
  }
}

</script>

<style scoped>
.espacio {
  cursor:pointer;
  border-radius:0!important;
}
.espacio:hover {
  color: #1b1e21;
  background-color: #d6d8d9;
  border-color: #c6c8ca;
}

.espacio_ocupado {
  /* opacity: 0.5; */
  border-width: 2px !important;
  border-color: #dc3545 !important;
}

.pagina {
  width: 100% !important;
  height: calc(100vh - 136px) !important;
  overflow: auto !important;
}

.pagina2 {
  min-width: 1550px !important;
}

</style>
