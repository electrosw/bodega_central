export default {
  
    install (Vue, options) {
  
      Vue.yourMethod = (value) => value
      Vue.mixin({
          created() { }, 
          methods :{
            muestraToast(titulo, mensaje, variante = null, posicion = 'b-toaster-bottom-right') {
              this.$bvToast.toast(mensaje, {
              title: titulo,
              variant: variante,
              toaster: posicion,
              autoHideDelay: 5000,
              solid: true
              })
            },

            cerrarSesion(){
              axios.post(base_url+'Login/cerrarSesion').then( resp => {
                if(resp.status === 200 && 'resp' in resp.data){
                  if(resp.data.resp.key == 1){
                    location.reload();
                  }else{
                    location.reload();
                  }
                }
                else{
                  location.reload();
                }
              }).catch(error =>{
                location.reload();
              });
            },

            validaSesion(){
              axios.post(base_url+'login/validaSesion').then( resp => {
                if(resp.status === 200 && 'resp' in resp.data){
                  var mensaje = resp.data.resp;
                  if(mensaje.key != 1){
                    this.cerrarSesion();
                  }
                }
              });
            },

            formateaFecha(fecha){
              return fecha.replace(/^(\d{4})-(\d{2})-(\d{2})$/g,'$3-$2-$1');
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
            }
          }
       })
       // Texto para utilizar en cualquier componente
       Vue.prototype.$myProperty = 'This is a Vue instance property.'
    }
  }