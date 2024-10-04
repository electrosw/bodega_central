
//window.sha1 = require("./libraries/locutus/php/strings/sha1.js");

var app = new Vue({
  el: '#app',
  data: {
    fondo:            1,
    usuario:          '',
    clave:            '',
    rut_valido:       false,
    cargando_inicio:  false, 
    clave_visible:    false
  },
  mounted: function () {
    $('#in_rut').focus();
  },

  methods: {
    iniciarSesion(){
      this.cargando_inicio = true;
      if(!this.verificaRut(this.usuario) || this.clave == null || this.clave == ''){
        this.muestraToast('ATENCIÓN!', 'DATOS INCORRECTOS 1!', 'danger');
        this.cargando_inicio = false;
        return;
      }
      var rut = this.usuario.replace(/\./g,'').replace(/[^k|K|\d]/g, '').replace(/\-/g, '').trim().toUpperCase();
      var rut_usuario = rut.substr(0, rut.length-1);
      var dato  = new URLSearchParams();
      dato.append('usuario', rut_usuario);
      dato.append('clave', CryptoJS.SHA1(CryptoJS.MD5(rut_usuario.toString()+this.clave.toString()).toString()).toString());
      axios.post('index.php/login/iniciarSesion', dato).then( resp => {
        if(resp.status === 200 && 'resp' in resp.data){
          if(resp.data.resp.key == 0){
            this.cargando_inicio = false;
            this.muestraToast('ATENCIÓN!', 'DATOS INCORRECTOS!', 'danger');
          }
          else if(resp.data.resp.key == 1){
            location.reload();
          }
        }
        else{
          this.cargando_inicio = false;
          this.muestraToast('ERROR', 'ERROR AL INICIAR, INTENTALO MAS TARDE.', 'danger');
        }
      }).catch(error =>{
        this.cargando_inicio = false;
        this.muestraToast('ERROR', 'ERROR AL CONECTARSE CON EL SERVIDOR.', 'danger');
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

    muestraToast(titulo, mensaje, variante = null, posicion = 'b-toaster-bottom-right') {
      this.$bvToast.toast(mensaje, {
      title: titulo,
      variant: variante,
      toaster: posicion,
      autoHideDelay: 5000,
      solid: true
      })
    },
  
    muestraClave(){
      this.clave_visible = !this.clave_visible;
      if(this.clave_visible){
        $('#in_clave').get(0).type = 'text';
      }
      else{
        $('#in_clave').get(0).type = 'password';
      }
    },

    marcaFoco(id){ 
      $('#'+id).addClass('border-primary');
    },

    desmarcaFoco(id){
      $('#'+id).removeClass('border-primary');
    }
  }
});
