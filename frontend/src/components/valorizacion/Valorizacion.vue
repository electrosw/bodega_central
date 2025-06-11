<template>
  <div class="container-fluid">
    <loader-component :cargando_pagina="cargando_pagina"/>
    <div class="row">
      <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 text-md-left text-center">
        <h3><i class="fas fa-fw fa-dollar-sign mr-2"></i>VALORIZACIÓN</h3>
      </div>
    </div>

    <b-card v-if="!muestra_nueva_valorizacion && !muestra_ver_valorizacion"  class="mt-4">
      <h5 class="mb-3">Nueva valorización</h5>
      <h6>Ingresa orden de compra:</h6>
      <div class="row justify-content-center">
        <div class="col-xl-10 col-md-8 col-12">
          <b-form-input v-model="buscar_oc" @keyup.enter="listarOrdenCompra();" class="mb-3" size="sm"></b-form-input>
        </div>
        <div class="col-xl-2 col-md-4 col-12 text-center">
          <b-button :disabled="buscar_oc == ''" @click="listarOrdenCompra();" variant="primary" size="sm" block><i class="fas fa-fw fa-search mr-2"></i>BUSCAR</b-button>
        </div>
      </div>
    </b-card>

    <nueva-valorizacion-component 
      v-if="muestra_nueva_valorizacion" 
      :orden_compra_p="orden_compra" 
      @cerrar="muestra_nueva_valorizacion=false;">
    </nueva-valorizacion-component>

    <ver-valorizacion-component 
      v-if="muestra_ver_valorizacion" 
      :val_id_p="val_id_p" 
      @cerrar="muestra_ver_valorizacion=false;">
    </ver-valorizacion-component>

    <lista-valorizaciones-component 
      v-if="muestra_lista_valorizaciones && !muestra_nueva_valorizacion && !muestra_ver_valorizacion" 
      @ver_val="muestra_ver_valorizacion = true; val_id_p = $event;">
    </lista-valorizaciones-component>
    

  </div>
</template>

<script>
export default {
  data(){
    return{
      cargando_pagina               : false,
      muestra_nueva_valorizacion    : false,
      muestra_lista_valorizaciones  : true,
      muestra_ver_valorizacion      : false,
      val_id_p                      : 0,

      orden_compra         : {
              bodega                  :'', 
              fecha                   :'', 
              glosa                   :'', 
              mensaje                 :'', 
              oc                      :'', 
              articulos               :[], 
            },
      buscar_oc                     : '1178904',
    }
  },
  mounted(){
    
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
        if(resp.data.key == 1){
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
              this.muestra_nueva_valorizacion = true;
            }
            else{
              alert('Orden de compra no encontrada.');
            }
          }
          else {
            alert('Falló la conexion con CGI interno.');
          }
        }
        else {
          alert(resp.data.msj);
        }
        this.cargando_pagina      = false;
      }).catch(error =>{
        this.cargando_pagina = false;
      });
    }
    
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