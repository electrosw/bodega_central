<template>
  <div class="container-fluid">
    <loader-component :cargando_pagina="cargando_pagina"/>
    <div class="row">
      <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 text-md-left text-center">
        <h3><i class="fas fa-fw fa-truck mr-2"></i>SALIDA DE BODEGA</h3>
      </div>
      <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mt-sm-2 mt-md-0 text-md-right text-center">
        <b-button @click="modal_historial=true;" variant="success" size="sm"><i class="fas fa-fw fa-history mr-2"></i>HISTORIAL DE SALIDAS</b-button>
      </div>
    </div>

    <!-- <div class="row justify-content-center mt-4">
      <div class="col-xl-6 col-md-10 col-12">
        Filtrar por bodega:
        <b-form-select @change="listarOrdenesTraspaso();" v-model="filtro_bodega" class="mb-3" :options="sucursales" :value-field="'suc_id'" :text-field="'suc_nombre'">
          <b-form-select-option value="''">Todas</b-form-select-option>
        </b-form-select>
      </div>
    </div>
    <div class="row justify-content-center mt-4 px-3">
      <div class="col-xl-8 col-md-12 col-12 border p-0">
        <table class="table table-striped text-center table-scroll mb-0">
          <thead>
            <tr>
              <th scope="col">INGRESO</th>
              <th scope="col">T.DOC</th>
              <th scope="col">DOC</th>
              <th scope="col">PESO</th>
              <th scope="col">DESP. ESTIMADO</th>
              <th scope="col" class="alert-dark">ESTANTE</th>
              <th scope="col" class="alert-dark">SECCION</th>
              <th scope="col" class="alert-dark">NUMERO</th>
              <th scope="col" class="alert-dark">-</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(art, index) in articulos" :key="'esp'+index" :class="{'art-seleccionado': art.seleccionado}">
              <td class="p-1">{{ 
                art.art_fecha_ingreso.split(' ')[0].split('-')[2]+'-'+
                art.art_fecha_ingreso.split(' ')[0].split('-')[1]+'-'+
                art.art_fecha_ingreso.split(' ')[0].split('-')[0]+' '+
                art.art_fecha_ingreso.split(' ')[1].split(':')[0]+':'+
                art.art_fecha_ingreso.split(' ')[1].split(':')[1]
                }}</td>
              <td class="p-1">{{art.art_tipo_documento}}</td>
              <td class="p-1">{{art.art_documento}}</td>
              <td class="p-1">{{art.art_peso}}</td>
              <td class="p-1" :class="{'alert-danger':fechaPasada(art.art_despacho_estimado)}">{{
                art.art_despacho_estimado.split('-')[2]+'-'+
                art.art_despacho_estimado.split('-')[1]+'-'+
                art.art_despacho_estimado.split('-')[0]
                }}</td>
              <td class="p-1 alert-dark">{{art.esp_estante}}</td>
              <td class="p-1 alert-dark">{{art.esp_seccion}}</td>
              <td class="p-1 alert-dark">{{art.esp_numero }}</td>
              <td class="p-1 alert-dark">
                <b-form-checkbox :id="'checkbox-'+index" v-model="art.seleccionado" :name="'checkbox-'+index" :value="true" :unchecked-value="false"></b-form-checkbox>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div> -->

    <div class="row px-2">
      <div class="card w-100">
        <div class="card-header">
          <h5>Solicitudes de traspaso</h5>
          <div class="row justify-content-center mt-2">
            <div class="col-6 text-rigth">
              Filtrar por bodega:
              <b-form-select @change="listarOrdenesTraspaso();" v-model="filtro_bodega" size="sm" class="mb-3" :options="sucursales" :value-field="'suc_id'" :text-field="'suc_nombre'">
                <b-form-select-option :value="''" selected>TODAS</b-form-select-option>
              </b-form-select>
            </div>
            <div class="col-6">
              Busqueda:
              <div class="input-group input-group-sm">
                <input v-model="filtro_busqueda" type="text" class="form-control form-control-sm h-auto" placeholder="BÚSQUEDA">
                <div class="input-group-append">
                  <button :disabled="filtro_busqueda==''" @click="filtro_busqueda=''" class="btn btn-sm btn-secondary">
                    <i v-if="filtro_busqueda==''" class="fa-fw fas fa-search"></i>
                    <i v-else class="fa-fw fas fa-times"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card-content">
          <b-table v-if="ordenes_traspaso.length > 0" striped hover :items="ordenes_traspaso" :fields="columnas_ordenes" :filter="filtro_busqueda" :thead-tr-class="'text-center align-middle bg-dark text-white'">
            <!-- <template v-slot:cell(art_cantidad)="data">
              <span>{{ data.value+' '+data.item.art_unidad_medida.toLowerCase()}}</span>
            </template> -->
            <template v-slot:cell(ing_rack)="data">
              <span>{{ data.item.esp_seccion+'-'+data.item.esp_estante+'-'+data.item.esp_numero }}</span>
            </template>
            <template v-slot:cell(opcion)="data">
              <b-button size="sm" variant="primary" @click="listarOrdenTraspaso(data.item.documento); orden_seleccionada=data.item; modal_ver_orden=true;">VER</b-button>
            </template>
          </b-table>
          <b-alert v-if="ordenes_traspaso.length == 0" show variant="warning" class="text-center m-3">Sin datos para listar.</b-alert>
        </div>
      </div>
    </div>

    <b-modal title="Solicitud de traspaso" v-model="modal_ver_orden" size="xl" hide-footer no-close-on-backdrop>
      <div v-if="orden_seleccionada != '' && orden_seleccionada.detalle">
        <h6><strong>Solicitud de traspaso:</strong> {{ orden_seleccionada.documento }}</h6>
        <h6><strong>Fecha: </strong>{{ orden_seleccionada.fecha }}</h6>
        <h6><strong>Bodega destino: </strong>{{ orden_seleccionada.bod+' - '+orden_seleccionada.nombod}}</h6>
        <h6><strong>Observacion: </strong>{{ orden_seleccionada.detalle.mensaje }}</h6>
        <h6><strong>Articulos: </strong></h6>
        <b-table v-if="!armando_salida" striped hover :items="orden_seleccionada.detalle.codigos" :fields="columnas_articulos_orden" :thead-tr-class="'text-center align-middle bg-dark text-white'">
        </b-table>
        <b-table v-else striped hover :items="orden_seleccionada.detalle.codigos" :fields="columnas_articulos_orden2" :thead-tr-class="'text-center align-middle bg-dark text-white'">
          <template v-slot:cell(cant_salida)="data">
            <b-form-input v-if="data.item.estantes.length > 0" type="text" :class="{'is-invalid': !validaNumero(data.item.cant_salida) || parseInt(data.item.cant_salida) > parseInt(data.item.cant)}" v-model="data.item.cant_salida" size="sm"></b-form-input>
          </template>
          <template v-slot:cell(estante)="data">
            <div class="container-fluid">
              <div class="row" v-for="(est, index) in data.item.estantes_seleccionados" :key="index+'est'">
                <div class="col-5 pr-0">
                  <b-form-input v-if="data.item.estantes.length > 0" type="text" :class="{'is-invalid': !validaNumero(est.cantidad) || !validaCantidadEstante(data.item)}" v-model="est.cantidad" @change="agregaEstante(); est.id_articulo_ingreso='';" @blur="agregaEstante();" @keyup.enter="agregaEstante();" size="sm"></b-form-input>
                </div>
                <div class="col-6 px-1">
                  <h6 v-if="data.item.estantes.length == 0" class="text-danger mt-1">Sin Stock.</h6>
                  <b-form-select v-else v-model="est.id_articulo_ingreso" size="sm" :class="{'is-invalid': !validaEstante(data.item)}">
                    <b-form-select-option :disabled="!desactivaEstante(estante)" v-for="estante in data.item.estantes" v-key="estante.esp_id+'esp'" v-text="'['+estante.esp_estante+'-'+estante.esp_seccion+'-'+estante.esp_numero+'] [ '+estante.ari_cantidad+' '+estante.ari_unidad_medida+' ]'" :value="estante.ari_id"></b-form-select-option>
                  </b-form-select>
                </div>
                <div class="col-1 ml-0 pl-0">
                  <b-button v-if="index>0" size="sm" variant="danger" @click="data.item.estantes_seleccionados.splice(index, 1)">X</b-button>
                </div>
              </div>
            </div>
          </template>
        </b-table>
        <h6 v-if="armando_salida">Observacion</h6>
        <b-form-textarea v-if="armando_salida" v-model="orden_seleccionada.detalle.mensaje_nuevo"></b-form-textarea>
      </div>
      <!-- {{ orden_seleccionada }} -->
      <div class="mt-3 pt-3 border-top text-center">
        <b-button v-if="!armando_salida" @click="armando_salida=true;" variant="success" size="sm">Crear Salida de Bodega</b-button>
        <b-button v-if="armando_salida" @click="armando_salida=false;" variant="danger" size="sm">Cancelar</b-button>
        <b-button v-if="armando_salida" :disabled="!validaDatos()" @click="registrarSalida();" variant="success" size="sm" class="ml-2">Registrar Salida</b-button>
      </div>
    </b-modal>

    <b-modal title="Historial de salidas" v-model="modal_historial" size="xl" no-close-on-backdrop content-class="alto-modal">
      <b-card class="mb-2">
        <div class="row pb-2 mb-2 border-bottom">
          <div class="col-12">
            <b-form-group label="Filtrar por:">
              <b-form-radio-group
                id="radio-group-1"
                v-model="tipo_filtro_historial"
                :options="tipos_filtro"
                name="radio-options"
              ></b-form-radio-group>
            </b-form-group>
          </div>
        </div>
        <div v-if="tipo_filtro_historial == 1" class="row">
          <div class="col-6">
            <h6>Desde</h6>
            <b-form-datepicker size="sm" v-model="filtro_fecha_desde" placeholder="SELECCIONA FECHA" label-help="Selecciona un día." label-no-date-selected="-" label-next-month="SIGUIENTE MES" label-prev-month="MES ANTERIOR" label-prev-year="AÑO ANTERIOR" label-next-year="SIGUIENTE AÑO" label-current-month="MES ACTUAL" start-weekday="1"></b-form-datepicker>
          </div>
          <div class="col-6">
            <h6>Hasta</h6>
            <b-form-datepicker size="sm" v-model="filtro_fecha_hasta" placeholder="SELECCIONA FECHA" label-help="Selecciona un día." label-no-date-selected="-" label-next-month="SIGUIENTE MES" label-prev-month="MES ANTERIOR" label-prev-year="AÑO ANTERIOR" label-next-year="SIGUIENTE AÑO" label-current-month="MES ACTUAL" start-weekday="1"></b-form-datepicker>
          </div>
        </div>
        <div v-if="tipo_filtro_historial == 2" class="row justify-content-center">
          <div class="col-6">
            <h6>Ingresa el N° de la salida</h6>
            <b-form-input size="sm" v-model="filtro_id_salida" placeholder="id salida" ></b-form-input>
          </div>
        </div>
        <div v-if="tipo_filtro_historial == 3" class="row justify-content-center">
          <div class="col-6">
            <h6>Ingresa el N° de la Solicitud de traspaso</h6>
            <b-form-input size="sm" v-model="filtro_orden_traspaso" placeholder="Solicitud de traspaso" ></b-form-input>
          </div>
        </div>
        <div class="row text-center">
          <div class="col-12 mt-2">
            <b-button :disabled="(tipo_filtro_historial == 2 && filtro_id_salida == '') || (tipo_filtro_historial == 3 && filtro_orden_traspaso == '')" @click="listarHistorialSalidas()" variant="success" size="sm"><i class="fas fa-fw fa-search mr-2"></i>Filtrar</b-button>
          </div>
        </div>
      </b-card>
      
      <div>
        <b-table v-if="historial_salidas.length > 0" striped hover :items="historial_salidas" :fields="columnas_historial" :thead-tr-class="'text-center align-middle bg-dark text-white'">
          <template v-slot:cell(sld_fecha_salida)="data">
            {{ data.value.split(' ')[0].split('-')[2]+'/'+data.value.split(' ')[0].split('-')[1]+'/'+data.value.split(' ')[0].split('-')[0]+' '+data.value.split(' ')[1].split(':')[0]+':'+data.value.split(' ')[1].split(':')[1] }}
          </template>
          <template v-slot:cell(opcion)="data">
            <b-button @click="listarDetalleSalida(data.item.sld_id);" variant="primary" size="sm">VER</b-button>
          </template>
        </b-table>
        <b-alert v-else class="text-center" show variant="info">Sin resultados.</b-alert>
      </div>
      <template #modal-footer>
        <div class="text-center w-100">
          <b-button @click="modal_historial=false;" variant="danger" size="sm">Cerrar</b-button>
        </div>
      </template>
    </b-modal>

    <b-modal title="Detalle de salida" size="lg" v-model="modal_detalle_salida">
      <div v-if="detalle_salida != ''" class="w-100">
        <h6><strong>N° Salida:</strong> {{ detalle_salida.sld_id }}</h6>
        <h6><strong>Fecha:</strong> {{ detalle_salida.sld_fecha_salida.split(' ')[0].split('-')[2]+'/'+detalle_salida.sld_fecha_salida.split(' ')[0].split('-')[1]+'/'+detalle_salida.sld_fecha_salida.split(' ')[0].split('-')[0]+' '+detalle_salida.sld_fecha_salida.split(' ')[1].split(':')[0]+':'+detalle_salida.sld_fecha_salida.split(' ')[1].split(':')[1] }}</h6>
        <h6><strong>Bodega destino:</strong> {{ detalle_salida.sld_bodega_destino }}</h6>
        <h6><strong>Observación:</strong> {{ detalle_salida.sld_observacion }}</h6>
        <h6><strong>Solicitud de traspaso:</strong> {{ detalle_salida.sld_orden_traspaso }}</h6>
      </div>
      <b-table striped hover :items="detalle_salida.articulos" :fields="columnas_detalle_salida" :thead-tr-class="'text-center align-middle bg-dark text-white'">
        <template v-slot:cell(ari_espacio)="data">
          {{data.item.esp_estante+'-'+data.item.esp_seccion+'-'+data.item.esp_numero}}
        </template>
      </b-table>
      <template #modal-footer>
        <div class="text-center w-100">
          <b-button @click="modal_detalle_salida=false;" variant="danger" size="sm">Cerrar</b-button>
        </div>
      </template>
    </b-modal>

  </div>
</template>

<script>
export default {
  data(){
    return{
      cargando_pagina   : false,
      sucursales        : [],
      salida            : {sucursal:'', articulos:''},
      articulos         : [],
      tipos_documentos  : [
        {value:33, text:'FACTURA'}, 
        {value:39, text:'BOLETA'}, 
        {value:52, text:'GUIA DESPACHO'}
      ],

      filtro_bodega     : '',
      filtro_busqueda   : '',
      ordenes_traspaso  : [],
      columnas_ordenes  : [
        {key: 'documento' , label: 'Documento'      , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'fecha'     , label: 'Fecha'          , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'bod'       , label: 'Bodega'         , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'nombod'    , label: 'Nombre Bodega'  , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'opcion'    , label: '-'                                , tdClass:'p-1 text-center', thClass:'p-1'}
      ],
      columnas_articulos_orden: [
        {key: 'codigo'  , label: 'SKU'        , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'nomart'  , label: 'Nombre'     , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'umed'    , label: 'U. Medida'  , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'cant'    , label: 'Cantidad'   , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'}
      ],
      columnas_articulos_orden2: [
        {key: 'codigo'        , label: 'SKU'                    , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'nomart'        , label: 'Nombre'                 , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'umed'          , label: 'U. Medida'              , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'cant'          , label: 'Cantidad Solicitada'    , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'cant_salida'   , label: 'Cantidad Salida'        , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'estante'       , label: 'Cantidad x Estante'     , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'}
      ],
      columnas_historial: [
        {key: 'sld_id'                , label: 'N° Salida'        , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'sld_fecha_salida'      , label: 'Fecha'            , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'sld_bodega_destino'    , label: 'Bodega destino'   , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'sld_observacion'       , label: 'Observacion'      , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'sld_orden_traspaso'    , label: 'Solicitud traspaso'   , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'opcion'                , label: '-'                                  , tdClass:'p-1 text-center', thClass:'p-1'}
      ],
      orden_seleccionada    : '',
      modal_ver_orden       : false,
      armando_salida        : false,
      modal_historial       : false,
      filtro_fecha_desde    : '',
      filtro_fecha_hasta    : '',
      filtro_id_salida      : '',
      filtro_orden_traspaso : '',
      historial_salidas     : [],
      tipos_filtro          : [
        {text:'Fechas'          , value: 1},
        {text:'N° Salida'       , value: 2},
        {text:'Orden traspaso'  , value: 3}
      ],
      tipo_filtro_historial: 1,

      modal_detalle_salida  : false,
      detalle_salida        : '',
      columnas_detalle_salida: [
        {key: 'ari_codigo'        , label: 'SKU'        , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'ari_nombre'        , label: 'Nombre'     , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'ari_unidad_medida' , label: 'U. Medida'  , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'ars_cantidad'      , label: 'Cantidad'   , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'ari_espacio'       , label: 'Estante'    , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'}
      ],
      soloNum : /^[0-9]*$/
    }
  },
  mounted(){
    this.listarSucursales();
    this.listarOrdenesTraspaso();
    this.filtro_fecha_hasta = new Date();
    this.filtro_fecha_desde = new Date(this.filtro_fecha_hasta.getTime() - (60*(24*30)*60000));
    this.filtro_fecha_desde = this.filtro_fecha_desde.getFullYear()+'-'+(this.filtro_fecha_desde.getMonth()+1)+'-'+this.filtro_fecha_desde.getDate();
    this.filtro_fecha_hasta = this.filtro_fecha_hasta.getFullYear()+'-'+(this.filtro_fecha_hasta.getMonth()+1)+'-'+this.filtro_fecha_hasta.getDate();

  },
  methods:{
    listarSucursales(){
      this.cargando_pagina = true;
      axios.post(base_url+'bodega/listarSucursales').then( resp => {
        this.sucursales       = resp.data;
      }).catch(error =>{
        console.log(error);
        this.cargando_pagina = false;
      });
    },

    listarProductosSucursal(){
      this.cargando_pagina = true;
      const datos = new URLSearchParams();
      datos.append('id_sucursal', this.salida.sucursal);
      axios.post(base_url+'bodega/listarProductosSucursal', datos).then( resp => {
        this.articulos        = resp.data;
        this.cargando_pagina  = false;
      }).catch(error =>{
        console.log(error);
        this.cargando_pagina = false;
      });
    },

    listarOrdenesTraspaso(){
      this.cargando_pagina = true;
      const datos = new URLSearchParams();
      datos.append('bodega', this.filtro_bodega);
      axios.post(base_url+'bodega/listarOrdenesTraspaso', datos).then( resp => {
        if(resp.data.status == 200){
          this.ordenes_traspaso = resp.data.sotrasp;
        }
        else{
          alert('Error al listar solicitudes de traspaso.');
        }
        this.cargando_pagina  = false;
      }).catch(error =>{
        console.log(error);
        this.cargando_pagina = false;
      });
    },

    listarOrdenTraspaso(id_orden){
      this.cargando_pagina = true;
      const datos = new URLSearchParams();
      datos.append('id_orden', id_orden);
      axios.post(base_url+'bodega/listarOrdenTraspaso', datos).then( resp => {
        if(resp.data.status == 200){
          this.orden_seleccionada.detalle = resp.data;
        }
        else{
          alert('Error al listar solicitud de traspaso.');
        }
        this.cargando_pagina  = false;
      }).catch(error =>{
        console.log(error);
        this.cargando_pagina = false;
      });
    },

    listarHistorialSalidas(){
      this.cargando_pagina = true;
      const datos = new URLSearchParams();
      datos.append('tipo_filtro'    , this.tipo_filtro_historial);
      datos.append('fecha_desde'    , this.filtro_fecha_desde);
      datos.append('fecha_hasta'    , this.filtro_fecha_hasta);
      datos.append('id_salida'      , this.filtro_id_salida);
      datos.append('orden_traspaso' , this.filtro_orden_traspaso);
      axios.post(base_url+'bodega/listarHistorialSalidas', datos).then( resp => {
        this.historial_salidas = resp.data;  
        this.cargando_pagina   = false;
      }).catch(error =>{
        console.log(error);
        alert('Error al listar historial de salidas.');
        this.cargando_pagina = false;
      });
    },

    listarDetalleSalida(id_salida){
      this.cargando_pagina = true;
      const datos = new URLSearchParams();
      datos.append('id_salida', id_salida);
      axios.post(base_url+'bodega/listarDetalleSalida', datos).then( resp => {
        this.detalle_salida       = resp.data;  
        this.modal_detalle_salida = true;
        this.cargando_pagina      = false;
      }).catch(error =>{
        console.log(error);
        alert('Error al listar detalle historial de salidas.');
        this.cargando_pagina = false;
      });
    },

    validaDatos(){
      var resp = true;
      this.orden_seleccionada.detalle.codigos.forEach(codigo => {
        if(parseInt(codigo.cant_salida) <= 0 || codigo.cant_salida == '' || parseInt(codigo.cant_salida) > parseInt(codigo.cant) ){
          resp = false;
        }
        let sumaEstantes = 0;
        codigo.estantes_seleccionados.forEach(estante => {
          sumaEstantes += parseInt(estante.cantidad);
          if(estante.cantidad <= 0 || estante.cantidad == '' || estante.id_articulo_ingreso == '' || parseInt(estante.cantidad) > parseInt(codigo.cant)){
            resp = false;
          }
        });
        if(parseInt(sumaEstantes) > parseInt(codigo.cant_salida)){
          resp = false;
        }
        codigo.estantes_seleccionados.forEach(estante_seleccionado => {
          codigo.estantes.forEach(estante_disponible => {
            if(estante_seleccionado.id_articulo_ingreso == estante_disponible.ari_id){            
              if(parseInt(estante_seleccionado.cantidad) > parseInt(estante_disponible.ari_cantidad)){
                resp = false;
              }
            }
          });
        });
          
      });
      return resp;
    },

    validaNumero(numero){
      return this.soloNum.test(numero);
    },

    validaEstante(codigo){
      let resp = true;
      codigo.estantes_seleccionados.forEach(estante_seleccionado => {
        codigo.estantes.forEach(estante_disponible => {
          if(estante_seleccionado.id_articulo_ingreso == estante_disponible.ari_id){            
            if(parseInt(estante_seleccionado.cantidad) > parseInt(estante_disponible.ari_cantidad)){
              resp = false;
            }
          }
        });
      });
      return resp;
    },

    validaCantidadEstante(codigo){
      let resp = true;
      let suma = 0;
      codigo.estantes_seleccionados.forEach(estante_seleccionado => {
        suma += parseInt(estante_seleccionado.cantidad);
      });
      if(suma > parseInt(codigo.cant_salida)){
        resp = false;
      }
      return resp;
    },

    desactivaEstante(estante){
      let resp = true;
      this.orden_seleccionada.detalle.codigos.forEach(codigo => {
        codigo.estantes_seleccionados.forEach(estante_seleccionado => {
          if(estante_seleccionado.id_articulo_ingreso == estante.ari_id){
            resp = false;
          }
        });
      }); 
      return resp;
    },

    agregaEstante(){
      let flagCero = false;
      this.orden_seleccionada.detalle.codigos.forEach(codigo => {
        let sumaArticulos = 0;
        codigo.estantes_seleccionados.forEach(estante => {
          sumaArticulos += parseInt(estante.cantidad);
          if(parseInt(estante.cantidad) == 0){
            flagCero = true;
          }
        });
        if(sumaArticulos < parseInt(codigo.cant_salida) && !flagCero){
          codigo.estantes_seleccionados.push({ id_articulo_ingreso: '', cantidad: 0 });
        }
        if(sumaArticulos > parseInt(codigo.cant)){
          codigo.estantes_seleccionados.pop();
          for(let i=codigo.estantes_seleccionados.length; i>sumaArticulos; i--){
            codigo.estantes_seleccionados.pop();
          }
        }
      });
    },

    registrarSalida(){
      this.cargando_pagina = true;
      const datos = new URLSearchParams();
      datos.append('salida', JSON.stringify(this.orden_seleccionada));
      axios.post(base_url+'bodega/registrarSalida', datos).then( resp => {
        if(resp.data.key == 1){
          alert('Salida registrada correctamente.');
          this.modal_ver_orden = false;
          this.listarOrdenesTraspaso();
        }
        else {
          alert(resp.data.msj);
        }
        this.cargando_pagina   = false;
      }).catch(error =>{
        console.log(error);
        this.cargando_pagina = false;
      });
    },

    fechaPasada(fecha){
      const fecha_estimada = new Date(fecha);
      const fecha_actual   = new Date();
      if(fecha_estimada < fecha_actual){
        return true;
      }
      else {
        return false;
      }
    }
  }
}

</script>

<style>
.alto-modal{
  min-height: 500px!important;
}
</style>
