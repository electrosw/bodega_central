
window.base_url = '/rhumanos/index.php/';

var app = new Vue({
  el: '#app',
  data: {
    ano_actual  : '',
    rut_persona : '',
    huella1     : '',
    huella2     : '',
    tiempo_conteo : 4,
    cuenta_atras : 0,
    cargando_pagina :false,
    modal_verificacion:false,
    modal_detalles:false,
    modal_detalles_semana:false,
    modal_confirmar_datos:false,
    estado_verificacion:0,
    alert_msj:'',
    alert_tipo:'',
    columnas_detalle: [
      { key: 'semana',           label: 'SEMANA',   sortable: true,   tdClass:'p-1', thClass:'p-1' },
      { key: 'tiempo_trabajado', label: 'HORAS T.', sortable: true,   tdClass:'p-1', thClass:'p-1' },
      { key: 'tiempo_extra',     label: 'HORAS E.', sortable: true,   tdClass:'p-1', thClass:'p-1' },
      //{ key: 'cant_permisos',    label: 'N° PERMISOS', sortable: true,   tdClass:'p-1', thClass:'p-1'},
      //{ key: 'cant_atrasos',     label: 'N° ATRASOS',  sortable: true,   tdClass:'p-1', thClass:'p-1'},
      { key: 'opciones',         label: 'OPCIONES',                   tdClass:'p-1', thClass:'p-1'}
    ],
    columnas_detalle_semana:[
        { key: 'fecha',            label: 'FECHA',     sortable: true,   tdClass:'p-1', thClass:'p-1' },
        { key: 'entrada',          label: 'ENTRADA',   sortable: true,   tdClass:'p-1', thClass:'p-1' },
        { key: 'salida_colacion',  label: 'SAL. COL.', sortable: true,   tdClass:'p-1', thClass:'p-1' },
        { key: 'entrada_colacion', label: 'ENT. COL.', sortable: true,   tdClass:'p-1', thClass:'p-1' },
        { key: 'salida',           label: 'SALIDA',    sortable: true,   tdClass:'p-1', thClass:'p-1' }
    ],
    informacion_detalle_semanal:{
      fecha:'',
      tiempo_t:'',
      tiempo_e:''
    },
    detalle_persona:[],
    detalle_semana_persona:[],
  },
  mounted: function () {
    this.ano_actual=new Date().getFullYear();
  },

  methods: {

    cancelarDP(){
      axios.post('http://127.0.0.1/biometria/cancelar/cancelar.php').then( resp => {
        if(resp.status === 200){
          console.log("lector apagado y archivos reiniciados");
        }
      }).catch(error =>{
        console.log("ERROR"+error);
      }); 
    },

    buscarHuellas(){

      if(this.verificaRut(this.rut_persona)){
        let tiempo_espera = 10000; //TIEMPO EN QUE EL LECTOR ESPERA (EN MILISEGUNDOS)
        let tiempo_inicio = new Date().getTime();
        let tiempo_fin = new Date().getTime();
        var interval_tiempo_limite = setInterval( () => {
          if((tiempo_fin - tiempo_inicio)>tiempo_espera){
            console.log("pasaron los segundos");
            this.cancelarDP();
            this.rut_persona = '';
            this.cargando_pagina = false;
            this.modal_verificacion = false;
            $("#rut_persona").prop("disabled",true);
            setTimeout(() =>$("#rut_persona").prop("disabled",false), 1000);              
            clearInterval(interval_tiempo_limite);
          }
          else{
            tiempo_fin = new Date().getTime();
            $.ajax({
              url: "http://127.0.0.1/biometria/verificar/estado.php",
            }).done( resp => {
              if(resp.trim() == '204' || resp.trim() == 204){
                //console.log("204");
                this.modal_verificacion = true;
              }
            }).fail( function( jqXHR, textStatus, errorThrown ) {
              this.modal_verificacion = false;
              this.alert_msj = "EXISTEN PROBLEMAS DE CONEXIÓN. INTENTE NUEVAMENTE.";
              this.alert_tipo = "danger";
              this.muestraAlert();
              this.rut_persona = '';
              this.cargando_pagina = false;
            })
          }
        },300);

        
        let data  = new URLSearchParams();
        data.append('rut', this.rut_persona);
        axios.post(base_url+'reporte/listarHuellasPorRut', data).then( resp => {
          if(resp.status === 200){
            let datos = resp.data;
            this.cargando_pagina = false;
            if(datos.respuesta == 1){
              if((datos.huella1!=null && datos.huella1!='') || (datos.huella2!=null && datos.huella2!='')){
                let huellas  = new URLSearchParams();
                huellas.append('huella1', datos.huella1);
                huellas.append('huella2', datos.huella2);
                axios.post('http://127.0.0.1/biometria/verificar/verificar.php', huellas).then( resp => {
                  if(resp.status === 200){
                    clearInterval(interval_tiempo_limite);
                    switch (resp.data) {
                      case 201:
                        console.log("huella correcta");
                        this.estado_verificacion = 1;
                        this.buscarReporteMensual()                      
                        break;
                      case 202:
                        console.log("error de sistema");
                        this.alert_msj = "EXISTE UN ERROR EN EL SISTEMA. VUELVA A INTENTARLO MÁS TARDE.";
                        this.alert_tipo = "danger";
                        this.muestraAlert();
                        break;
                      case 203:
                        console.log("huella incorrecta");
                        this.rut_persona = '';
                        this.estado_verificacion = 2;     
                        setTimeout(() =>this.modal_verificacion = false, 3000);              
                        /*this.alert_msj = "LA HUELLA INGRESADA NO COINCIDE CON NUESTROS REGISTROS.";
                        this.alert_tipo = "warning";
                        this.muestraAlert();*/
                        break;
                    }
                  //this.cargando_pagina = false;
                  }
                }).catch(error =>{
                  //console.log("ERROR");
                  console.log(error);
                }); 
              }
              else{
                this.alert_msj = "USTED NO CUENTA CON HUELLAS REGISTRADAS.";
                this.alert_tipo = "warning";
                this.muestraAlert();
                this.rut_persona = '';
                //this.cargando_pagina = false;
              }
            }
            else{
              clearInterval(interval_tiempo_limite);
              this.alert_msj = "EL RUT INGRESADO NO SE ENCUENTRA EN NUESTROS REGISTROS.";
              this.alert_tipo = "warning";
              this.muestraAlert();
              this.rut_persona = '';
              //this.cargando_pagina = false;
            }
          }
        }).catch(error =>{
          this.alert_msj = "EXISTEN PROBLEMAS DE CONEXIÓN. INTENTE NUEVAMENTE.";
          this.alert_tipo = "danger";
          this.muestraAlert();
          this.rut_persona = '';
          //this.cargando_pagina = false;
        }); 
      }
    },

    buscarReporteMensual(){
      let data  = new URLSearchParams();
      data.append('rut', this.rut_persona);
      axios.post(base_url+'reporte/generarReporte', data).then( resp => {
        if(resp.status === 200){
          //console.log(resp.data);
          respuesta = resp.data;
          if(respuesta.resp == 1){
            this.modal_verificacion = false;
            this.detalle_persona = resp.data.detalle;
            this.modal_detalles = true;
          }
          else if(respuesta.resp == 2){
            this.alert_msj = "EL REPORTE YA SE ENCUENTRA REGISTRADO EN NUESTROS SISTEMAS.";
            this.alert_tipo = "warning";
            this.muestraAlert();
            this.rut_persona = '';
            this.cargando_pagina = false;
            this.modal_verificacion = false;    
          }
        }
      }).catch(error =>{
        this.alert_msj = "EXISTEN PROBLEMAS DE CONEXIÓN. INTENTE NUEVAMENTE.";
        this.alert_tipo = "danger";
        this.muestraAlert();
        this.rut_persona = '';
        this.cargando_pagina = false;
        this.modal_verificacion = false;
      }); 
    },

    confirmarReporte(){
      this.modal_confirmar_datos = false;
      this.modal_detalles_semana = false;
      this.modal_detalles = false;
      this.cargando_pagina = true;

      var data = new URLSearchParams();
      data.append('rut', this.rut_persona);
//      axios.post(base_url+"Asistencias/generarPDFAsistenciaPersonaRango", data,{responseType: 'blob'}).then((resp) => {
      axios.post(base_url+"reporte/generarPDFAsistenciaPersonaRango", data).then((resp) => {
        if(resp.status === 200){
          let respuesta = parseInt(resp.data);
          if(respuesta == 0){
            this.alert_msj = "EXISTEN PROBLEMAS INTERNOS. INTENTE NUEVAMENTE.";
            this.alert_tipo = "danger";
          }
          else if(respuesta == 1){
            this.alert_msj = "EL REPORTE FUE ENVIADO AL CORREO REGISTRADO EN EL SISTEMA.";
            this.alert_tipo = "success";
          }
          else if(respuesta == 2){
            this.alert_msj = "NO TIENE CORREO PARA ENVIAR EL REPORTE. COMUNÍQUESE CON EL ÁREA DE RRHH.";
            this.alert_tipo = "danger";
          }
          this.muestraAlert();
        }
        this.cargando_pagina = false;
      }).catch(error =>{
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
        if(!rut.match(/(KK|0000000|1111111|2222222|3333333|4444444|5555555|6666666|7777777|8888888|9999999)/g)){
            let m=0,s=1;
            for(;rut;rut=Math.floor(rut/10))
              s=(s+rut%10*(9-m++%6))%11;
            let dv_calc= (s?s-1:'K').toString();
            if(dv_calc===dv) resp=true;
         }
        }
      }
      return resp;
    },

    cuentaAtras(cuenta_atras) {
      this.cuenta_atras = cuenta_atras
    },
    
    muestraAlert() {
      this.cuenta_atras = this.tiempo_conteo
    }
  }
});
