<template>
  <div class="container-fluid">
    <loader-component :cargando_pagina="cargando_pagina"/>
    <div class="row">
      <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 text-md-left text-center">
        <h3><i class="fas fa-fw fa-box mr-2"></i>INGRESOS DE BODEGA</h3>
      </div>
      <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mt-sm-2 mt-md-0 text-md-right text-center">
        <b-button @click="modal_historial=true;" variant="success" size="sm"><i class="fas fa-fw fa-history mr-2"></i>HISTORIAL DE INGRESOS</b-button>
      </div>
    </div>

    <b-card v-if="nuevo_ingreso.oc == ''"  class="mt-4">
      <h5>Nuevo ingreso de bodega</h5>
      <div class="row justify-content-center mt-4">
        <div class="col-xl-6 col-md-10 col-12">
          Ingresa orden de compra:
          <b-form-input v-model="buscar_oc" @keyup.enter="listarOrdenCompra();" class="mb-3" size="sm"></b-form-input>
        </div>
        <div class="col-12 text-center">
          <b-button :disabled="buscar_oc == ''" @click="listarOrdenCompra();" variant="primary" size="sm"><i class="fas fa-fw fa-search mr-2"></i>BUSCAR</b-button>
        </div>
      </div>
    </b-card>
    

    <div v-else class="row justify-content-center mt-4">
      <div class="col-12">
        <b-card>
          <table>
            <td>
              <tr><strong class="pr-3">Orden de compra</strong></tr>
              <tr><strong class="pr-3">Fecha</strong></tr>
              <tr><strong class="pr-3">Bodega</strong></tr>
              <tr><strong class="pr-3">Glosa</strong></tr>
            </td>
            <td>
              <tr>:<span class="pl-3">{{ nuevo_ingreso.oc}}</span></tr>
              <tr>:<span class="pl-3">{{ nuevo_ingreso.fecha}}</span></tr>
              <tr>:<span class="pl-3">{{ nuevo_ingreso.bodega}}</span></tr>
              <tr>:<span class="pl-3">{{ nuevo_ingreso.glosa}}</span></tr>
            </td>
          </table>
          <h6 v-if="nuevo_ingreso.mensaje != ''"><strong>Mensaje:</strong> {{ nuevo_ingreso.mensaje }}</h6>

          <b-table striped hover :items="nuevo_ingreso.articulos" :fields="columnas_articulos" :thead-tr-class="'text-center align-middle bg-dark text-white'">
            <template v-slot:cell(codigo)="data">
              {{ data.item.codigo +'-'+ data.item.ver }}
            </template>
            <template v-slot:cell(saldo)="data">
              <span :class="{'badge badge-success badge-pill':data.value<=0, 'badge badge-warning badge-pill':data.value>0}">{{ data.value }}</span>
            </template>
            <template v-slot:cell(cant_recibida)="data">
              <b-form-input size="sm" type="text" v-model="data.item.cant_recibida" :class="{'is-valid':validaNumero(data.item.cant_recibida) && data.item.cant_recibida != 0, 'is-invalid':!validaNumero(data.item.cant_recibida) && data.item.cant_recibida != 0}" @change="cambioCantidad();"></b-form-input>
            </template>
            <template v-slot:cell(estantes_usar)="data">
              <b-form-input size="sm" type="text" v-model="data.item.estantes_usar" @change="data.item.estantes = cambioEstantes(data.item.estantes, data.item.estantes_usar);" :disabled="!validaNumero(data.item.cant_recibida) || data.item.cant_recibida == 0" :class="{'is-valid':validaNumero(data.item.estantes_usar) && data.item.estantes_usar !=0 && data.item.cant_recibida != 0, 'is-invalid':(!validaNumero(data.item.estantes_usar) || data.item.estantes_usar == 0) && data.item.cant_recibida != 0}"></b-form-input>
            </template>
            <template v-slot:cell(estantes)="data">
              <!-- <b-form-select size="sm" v-model="data.item.estante" :disabled="!validaNumero(data.item.cant_recibida) || data.item.cant_recibida == 0" :class="{'is-invalid': data.item.estante == 0 && validaNumero(data.item.cant_recibida) && data.item.cant_recibida != 0, 'is-valid': data.item.estante != 0 && validaNumero(data.item.cant_recibida)}">
                <b-form-select-option v-for="espacio in espacios_disponibles" :key="espacio.esp_id" :value="espacio.esp_id">
                  {{ espacio.esp_estante+'-'+espacio.esp_seccion+'-'+espacio.esp_numero+' ('+espacio.esp_peso+'kg)' }}
                </b-form-select-option>
              </b-form-select> -->
              <div style="min-width: 300px!important;" class="container-fluid">
                <div class="row p-0" v-for="(estante,index) in data.item.estantes" :key="'idx'+index">
                  <div class="col-4 p-0">
                    <b-form-input size="sm" type="text" v-model="estante.cantidad" :disabled="!validaNumero(data.item.cant_recibida) || data.item.cant_recibida == 0" :class="{'is-valid':validaNumero(estante.cantidad) && estante.cantidad != 0 && data.item.cant_recibida !=0, 'is-invalid':(!validaNumero(estante.cantidad) || estante.cantidad == 0 || !validaCantidadRecibida(data.item)) && data.item.cant_recibida != 0}"></b-form-input>
                  </div>
                  <div class="col-8 p-0">
                    <v-select style="width: 100%!important;" class="form-control form-control-sm p-0 h-auto" :reduce="(espacio) => espacio.esp_id" label="esp_nombre" v-model="estante.estante" :clearable="true" :closeOnSelect="true" :options="espacios_disponibles" :disabled="!validaNumero(data.item.cant_recibida) || data.item.cant_recibida == 0" :class="{'is-invalid': (estante.estante == 0 || estante.estante == null) && validaNumero(data.item.cant_recibida) && data.item.cant_recibida != 0, 'is-valid': estante.estante != 0 && estante.estante != null && validaNumero(data.item.cant_recibida) && data.item.cant_recibida != 0}">
                      <template v-slot:no-options="{ search, searching }">
                        <template v-if="searching">
                          SIN RESULTADOS PARA <em>{{ search }}</em>.
                        </template>
                        <em style="opacity: 0.5;" v-else>SIN OPCIONES PARA MOSTRAR.</em>
                      </template>
                      <template v-slot:option="option">
                        <div :class="{ 'disabled-option': !desactivaEstante(option) }" :disabled="!desactivaEstante(option)">
                          {{ option.esp_nombre }}
                        </div>
                      </template>
                    </v-select>
                  </div>
                </div>
              </div>
            </template>
            <template v-slot:cell(certificacion)="data">
              <b-form-checkbox class="text-center" v-model="data.item.certificacion" name="check-button" switch v-b-tooltip.hover title="Requiere certificación."></b-form-checkbox>
            </template>
          </b-table>
          
          <div class="row text-center">
            <div class="col"><b-button size="sm" @click="limpiarIngreso();" variant="danger">CANCELAR</b-button></div>
            <div class="col"><b-button size="sm" :disabled="!validarIngreso()" @click="modal_confirmar_oc=true;" variant="success">ACEPTAR</b-button></div>
          </div>
        </b-card>
      </div>
    </div>

    <b-modal v-model="modal_confirmar_oc" size="lg" title="CONFIRMAR" hide-footer no-close-on-backdrop>
      <div v-if="ingresando_datos_extra" class="w-100">
        <h6 class="mb-3 font-weight-bold text-center">* Campos obligatorios</h6>

        <h6><strong>*</strong> Fecha estimada de despacho:</h6>
        <b-form-datepicker size="sm" v-model="nuevo_ingreso.fecha_estimada_despacho" class="mb-3" :min="fecha_actual" placeholder="SELECCIONA FECHA" label-help="Selecciona un día." label-no-date-selected="-" label-next-month="SIGUIENTE MES" label-prev-month="MES ANTERIOR" label-prev-year="AÑO ANTERIOR" label-next-year="SIGUIENTE AÑO" label-current-month="MES ACTUAL" start-weekday="1"></b-form-datepicker>

        <h6><strong>*</strong> Tipo documento asociado:</h6>
        <b-form-select size="sm" v-model="nuevo_ingreso.tipo_doc_asociado" class="mb-3" :options="tipos_documentos"></b-form-select>
        <h6><strong>*</strong> Documento asociado:</h6>
        <b-form-input size="sm" v-model="nuevo_ingreso.doc_asociado" class="mb-3"></b-form-input>

        <h6><strong>*</strong> Rut transportista:</h6>
        <b-form-input size="sm" v-model="nuevo_ingreso.rut_transportista" @input="nuevo_ingreso.rut_transportista = formateaRut(nuevo_ingreso.rut_transportista)" class="mb-3" :class="{'is-valid': nuevo_ingreso.rut_transportista != '' && verificarRut(nuevo_ingreso.rut_transportista), 'is-invalid': nuevo_ingreso.rut_transportista != '' && !verificarRut(nuevo_ingreso.rut_transportista)}"></b-form-input>
        <h6>Orden transporte:</h6>
        <b-form-input size="sm" v-model="nuevo_ingreso.orden_transporte" class="mb-3"></b-form-input>
        <h6>Fecha documento transporte:</h6>
        <b-form-datepicker size="sm" v-model="nuevo_ingreso.fecha_transporte" class="mb-3" placeholder="SELECCIONA FECHA" label-help="Selecciona un día." label-no-date-selected="-" label-next-month="SIGUIENTE MES" label-prev-month="MES ANTERIOR" label-prev-year="AÑO ANTERIOR" label-next-year="SIGUIENTE AÑO" label-current-month="MES ACTUAL" start-weekday="1"></b-form-datepicker>
        <h6>Peso transporte(kg):</h6>
        <b-form-input size="sm" v-model="nuevo_ingreso.peso_transporte" class="mb-3"></b-form-input>

        <h6>Observación:</h6>
        <b-form-textarea v-model="nuevo_ingreso.observacion" placeholder="Ingresa texto..." rows="3" max-rows="6"></b-form-textarea>
        
      </div>
      <div v-else class="w-100">
        <div class="container-fluid mb-3 card p-3">
          <h6><strong>Confirma los siguentes datos para el ingreso de bodega:</strong></h6>
          <ul>
            <li v-show="articulo.cant_recibida > 0" v-for="articulo in nuevo_ingreso.articulos" :key="articulo.codigo">
              <div class="row">
                <div class="col-2">{{ articulo.codigo+'-'+articulo.ver }}</div>
                <div class="col-8 font-weight-bold">{{ articulo.nomart }}</div>
                <div class="col-2 text-right">    
                  <span class="badge badge-dark badge-pill">
                    <strong>{{ articulo.cant_recibida +' '+articulo.umed }}</strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <ul>
                    <li v-for="(estante, index) in articulo.estantes" :key="'est'+index">{{ estante.cantidad+' '+articulo.umed }} en estante <span class="badge badge-primary badge-pill" v-for="espacio in espacios_disponibles" :key="'esp'+espacio.esp_id" v-if="espacio.esp_id == estante.estante">{{ espacio.esp_nombre }}</span></li>
                  </ul>
                </div>
              </div>
            </li>
          </ul>
        </div>
        <div class="container-fluid card p-3">
          <div class="row">
            <div class="col-5"><h6>Fecha estimada de despacho</h6></div>
            <div class="col-7"><h6>: {{ nuevo_ingreso.fecha_estimada_despacho.split('-')[2]+'-'+nuevo_ingreso.fecha_estimada_despacho.split('-')[1]+'-'+nuevo_ingreso.fecha_estimada_despacho.split('-')[0] }}</h6></div>
            <div v-if="nuevo_ingreso.tipo_doc_asociado != ''" class="col-5"><h6>Tipo documento asociado</h6></div>
            <div v-if="nuevo_ingreso.tipo_doc_asociado != ''" class="col-7"><h6 v-for="(doc,index) in tipos_documentos" :key="'tip'+index" v-show="doc.value==nuevo_ingreso.tipo_doc_asociado">: {{doc.text}}</h6></div>
            <div v-if="nuevo_ingreso.doc_asociado != ''" class="col-5"><h6>N° Documento asociado</h6></div>
            <div v-if="nuevo_ingreso.doc_asociado != ''" class="col-7"><h6>: {{nuevo_ingreso.doc_asociado}}</h6></div>
            <div class="col-5"><h6>Rut Transportista</h6></div>
            <div class="col-7"><h6>: {{nuevo_ingreso.rut_transportista}}</h6></div>
            <div v-if="nuevo_ingreso.orden_transporte != ''" class="col-5"><h6>Orden de transporte</h6></div>
            <div v-if="nuevo_ingreso.orden_transporte != ''" class="col-7"><h6>: {{nuevo_ingreso.orden_transporte}}</h6></div>
            <div v-if="nuevo_ingreso.fecha_transporte != ''" class="col-5"><h6>Fecha documento transporte</h6></div>
            <div v-if="nuevo_ingreso.fecha_transporte != ''" class="col-7"><h6>: {{nuevo_ingreso.fecha_transporte}}</h6></div>
            <div v-if="nuevo_ingreso.peso_transporte != ''" class="col-5"><h6>Peso transporte</h6></div>
            <div v-if="nuevo_ingreso.peso_transporte != ''" class="col-7"><h6>: {{nuevo_ingreso.peso_transporte}} kg.</h6></div>
            <div v-if="nuevo_ingreso.observacion != ''" class="col-5"><h6>Observación</h6></div>
            <div v-if="nuevo_ingreso.observacion != ''" class="col-7"><h6>: {{nuevo_ingreso.observacion}}</h6></div>
          </div>
        </div>
        <b-alert show v-if="muestra_alerta" variant="warning" class="text-center mt-3"><strong><i class="fas fa-fw fa-exclamation-triangle mr-2"></i> {{msj_alerta}}</strong></b-alert>
      </div>

      <div v-if="ingresando_datos_extra" class="row mt-3 pt-3 border-top text-center">
        <div class="col"><b-button @click="modal_confirmar_oc=false;ingresando_datos_extra=true;" size="sm" variant="danger">CANCELAR</b-button></div>
        <div class="col"><b-button :disabled="!validaCamposObligatorios()" @click="validarDocAsociado(); registro_iniciado=false;" size="sm" variant="success">CONTINUAR</b-button></div>
      </div>
      <div v-else class="row mt-3 pt-3 border-top text-center">
        <div class="col"><b-button @click="modal_confirmar_oc=false;ingresando_datos_extra=true;" size="sm" variant="danger">CANCELAR</b-button></div>
        <div class="col"><b-button :disabled="registro_iniciado" @click="registrarIngreso();" size="sm" variant="success">CONFIRMAR</b-button></div>
      </div>
    </b-modal>


    <b-modal v-model="modal_msj" size="lg" title="REGISTRO DE INGRESO" hide-footer no-close-on-backdrop :scrollable="false">
      <div class="row">
        <div class="col-12">
          <h6><strong>{{msj_registro}}</strong></h6>
        </div>
      </div>
      <div class="row mt-3 pt-3 border-top">
        <div class="col text-center">
          <b-button @click="limpiarDatos();" variant="primary" size="sm">ACEPTAR</b-button>
        </div>
      </div>
    </b-modal>


    <b-modal title="Historial de ingresos" v-model="modal_historial" size="xl" no-close-on-backdrop content-class="alto-modal">
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
            <h6>Ingresa el N° del ingreso</h6>
            <b-form-input size="sm" v-model="filtro_id_ingreso" placeholder="nro ingreso" ></b-form-input>
          </div>
        </div>
        <div class="row text-center">
          <div class="col-12 mt-2">
            <b-button :disabled="(tipo_filtro_historial == 2 && filtro_id_ingreso == '')" @click="listarHistorialIngresos()" variant="success" size="sm"><i class="fas fa-fw fa-search mr-2"></i>Filtrar</b-button>
          </div>
        </div>
      </b-card>
      
      <div>
        <b-table v-if="historial_ingresos.length > 0" striped hover :items="historial_ingresos" :fields="columnas_historial" :thead-tr-class="'text-center align-middle bg-dark text-white'">
          <template v-slot:cell(ing_fecha_ingreso)="data">
            {{ data.value.split(' ')[0].split('-')[2]+'/'+data.value.split(' ')[0].split('-')[1]+'/'+data.value.split(' ')[0].split('-')[0]+' '+data.value.split(' ')[1].split(':')[0]+':'+data.value.split(' ')[1].split(':')[1] }}
          </template>
          <template v-slot:cell(ing_despacho_estimado)="data">
            {{ data.value.split('-')[2]+'/'+data.value.split('-')[1]+'/'+data.value.split('-')[0]}}
          </template>
          <template v-slot:cell(ing_rut_transportista)="data">
            <span v-if="data.value && data.item.ing_dv_transportista">{{ data.value+'-'+data.item.ing_dv_transportista }}</span>
          </template>
          <template v-slot:cell(ing_documento_asociado)="data">
            <span v-for="doc in tipos_documentos" v-if="doc.value==data.item.ing_tipo_documento">{{ doc.text + ' ' + data.value}}</span>
          </template>
          <template v-slot:cell(opcion)="data">
            <b-button @click="listarDetalleIngreso(data.item.ing_id);" variant="primary" size="sm">VER</b-button>
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

    <b-modal title="Detalle de ingreso" size="lg" v-model="modal_detalle_ingreso">
      <div v-if="detalle_ingreso != ''" class="w-100">
        <h6><strong>N° Ingreso:</strong> {{ detalle_ingreso.ing_id }}</h6>
        <h6><strong>Fecha:</strong> {{ detalle_ingreso.ing_fecha_ingreso.split(' ')[0].split('-')[2]+'/'+detalle_ingreso.ing_fecha_ingreso.split(' ')[0].split('-')[1]+'/'+detalle_ingreso.ing_fecha_ingreso.split(' ')[0].split('-')[0]+' '+detalle_ingreso.ing_fecha_ingreso.split(' ')[1].split(':')[0]+':'+detalle_ingreso.ing_fecha_ingreso.split(' ')[1].split(':')[1] }}</h6>
        <h6><strong>OC:</strong> {{ detalle_ingreso.ing_orden_compra }}</h6>
        <h6><strong>Doc. Asociado:</strong> <span v-for="doc in tipos_documentos" v-if="doc.value==detalle_ingreso.ing_tipo_documento">{{ doc.text + ' ' + detalle_ingreso.ing_documento}}</span></h6>
        <h6><strong>Despacho estimado:</strong> {{ detalle_ingreso.ing_despacho_estimado.split('-')[2]+'/'+detalle_ingreso.ing_despacho_estimado.split('-')[1]+'/'+detalle_ingreso.ing_despacho_estimado.split('-')[0] }}</h6>
        <h6><strong>Rut transportista:</strong> {{ detalle_ingreso.ing_rut_transportista+'-'+detalle_ingreso.ing_dv_transportista }}</h6>
        <h6><strong>Peso de carga(kg):</strong> {{ detalle_ingreso.ing_peso_transporte }}</h6>
        <h6><strong>Observación:</strong> {{ detalle_ingreso.ing_observacion }}</h6>
      </div>
      <b-table striped hover :items="detalle_ingreso.articulos" :fields="columnas_detalle_ingreso" :thead-tr-class="'text-center align-middle bg-dark text-white'">
        <template v-slot:cell(ari_espacio)="data">
          {{data.item.esp_estante+'-'+data.item.esp_seccion+'-'+data.item.esp_numero}}
        </template>
      </b-table>
      <template #modal-footer>
        <div class="text-center w-100">
          <b-button @click="modal_detalle_ingreso=false;" variant="danger" size="sm">Cerrar</b-button>
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
      //nuevo_ingreso     : {documento:'', tipo_documento:'', orden_compra:'1133762', sucursal:'', fecha_despacho:'', peso:'', espacio:''},
      sucursales        : [],
      tipos_documentos  : [
        {value:33, text:'FACTURA'}, 
        {value:52, text:'GUIA DESPACHO'}
      ],
      espacios_disponibles  : [],
      esp_disp_filtrados    : [],
      modal_msj             : false,
      msj_registro          : '',
      filtro_estante        : '',
      fecha_actual          : '',

      columnas_articulos    : [
        {key: 'codigo'        , label: 'Codigo'             , sortable: false  , tdClass:'p-1', thClass:'p-1'},
        {key: 'nomart'        , label: 'Articulo'           , sortable: false  , tdClass:'p-1', thClass:'p-1'},
        {key: 'umed'          , label: 'U. Medida'          , sortable: false  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'cant'          , label: 'Cantidad comprada'  , sortable: false  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'saldo'         , label: 'Saldo pendiente'    , sortable: false  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'cant_recibida' , label: 'Cantidad recibida'  , sortable: false  , tdClass:'p-1', thClass:'p-1'},
        {key: 'estantes_usar' , label: 'Estantes a usar'    , sortable: false  , tdClass:'p-1', thClass:'p-1'},
        {key: 'estantes'      , label: 'Cantidad x Estante' , sortable: false  , tdClass:'p-1', thClass:'p-1'},
        {key: 'certificacion' , label: 'Certificación'      , sortable: false  , tdClass:'p-1', thClass:'p-1'}
      ],

      nuevo_ingreso         : {
        bodega                  :'', 
        fecha                   :'', 
        glosa                   :'', 
        mensaje                 :'', 
        oc                      :'', 
        articulos               :[], 
        fecha_estimada_despacho :'', 
        doc_asociado            :'', 
        tipo_doc_asociado       :'', 
        rut_transportista       :'',
        orden_transporte        :'',
        fecha_transporte        :'',
        peso_transporte         :'',
        observacion             :''
      },
      buscar_oc             : '',
      modal_confirmar_oc    : false,
      ingresando_datos_extra: true,
      muestra_alerta        : false,
      msj_alerta            : '',
      formato_numero        : /^[0-9]+([.][0-9]*)?$/,

      modal_historial       : false,
      modal_detalle_ingreso : false,
      historial_ingresos    : [],
      detalle_ingreso       : '',

      filtro_fecha_desde    : '',
      filtro_fecha_hasta    : '',
      filtro_id_ingreso     : '',
      tipos_filtro          : [
        {text:'Fechas'          , value: 1},
        {text:'N° Ingreso'      , value: 2}
      ],
      tipo_filtro_historial : 1,
      registro_iniciado     : false,

      columnas_historial: [
        {key: 'ing_id'                , label: 'N° Ingreso'         , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'ing_fecha_ingreso'     , label: 'Fecha'              , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'ing_orden_compra'      , label: 'OC'                 , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'ing_documento'         , label: 'Doc. Asoc.'         , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'ing_despacho_estimado' , label: 'Desp. Estimado'     , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'ing_rut_transportista' , label: 'Rut Transportista'  , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'ing_peso_transporte'   , label: 'Peso carga'         , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'opcion'                , label: '-'                                    , tdClass:'p-1 text-center', thClass:'p-1'}
      ],

      columnas_detalle_ingreso: [
        {key: 'ari_codigo'            , label: 'SKU'            , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'ari_nombre'            , label: 'Nombre'         , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'ari_unidad_medida'     , label: 'U. Medida'      , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'ari_cantidad'          , label: 'Cantidad'       , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'ari_sucursal_destino'  , label: 'Bod. Destino'   , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'ari_espacio'           , label: 'Estante'        , sortable: true  , tdClass:'p-1 text-center', thClass:'p-1'}
      ],
    }
  },
  mounted(){
    this.fecha_actual = new Date();
    this.listarSucursales();
    this.listarEspaciosDisponibles();
    this.filtro_fecha_hasta = new Date();
    this.filtro_fecha_desde = new Date(this.filtro_fecha_hasta.getTime() - (60*(24*30)*60000));
    this.filtro_fecha_desde = this.filtro_fecha_desde.getFullYear()+'-'+(this.filtro_fecha_desde.getMonth()+1)+'-'+this.filtro_fecha_desde.getDate();
    this.filtro_fecha_hasta = this.filtro_fecha_hasta.getFullYear()+'-'+(this.filtro_fecha_hasta.getMonth()+1)+'-'+this.filtro_fecha_hasta.getDate();
  },
  methods:{
    listarOrdenCompra(){
      if(this.buscar_oc == ''){
        return;
      }
      this.cargando_pagina = true;
      const datos = new URLSearchParams();
      datos.append('orden_compra', this.buscar_oc);
      axios.post(base_url+'bodega/listarOrdenCompra', datos).then( resp => {
        if(resp.data.status == 200){
          if(resp.data.data.Cod == 200){
            this.nuevo_ingreso.oc        = resp.data.data.ocompra;
            this.nuevo_ingreso.bodega    = resp.data.data.bod;
            this.nuevo_ingreso.fecha     = resp.data.data.fecha;
            this.nuevo_ingreso.glosa     = resp.data.data.glosa;
            this.nuevo_ingreso.mensaje   = resp.data.data.mensaje;
            this.nuevo_ingreso.articulos = resp.data.data.codigos;
          }
          else{
            alert('Orden de compra no encontrada.');
          }
        }
        else {
          alert('Falló la conexion con CGI interno.');
        }
        this.cargando_pagina      = false;
      }).catch(error =>{
        this.cargando_pagina = false;
      });
    },

    limpiarIngreso(){
      this.buscar_oc = '';
      this.nuevo_ingreso = {
        bodega                  :'', 
        fecha                   :'', 
        glosa                   :'', 
        mensaje                 :'', 
        oc                      :'', 
        articulos               :[], 
        fecha_estimada_despacho :'', 
        doc_asociado            :'', 
        tipo_doc_asociado       :'', 
        rut_transportista       :'',
        orden_transporte        :'',
        fecha_transporte        :'',
        peso_transporte         :'',
        observacion             :''
      }
    },

    validaCamposObligatorios(){
      if( this.nuevo_ingreso.fecha_estimada_despacho  == '' || 
          this.nuevo_ingreso.tipo_doc_asociado        == '' || 
          this.nuevo_ingreso.doc_asociado             == '' || 
          !this.verificarRut(this.nuevo_ingreso.rut_transportista)){
        return false;
      }
      else{
        return true;
      }
    },

    validarIngreso(){
      let resp           = true;
      let cant_ingresada = false;
      this.nuevo_ingreso.articulos.forEach(articulo => {
        // VALIDACION DE CAMPOS DE INGRESO.
        if(   
            !this.validaNumero(articulo.cant_recibida) || 
            (this.validaNumero(articulo.cant_recibida) && articulo.cant_recibida != 0 && (articulo.estantes_usar == 0 || articulo.estantes_usar == '' || articulo.estantes_usar == null) ) 
          ){
          resp = false;
        }
        if(this.validaNumero(articulo.cant_recibida) && articulo.cant_recibida != 0){
          articulo.estantes.forEach(estante => {
            if(
                !this.validaNumero(estante.cantidad) ||
                (this.validaNumero(estante.cantidad) && estante.cantidad != 0 && (estante.cantidad > articulo.cant_recibida)) ||
                estante.estante == '' || estante.estante == null || estante.estante == 0
              ){
              resp = false;
            }
          });
        }
        if( this.validaNumero(articulo.cant_recibida) && articulo.cant_recibida != 0 ){ // valida que al menos un articulo tenga cantidad recibida
          cant_ingresada = true;
        }
      });
      return resp && cant_ingresada;
    },

    validaCantidadRecibida(datos){
      var suma = 0;
      datos.estantes.forEach(estante => {
        suma += parseFloat(estante.cantidad);
      });
      if(suma == parseFloat(datos.cant_recibida)){
        return true;
      }
      else {
        return false;
      }
    },

    cambioCantidad(){
      this.nuevo_ingreso.articulos.forEach(articulo => {
        if(articulo.cant_recibida == 0){
          articulo.estantes_usar = 0;
          articulo.estantes = [{cantidad:0, estante:0}];
        }
      });
    },

    cambioEstantes(estantes, estantes_usar){
      estantes = [];
      if(estantes_usar > 0){
        for(var i=0; i<estantes_usar; i++){
          estantes.push({cantidad:0, estante:0});
        }
      }
      else {
        estantes.push({cantidad:0, estante:0});
      }
      return estantes;
      /* if(estantes.length > estantes_usar){

      }
      else {

      } */
    },

    validaNumero(cantidad){
      if(!this.formato_numero.test(cantidad)){
        return false;
      }
      else{
        return true;
      }
    },

    listarEspaciosDisponibles(){
      this.cargando_pagina = true;
      const datos = new URLSearchParams();
      datos.append('peso', this.nuevo_ingreso.peso);
      axios.post(base_url+'bodega/listarEspaciosDisponibles', datos).then( resp => {
        this.espacios_disponibles = resp.data;
        this.filtrarEstantes();
        this.cargando_pagina      = false;
      }).catch(error =>{
        alert('Error al listar espacios disponibles.');
        console.log(error);
        this.cargando_pagina = false;
      });
    },

    filtrarEstantes(){
      if(this.filtro_estante != ''){
        this.esp_disp_filtrados = [];
        this.espacios_disponibles.forEach(espacio => {
          if(espacio.esp_estante == this.filtro_estante){
            this.esp_disp_filtrados.push(espacio);
          }
        });
      }
      else {
        this.esp_disp_filtrados = JSON.parse(JSON.stringify(this.espacios_disponibles))
      }
    },

    listarSucursales(){
      this.cargando_pagina = true;
      axios.post(base_url+'bodega/listarSucursales').then( resp => {
        this.sucursales       = resp.data;
        this.cargando_pagina  = false;
      }).catch(error =>{
        alert('Error al listar sucursales.');
        console.log(error);
        this.cargando_pagina = false;
      });
    },

    /* validaDatos(){
      if(this.nuevo_ingreso.documento != '' && this.nuevo_ingreso.tipo_documento != '' && this.nuevo_ingreso.sucursal != '' && this.nuevo_ingreso.fecha_despacho != '' && this.nuevo_ingreso.peso != ''){
        return true;
      }
      else {
        return false;
      }
    }, */

    validarDocAsociado(){
      const datos = new URLSearchParams();
      datos.append('doc', this.nuevo_ingreso.doc_asociado);
      datos.append('tipo', this.nuevo_ingreso.tipo_doc_asociado);
      axios.post(base_url+'bodega/validarDocAsociado', datos).then( resp => {
        if(resp.data.key == 2){
          this.muestra_alerta = true;
          this.msj_alerta     = 'Este documento de proveedor asociado ya fue digitado anteriormente.';
        }
        else {
          this.muestra_alerta = false;
        }
        this.ingresando_datos_extra = false;
        this.cargando_pagina        = false;

      }).catch(error =>{
        alert('Error al validar el doc asociado.');
        console.log(error);
        this.cargando_pagina = false;
      });
    },

    registrarIngreso(){
      this.cargando_pagina  = true;
      let ingreso           = JSON.parse(JSON.stringify(this.nuevo_ingreso));
      const rut_limpio      = ingreso.rut_transportista.replace(/\./g,'').replace(/[^k|K|\d]/g, '').replace(/\-/g, '').trim().toUpperCase();
      const dv              = rut_limpio.substr(-1, 1);
      const rut             = rut_limpio.substr(0, rut_limpio.length-1);

      ingreso.rut_transportista = rut;
      ingreso.dv_transportista  = dv;

      const datos = new URLSearchParams();
      datos.append('ingreso', JSON.stringify(ingreso));
      axios.post(base_url+'bodega/registrarIngreso', datos).then( resp => {
        if(resp.data.key == 1){
          alert('Ingreso registrado correctamente.');
          this.limpiarIngreso();
          this.modal_confirmar_oc = false;
        }
        else {
          alert(resp.data.msj);
        }
        this.cargando_pagina   = false;
      }).catch(error =>{
        console.log(error);
        alert('Error al registrar ingreso.');
        this.cargando_pagina = false;
      });
    },

    listarHistorialIngresos(){
      this.cargando_pagina = true;
      const datos = new URLSearchParams();
      datos.append('tipo_filtro'    , this.tipo_filtro_historial);
      datos.append('fecha_desde'    , this.filtro_fecha_desde);
      datos.append('fecha_hasta'    , this.filtro_fecha_hasta);
      datos.append('id_ingreso'     , this.filtro_id_ingreso);
      datos.append('orden_traspaso' , this.filtro_orden_traspaso);
      axios.post(base_url+'bodega/listarHistorialIngresos', datos).then( resp => {
        this.historial_ingresos = resp.data;  
        this.cargando_pagina    = false;
      }).catch(error =>{
        console.log(error);
        alert('Error al listar historial de ingresos.');
        this.cargando_pagina = false;
      });
    },

    listarDetalleIngreso(id_ingreso){
      this.cargando_pagina = true;
      const datos = new URLSearchParams();
      datos.append('id_ingreso', id_ingreso);
      axios.post(base_url+'bodega/listarDetalleIngreso', datos).then( resp => {
        this.detalle_ingreso       = resp.data;  
        this.modal_detalle_ingreso = true;
        this.cargando_pagina       = false;
      }).catch(error =>{
        console.log(error);
        alert('Error al listar detalle historial de ingresos.');
        this.cargando_pagina = false;
      });
    },

    desactivaEstante(espacio){
      let resp = true;
      this.nuevo_ingreso.articulos.forEach(articulo => {
        articulo.estantes.forEach(estante => {
          if(estante.estante == espacio.esp_id){
            resp = false;
          }
        });
      });
      return resp;
    },

    limpiarDatos(){
      this.nuevo_ingreso = {documento:'', tipo_documento:'', orden_compra:'', sucursal:'', fecha_despacho:'', peso:'', espacio:''};
      this.espacios_disponibles = [];
      this.modal_msj = false;
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

    verificarRut(rut){
      let resp=false;
      if(rut != null && rut.toString().length >= 11 && rut.toString().length < 14){
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