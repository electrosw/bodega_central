<template>
  <div class="container-fluid p-0">
    <loader-component :cargando_pagina="cargando_pagina"/>
    <biometria-component :modal_verificar_huella="modal_verificar_huella" :funcion_ejecucion="funcion_verificar" @verificacion_correcta="verificacionCorrecta" @cerrar_modal_biometria="modal_verificar_huella=false"/>

    <div class="row justify-content-center mt-4">
      <div class="col-12">
        <b-card>
          <div class="container-fluid p-0 mb-3">
            <div class="row justify-content-end mb-3">
              <div class="col text-left">
                <h4>Nueva valorización</h4>
              </div>
              <div class="col-xl-2 col-md-3 col-6">
                <b-button variant="danger" size="sm" block @click="modal_confirmar_cancelar=true;"><i class="fas fa-times mr-2"></i>CANCELAR</b-button>
              </div>
              <div class="col-xl-2 col-md-3 col-6">
                <b-button variant="primary" size="sm" block @click="funcion_verificar='registrarValorizacion'; modal_verificar_huella=true;"><i class="fas fa-save mr-2"></i>GUARDAR</b-button>
              </div>
              <div class="col-xl-2 col-md-3 col-6">
                <b-button variant="success" size="sm" block @click="modal_confirmar_finalizar=true"><i class="fas fa-chevron-circle-right mr-2"></i>FINALIZAR</b-button>
              </div>
            </div>
            <div class="row justify-content-center">
              <div class="col-xl-4 col-md-6 col-12 mb-2">
                <b-input-group prepend="Factura" size="sm">
                  <b-form-input v-model="valorizacion.factura" placeholder="Ej. 14322"></b-form-input>
                </b-input-group>
              </div>
              <div class="col-xl-4 col-md-6 col-12 mb-2">
                <b-input-group prepend="DIN" size="sm">
                  <b-form-input v-model="valorizacion.din" placeholder="Ej. 14322"></b-form-input>
                </b-input-group>
              </div>
              <div class="col-xl-4 col-md-6 col-12 mb-2">
                <b-input-group prepend="Rut Carpeta" size="sm">
                  <b-form-input v-model="valorizacion.rut_carpeta" placeholder="Ej. 14322"></b-form-input>
                </b-input-group>
              </div>
              <div class="col-xl-4 col-md-6 col-12 mb-2">
                <b-input-group prepend="Costo Importacion (moneda extranjera)" size="sm">
                  <b-form-input v-model="valorizacion.costo_importacion" placeholder="Ej. 14322" @input="valorizacion.costo_importacion = formatearMiles(valorizacion.costo_importacion); reajustaValoresOC();" @change="tipoCambio();"></b-form-input>
                </b-input-group>
              </div>
              <div class="col-xl-4 col-md-6 col-12 mb-2">
                <b-input-group prepend="CIF Pagado (pesos)" size="sm">
                  <b-form-input v-model="valorizacion.costo_pagado" placeholder="Ej. 14322" @input="valorizacion.costo_pagado = formatearMiles(valorizacion.costo_pagado); reajustaValoresOC();" @change="tipoCambio();"></b-form-input>
                </b-input-group>
              </div>
              <div class="col-xl-4 col-md-6 col-12 mb-2">
                <b-input-group prepend="Comisión Banco $ " size="sm">
                  <b-form-input v-model="valorizacion.comision_banco" placeholder="Ej. 14322" @input="valorizacion.comision_banco = formatearMiles(valorizacion.comision_banco); reajustaValoresOC();"></b-form-input>
                </b-input-group>
              </div>
              
            </div>
          </div>

          <b-table striped hover :items="valorizacion.items" :fields="columnas_valorizacion" class="rounded" :thead-tr-class="'text-center align-middle bg-dark text-white'">
            <template v-slot:cell(glosa)="data">
              <b-form-select v-model="data.item.glosa" :options="glosas_valorizacion" size="sm" value-field="glo_id" text-field="glo_glosa"></b-form-select>
            </template>
            <template v-slot:cell(monto)="data">
              <b-form-input v-model="data.item.monto" size="sm" @input="data.item.monto = formatearMiles(data.item.monto); reajustaValoresOC();"></b-form-input>
            </template>
            <template v-slot:cell(observacion)="data">
              <b-form-input v-model="data.item.observacion" @keyup.enter="actualizarObservacion(data.item.id)" size="sm"></b-form-input>
            </template>
            <template v-slot:cell(opcion)="data">
              <div class="w-100 text-center">
                <b-button v-if="data.index>0" size="sm" variant="danger" @click="eliminarItem(data.index)"><i class="fas fa-trash-alt"></i></b-button>
              </div>
            </template>
            <template v-slot:head(opcion)="data">
              <div class="w-100 text-center">
                <b-button variant="primary" size="sm" block @click="agregarItem()"><i class="fas fa-plus fa-fw"></i></b-button>
              </div>
            </template>
          </b-table>

          <div class="row justify-content-center">
            <div class="col-xl-3 col-md-4 col-6">
              <b-button variant="outline-success" size="sm" block @click="modal_nueva_glosa=true;"><i class="fas fa-plus mr-2"></i>CREAR NUEVA GLOSA</b-button>
            </div>
            <!-- <div class="col-xl-3 col-md-4 col-6">
              <b-button variant="primary" size="sm" block @click="agregarItem()"><i class="fas fa-plus mr-2"></i>AGREGAR ITEM</b-button>
            </div> -->
          </div>         
          
          <div class="row mt-2">
            <div class="col-12 text-right">
                <table class="ml-auto">
                  <tr>
                    <td>Total gastos de importación : </td>
                    <td><b v-text="'$ '+ totalCostos().toLocaleString('de-DE')"></b></td>
                  </tr>
                  <tr>
                    <td>Tipo de cambio inicial : </td>
                    <td><b v-text="'$ '+ parseFloat(parseFloat(tipoCambio().toFixed(2))).toLocaleString('de-DE')"></b></td>
                  </tr>
                  <tr>
                    <td>% Gasto S/Import. : </td>
                    <td><b v-text="'% '+ parseFloat(parseFloat(porcentajeCostos()).toFixed(2)) * 100"></b></td>
                  </tr>
                  <tr>
                    <td>Tipo de cambio reajustado : </td>
                    <td><b v-text="'$ '+ parseFloat(parseFloat(tipoCambioReajustado()).toFixed(2)).toLocaleString('de-DE')"></b></td>
                  </tr>
                </table>
              

            </div>
          </div>
          
        </b-card>
      </div>
      <div class="col-12">
        <b-card class="mt-4">
          <div class="container-fluid p-0 mb-3">
            <div class="row justify-content-end mb-3">
              <div class="col text-left">
                <h4>Orden de compra {{ orden_compra.oc}}</h4>
              </div>
            </div>
          </div>
          <table class="w-100">
            <td>
              <tr><strong class="pr-3">Fecha</strong>:<span class="pl-3">{{ orden_compra.fecha}}</span></tr>
            </td>
            <td>
              <tr><strong class="pr-3">Bodega</strong>:<span class="pl-3">{{ orden_compra.bodega}}</span></tr>
            </td>
            <td>
              <tr><strong class="pr-3">Glosa</strong>:<span class="pl-3">{{ orden_compra.glosa}}</span></tr>
            </td>
          </table>
          <h6 v-if="orden_compra.mensaje != ''"><strong>Mensaje:</strong> {{ orden_compra.mensaje }}</h6>

          <b-table striped hover :items="orden_compra.articulos" :fields="columnas_articulos" :thead-tr-class="'text-center align-middle bg-dark text-white'" class="mt-2">
            <template v-slot:cell(codigo)="data">
              {{ data.item.codigo +'-'+ data.item.ver }}
            </template>
            <template v-slot:cell(cant)="data">
              {{ data.value.toLocaleString('de-DE') }}
            </template>
            <template v-slot:cell(valorextra)="data">
              <b-form-input v-model="data.item.valorextra" size="sm" @input="data.item.valorextra = formatearMiles(data.item.valorextra); reajustaValoresOC();"></b-form-input>
            </template>
            <template v-slot:cell(totalextra)="data">
              {{ data.value.toLocaleString('de-DE') }}
            </template>
            <template v-slot:cell(totalpesos)="data">
              {{ data.value.toLocaleString('de-DE') }}
            </template>
            <template v-slot:cell(valorpesos)="data">
              {{ data.value.toLocaleString('de-DE') }}
            </template>
          </b-table>
        </b-card>
      </div>
    </div>

    <b-modal v-model="modal_nueva_glosa" size="md" title="NUEVA GLOSA" hide-footer no-close-on-backdrop :scrollable="false">
      <div class="row">
        <div class="col-12">
          <h6>Nueva glosa:</h6>
          <b-form-input v-model="nueva_glosa" placeholder="Ej. Gastos extra."></b-form-input>
        </div>
      </div>
      <div class="row mt-3 pt-3 border-top justify-content-center">
        <div class="col-4 text-center">
          <b-button @click="modal_nueva_glosa=false; nueva_glosa='';" variant="danger" size="sm"><i class="fas fa-ban fa-fw ml-2"></i>CANCELAR</b-button>
        </div>
        <div class="col-4 text-center">
          <b-button @click="registrarGlosa();" variant="success" size="sm"><i class="fas fa-save fa-fw ml-2"></i>REGISTRAR</b-button>
        </div>
      </div>
    </b-modal>

    <b-modal v-model="modal_confirmar_cancelar" size="md" title="CANCELAR" hide-footer no-close-on-backdrop :scrollable="false">
      <div class="row">
        <div class="col-12 text-center">
          <h5>¿Confirma que desea cancelar la creación de la nueva valorización?</h5>
        </div>
      </div>
      <div class="row mt-3 pt-3 border-top justify-content-center">
        <div class="col-4 text-center">
          <b-button @click="modal_confirmar_cancelar=false; nueva_glosa='';" variant="danger" size="sm"><i class="fas fa-ban fa-fw mr-2"></i>CANCELAR</b-button>
        </div>
        <div class="col-4 text-center">
          <b-button @click="cerrarNuevaValoricacion();" variant="success" size="sm"><i class="fas fa-check-circle fa-fw mr-2"></i>CONFIRMAR</b-button>
        </div>
      </div>
    </b-modal>

    <b-modal v-model="modal_confirmar_finalizar" size="md" title="FINALIZAR" hide-footer no-close-on-backdrop :scrollable="false">
      <div class="row">
        <div class="col-12 text-center">
          <h5>¿Confirma que desea finalizar la valorización?</h5>
        </div>
      </div>
      <div class="row mt-3 pt-3 border-top justify-content-center">
        <div class="col-4 text-center">
          <b-button @click="modal_confirmar_finalizar=false; nueva_glosa='';" variant="danger" size="sm"><i class="fas fa-ban fa-fw mr-2"></i>CANCELAR</b-button>
        </div>
        <div class="col-4 text-center">
          <b-button @click="funcion_verificar='finalizarValorizacion'; modal_verificar_huella=true;" variant="success" size="sm"><i class="fas fa-check-circle fa-fw mr-2"></i>FINALIZAR</b-button>
        </div>
      </div>
    </b-modal>

    <b-modal v-model="modal_msj" size="lg" title="REGISTRO VALORIZACIÓN" hide-footer no-close-on-backdrop :scrollable="false">
      <div class="row">
        <div class="col-12">
          <h6 class="text-center"><strong>{{msj_registro}}</strong></h6>
        </div>
      </div>
      <div class="row mt-3 pt-3 border-top">
        <div class="col text-center">
          <b-button @click="cerrarNuevaValoricacion();" variant="primary" size="sm">ACEPTAR</b-button>
        </div>
      </div>
    </b-modal>

  </div>
</template>

<script>
export default {
  props: ['orden_compra_p'],
  data(){
    return{
      cargando_pagina         : false,
      modal_verificar_huella  : false,
      funcion_verificar       : '',

      modal_msj               : false,
      msj_registro            : '',
      fecha_actual            : '',

      columnas_valorizacion    : [
        {key: 'glosa'        , label: 'Glosa'           , sortable: false  , tdClass:'p-1', thClass:'p-1'},
        {key: 'observacion'  , label: 'Observacion'     , sortable: false  , tdClass:'p-1', thClass:'p-1'},
        {key: 'monto'        , label: 'Monto'           , sortable: false  , tdClass:'p-1', thClass:'p-1'},
        {key: 'opcion'       , label: '-'               , sortable: false  , tdClass:'p-1', thClass:'p-1'}
      ],
      valorizacion          : {
        factura                 : '', 
        din                     : '',
        rut_carpeta             : '',
        comision_banco          : '',
        costo_importacion       : '',   // moneda extranjera
        costo_pagado            : '',   // pesos
        total_gastos            : '',   // se calcula: la suma de todos los montos de los items
        tipo_cambio             : '',   // se calcula: costo_pagado / costo_importacion
        porcentaje_costos       : '',   // se calcula: (total_gastos + comision_banco) / costo_pagado
        tipo_cambio_reajustado  : '',   // se calcula: tipo_cambio + (tipo_cambio * porcentaje_costos)
        items                   : [{glosa:'', monto:'', observacion:''}]
      },
      modal_confirmar_cancelar  : false,
      modal_confirmar_finalizar : false,
      modal_nueva_glosa         : false,
      nueva_glosa               : '',
      glosas_valorizacion       : [],


      columnas_articulos    : [
        {key: 'codigo'        , label: 'Codigo'                       , sortable: false  , tdClass:'p-1', thClass:'p-1'},
        {key: 'nomart'        , label: 'Articulo'                     , sortable: false  , tdClass:'p-1', thClass:'p-1'},
        {key: 'umed'          , label: 'U. Medida'                    , sortable: false  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'cant'          , label: 'Cantidad'                     , sortable: false  , tdClass:'p-1 text-center', thClass:'p-1'},
        //{key: 'pcompra'       , label: 'Precio'               , sortable: false  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'valorextra'    , label: 'Valor Unitario M. Extranjera' , sortable: false  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'totalextra'    , label: 'Total M. Extranjera'          , sortable: false  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'totalpesos'    , label: 'Total Pesos'                  , sortable: false  , tdClass:'p-1 text-center', thClass:'p-1'},
        {key: 'valorpesos'    , label: 'Valor Unitario Pesos'         , sortable: false  , tdClass:'p-1 text-center', thClass:'p-1'}
      ],

      orden_compra          : {},
      muestra_alerta        : false,
      msj_alerta            : '',
      formato_numero        : /^[0-9]+([.][0-9]*)?$/,
      
    }
  },
  mounted(){
    this.orden_compra = JSON.parse(JSON.stringify(this.orden_compra_p));
    this.fecha_actual = new Date();
    this.filtro_fecha_hasta = new Date();
    this.filtro_fecha_desde = new Date(this.filtro_fecha_hasta.getTime() - (60*(24*30)*60000));
    this.filtro_fecha_desde = this.filtro_fecha_desde.getFullYear()+'-'+(this.filtro_fecha_desde.getMonth()+1)+'-'+this.filtro_fecha_desde.getDate();
    this.filtro_fecha_hasta = this.filtro_fecha_hasta.getFullYear()+'-'+(this.filtro_fecha_hasta.getMonth()+1)+'-'+this.filtro_fecha_hasta.getDate();

    this.listarGlosas();
  },
  methods:{
    listarOrdenCompra(){
      if(this.buscar_oc == ''){
        return;
      }
      this.cargando_pagina = true;
      const datos = new URLSearchParams();
      datos.append('orden_compra', this.buscar_oc);
      axios.post(base_url+'valorizacion/listarOrdenCompra', datos).then( resp => {
        if(resp.data.status == 200){
          if(resp.data.data.Cod == 200){
            this.orden_compra.oc        = resp.data.data.ocompra;
            this.orden_compra.bodega    = resp.data.data.bod;
            this.orden_compra.fecha     = resp.data.data.fecha;
            this.orden_compra.glosa     = resp.data.data.glosa;
            this.orden_compra.mensaje   = resp.data.data.mensaje;
            this.orden_compra.articulos = resp.data.data.codigos;
            this.orden_compra.articulos.forEach(articulo => {
              articulo.valorextra = 0;
              articulo.totalextra = 0;
              articulo.totalpesos = 0;
              articulo.valorpesos = 0;
            });
            console.log(this.orden_compra);
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

    agregarItem(){
      this.valorizacion.items.push({glosa:'', monto:'', observacion:''});
    },

    eliminarItem(index){
      this.valorizacion.items.splice(index, 1);
    },

    totalCostos(){
      let total         = 0;
      let numeroLimpio  = '';
      this.valorizacion.items.forEach(item => {
        if(typeof parseFloat(item.monto) === "number" && item.monto != ''){
          numeroLimpio  = item.monto.replace(/\./g, '').replace(/,/g, '.'); 
          total        += parseFloat(numeroLimpio);
        }
      });
      this.valorizacion.total_gastos = total;
      return total;
    },

    porcentajeCostos(){
      let total          = 0;
      let comision_banco = 0;
      let costo_pagado   = 0;
      let numeroLimpio   = '';

      total  = this.totalCostos();

      if(typeof parseFloat(this.valorizacion.comision_banco) === "number" && this.valorizacion.comision_banco != ''){
        numeroLimpio    = this.valorizacion.comision_banco.replace(/\./g, '').replace(/,/g, '.'); 
        comision_banco  = parseFloat(numeroLimpio);
      }
      if(typeof parseFloat(this.valorizacion.costo_pagado) === "number" && this.valorizacion.costo_pagado != ''){
        numeroLimpio  = this.valorizacion.costo_pagado.replace(/\./g, '').replace(/,/g, '.'); 
        costo_pagado  = parseFloat(numeroLimpio);
      }
      if(total == 0 || comision_banco == 0 || costo_pagado == 0){
        return 0;
      }
      let porcentaje = (total + comision_banco) / costo_pagado;
      this.valorizacion.porcentaje_costos = porcentaje;
      return porcentaje;
    },

    tipoCambio(){
      let tipo_cambio   = 0;
      let numeroLimpio  = '';
      let costo_importacion = 0;
      let costo_pagado = 0;
      if( typeof parseFloat(this.valorizacion.costo_pagado) === "number" && this.valorizacion.costo_pagado != '' &&
          typeof parseFloat(this.valorizacion.costo_importacion) === "number" && this.valorizacion.costo_importacion  != '' )
        {
        numeroLimpio       = this.valorizacion.costo_pagado.replace(/\./g, '').replace(/,/g, '.');
        costo_pagado       = parseFloat(numeroLimpio);
        numeroLimpio       = this.valorizacion.costo_importacion.replace(/\./g, '').replace(/,/g, '.');
        costo_importacion  = parseFloat(numeroLimpio);
        tipo_cambio        = (costo_pagado / costo_importacion );
        this.valorizacion.tipo_cambio = tipo_cambio;
        return tipo_cambio;
      }
      else{
        return 0;
      }
      
    },

    tipoCambioReajustado(){
      let tipo_cambio   = 0;
      if(typeof this.valorizacion.tipo_cambio === "number" && this.valorizacion.tipo_cambio != ''){
        tipo_cambio   = parseFloat(this.valorizacion.tipo_cambio);
      }
      let TC = tipo_cambio + (tipo_cambio * this.porcentajeCostos() )
      this.valorizacion.tipo_cambio_reajustado = TC;
      return TC;
    },

    reajustaValoresOC(){
      let valorextra    = 0;
      let numeroLimpio  = '';
      let articulos     = []
      if(this.tipoCambioReajustado() != 0){
        articulos = JSON.parse(JSON.stringify(this.orden_compra.articulos));
        articulos.forEach(articulo => {
          if(articulo.valorextra != 0 && articulo.valorextra != null && articulo.valorextra != ''){
            numeroLimpio        = articulo.valorextra.replace(/\./g, '').replace(/,/g, '.');
            valorextra          = parseFloat(numeroLimpio);
            articulo.totalextra = parseFloat(parseFloat(valorextra * articulo.cant).toFixed(2));
            articulo.totalpesos = parseFloat(parseFloat(articulo.totalextra * this.tipoCambioReajustado()).toFixed(2));
            articulo.valorpesos = parseFloat(parseFloat(articulo.totalpesos / articulo.cant).toFixed(2));
          }
          else {
            articulo.valorextra = 0;
            articulo.totalextra = 0;
            articulo.totalpesos = 0;
            articulo.valorpesos = 0;
          }
        });
        this.orden_compra.articulos = articulos;
      }
    },

    registrarGlosa(){
      if(this.nueva_glosa == ''){
        alert('Debe ingresar una glosa.');
        return;
      }
      this.cargando_pagina = true;
      const datos = new URLSearchParams();
      datos.append('nueva_glosa', this.nueva_glosa);
      axios.post(base_url+'valorizacion/registrarGlosa', datos).then( resp => {
        if(resp.data.key == 1){
          this.listarGlosas();
          this.modal_nueva_glosa = false;
          this.nueva_glosa = '';
        }
        else{
          alert('Error al registrar la glosa: '+resp.data.msj);
        }
        this.cargando_pagina      = false;
      }).catch(error =>{
        alert('Error al registrar la glosa: '+error);
        this.cargando_pagina = false;
      });
    },

    listarGlosas(){
      this.cargando_pagina = true;
      axios.get(base_url+'valorizacion/listarGlosas').then( resp => {
        if(resp.data.key == 1){
          this.glosas_valorizacion = resp.data.glosas;
        }
        else{
          alert('Falla al listar las glosas.');
        }
        this.cargando_pagina      = false;
      }).catch(error =>{
        alert('Falla al listar las glosas: '+error);
        this.cargando_pagina = false;
      });
    },

    registrarValorizacion(finalizar = false){
      this.cargando_pagina = true;
      const datos = new URLSearchParams();
      datos.append('valorizacion', JSON.stringify(this.valorizacion));
      datos.append('orden_compra', JSON.stringify(this.orden_compra));
      datos.append('finalizar', JSON.stringify(finalizar));
      axios.post(base_url+'valorizacion/registrarValorizacion', datos).then( resp => {
        if(resp.data.key == 1){
          this.msj_registro = 'Valorización guardada correctamente.';
          this.modal_msj = true;
        }
        else{
          alert('Error al guardar la valorización: '+resp.data.msj);
        }
        this.cargando_pagina      = false;
      }).catch(error =>{
        alert('Error al guardar la valorización: '+error);
        this.cargando_pagina = false;
      });
    },


    formatearMiles(valor) {
      valor = valor.toString();
      let numeroLimpio  = valor.replace(/\./g, '').replace(/,/g, '.'); 
      const numero      = parseFloat(numeroLimpio);
      if (!isNaN(numero)) {
        return numero.toLocaleString("de-DE");
      }
      else {
        return 0;
      }
      //
    },

    verificacionCorrecta(funcion_ejecucion){
      this.modal_verificar_huella = false;
      if(funcion_ejecucion == 'registrarValorizacion'){
        this.registrarValorizacion();
      }
      else if(funcion_ejecucion == 'finalizarValorizacion'){
        this.registrarValorizacion(true);
      }
    },

    cerrarNuevaValoricacion(){
      this.$emit('cerrar');
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