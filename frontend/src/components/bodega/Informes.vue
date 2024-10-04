<template>
  <div class="container-fluid">
    <loader-component :cargando_pagina="cargando_pagina"/>
    <div class="row">
      <div class="col-12 text-md-left text-center">
        <h3><i class="fas fa-fw fa-file-alt mr-2"></i>INFORMES DE BODEGA</h3>
      </div>
    </div>


    <div class="row px-2">
      <div class="card w-100">
        <div class="card-header">
          <div class="row justify-content-center mt-4">
            <div class="col-6 text-rigth">
              <b-form-select v-model="informe_seleccionado" class="mb-3" :options="informes" size="sm"></b-form-select>
            </div>
            <div class="col-3 text-left">
              <b-button :disabled="!validaFiltros()" @click="listarInforme();" variant="primary" size="sm">BUSCAR</b-button>
            </div>
          </div>
          <div v-if="informe_seleccionado == 2 || informe_seleccionado == 3" class="row">
            <div class="col-6">
              <b-form-datepicker size="sm" v-model="filtro_fecha_desde" class="mb-3" placeholder="Fecha desde" label-help="Selecciona un día." label-no-date-selected="-" label-next-month="SIGUIENTE MES" label-prev-month="MES ANTERIOR" label-prev-year="AÑO ANTERIOR" label-next-year="SIGUIENTE AÑO" label-current-month="MES ACTUAL" start-weekday="1"></b-form-datepicker>
            </div>
            <div class="col-6">
              <b-form-datepicker size="sm" v-model="filtro_fecha_hasta" class="mb-3" placeholder="Fecha hasta" label-help="Selecciona un día." label-no-date-selected="-" label-next-month="SIGUIENTE MES" label-prev-month="MES ANTERIOR" label-prev-year="AÑO ANTERIOR" label-next-year="SIGUIENTE AÑO" label-current-month="MES ACTUAL" start-weekday="1"></b-form-datepicker>
            </div>
          </div>
        </div>
        <div class="card-content">
          <b-table v-if="articulos_bodega.length > 0 && informe_seleccionado < 3" striped hover :items="articulos_bodega" :fields="columnas_articulos" :thead-tr-class="'text-center align-middle bg-dark text-white'">
            <template v-slot:cell(ari_cantidad)="data">
              <span>{{ data.value+' '+data.item.ari_unidad_medida.toLowerCase()}}</span>
            </template>
            <template v-slot:cell(ing_rack)="data">
              <span>{{ data.item.esp_seccion+'-'+data.item.esp_estante+'-'+data.item.esp_numero }}</span>
            </template>
          </b-table>

          <b-table v-if="articulos_bodega.length > 0 && informe_seleccionado == 3" striped hover :items="articulos_bodega" :fields="columnas_articulos_salida" :thead-tr-class="'text-center align-middle bg-dark text-white'">
            <template v-slot:cell(ari_cantidad)="data">
              <span>{{ data.value+' '+data.item.ari_unidad_medida.toLowerCase()}}</span>
            </template>
            <template v-slot:cell(ing_rack)="data">
              <span>{{ data.item.esp_seccion+'-'+data.item.esp_estante+'-'+data.item.esp_numero }}</span>
            </template>
          </b-table>

          <b-alert v-if="informe_seleccionado == ''" show variant="info" class="text-center m-3">Sin informe seleccionado.</b-alert>
          <b-alert v-if="articulos_bodega.length == 0" show variant="warning" class="text-center m-3">Sin datos para listar.</b-alert>
        </div>
      </div>
      
    </div>


  </div>
</template>

<script>
export default {
  data(){
    return{
      cargando_pagina       : false,
      informe_seleccionado  : '',
      filtro_fecha_desde    : '',
      filtro_fecha_hasta    : '',
      informes              : [
        {value:'', text:'Selecciona un informe'}, 
        {value:1, text:'Articulos en bodega'}, 
        {value:2, text:'Ingresos por fecha'   },
        {value:3, text:'Salidas por fecha'    }
      ],

      columnas_articulos    : [
        {key: 'ari_codigo'            , label: 'SKU'                , sortable: true  , tdClass:'p-1', thClass:'p-1'},
        {key: 'ari_nombre'            , label: 'Articulo'           , sortable: true  , tdClass:'p-1', thClass:'p-1'},
        {key: 'ari_cantidad'          , label: 'Cantidad'           , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'ing_fecha_ingreso'     , label: 'Fecha Ingreso'      , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'ing_despacho_estimado' , label: 'Despacho Estimado'  , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'ari_sucursal_destino'  , label: 'Sucursal Dest.'     , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'ing_rack'              , label: 'Estante'            , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'ing_id'                , label: 'N° Ingreso'         , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'ing_orden_compra'      , label: 'OC'                 , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'}
      ],

      columnas_articulos_salida    : [
        {key: 'ari_codigo'            , label: 'SKU'                , sortable: true  , tdClass:'p-1', thClass:'p-1'},
        {key: 'ari_nombre'            , label: 'Articulo'           , sortable: true  , tdClass:'p-1', thClass:'p-1'},
        {key: 'ars_cantidad'          , label: 'Cantidad'           , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'ing_fecha_ingreso'     , label: 'Fecha Ingreso'      , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'sld_fecha_salida'      , label: 'Fecha Salida'       , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'sld_bodega_destino'    , label: 'Sucursal Dest.'     , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'ing_rack'              , label: 'Estante'            , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'ing_orden_compra'      , label: 'OC'                 , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'ing_id'                , label: 'N° Ingreso'         , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'sld_id'                , label: 'N° Salida'          , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'sld_orden_traspaso'    , label: 'N° Ord. Traspaso'   , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
      ],

      articulos_bodega  : []
    }
  },
  mounted(){
    this.fecha_actual = new Date();
  },
  methods:{
    listarInforme(){
      this.cargando_pagina = true;
      const datos = new URLSearchParams();
      datos.append('tipo_informe', this.informe_seleccionado);
      if(this.informe_seleccionado == 2 || this.informe_seleccionado == 3){
        datos.append('fecha_desde', this.filtro_fecha_desde);
        datos.append('fecha_hasta', this.filtro_fecha_hasta);
      }
      axios.post(base_url+'bodega/listarInforme', datos).then( resp => {
        this.articulos_bodega = resp.data;
        console.log(resp.data);
        this.cargando_pagina = false;
      }).catch(error =>{
        console.log(error);
        this.cargando_pagina = false;
      });
    },

    validaFiltros(){
      if(this.informe_seleccionado == '' || ( (this.informe_seleccionado == 2 || this.informe_seleccionado == 3) && (this.filtro_fecha_desde == '' || this.filtro_fecha_hasta == ''))){
        return false;
      }
      else {
        return true;
      }
    }
  }
}

</script>

<style >

</style>
